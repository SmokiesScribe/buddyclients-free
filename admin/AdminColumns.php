<?php
namespace BuddyClients\Admin;

use DateTimeZone;
use DateTime;

/**
 * Generates the admin columns for post types.
 *
 * @since 0.1.0
 */
class AdminColumns {
    
    /**
     * Cache for instances.
     * 
     * Stores instances of the class by post type.
     * 
     * @var array
     */
    private static $instances = [];
	
    /**
     * Post type.
     * 
     * The post type to which the admin columns belong.
     * 
     * @var string
     */
    public $post_type;
    
    /**
     * Columns.
     * 
     * The array of columns to add.
     * 
     * @var array
     */
    protected $columns;
    
    /**
     * Defines columns for all post types.
     * 
     * @since 0.1.0
     */
    private static function columns_data( $post_type ) {
        $columns = [
            'bc_brief' => [
                'project_id'                =>  __( 'Project', 'buddyclients-free' ),
                'updated_date'              =>  __( 'Submitted', 'buddyclients-free' ),
            ],
            'bc_brief_field' => [
                'brief_types'               =>  __( 'Brief Types', 'buddyclients-free' ),
                'field_type'                =>  __( 'Field Type', 'buddyclients-free' ),
            ],
            'bc_service' => [
                'valid'                     =>  __( 'Valid', 'buddyclients-free' ),
                'visible'                   =>  __( 'Visibility', 'buddyclients-free' ),
                'rate_value'                =>  __( 'Client Rate', 'buddyclients-free' ),
                'team_member_percentage'    =>  __( 'Team Member %', 'buddyclients-free' ),
            ],
            'bc_adjustment' => [
                'form_field_type'           =>  __( 'Field Type', 'buddyclients-free' ),
            ],
            'bc_service_type' => [
                'visible'                   =>  __( 'Visibility', 'buddyclients-free' ),
                'form_field_type'           =>  __( 'Field Type', 'buddyclients-free' ),
            ],
            'bc_filter' => [
                'xprofile_field'            =>  __( 'Field', 'buddyclients-free' ),
                'xprofile_field_type'       =>  __( 'Field Type', 'buddyclients-free' ),
            ],
            'bc_quote' => [
                'client_id'                 =>  __( 'Client', 'buddyclients-free' ),
                'valid'                     =>  __( 'Valid', 'buddyclients-free' ),
                'visible'                   =>  __( 'Visibility', 'buddyclients-free' ),
                'rate_value'                =>  __( 'Client Rate', 'buddyclients-free' ),
                'team_member_percentage'    =>  __( 'Team Member %', 'buddyclients-free' ),
            ],
            'bc_legal_mod' => [
                'user_id'                   =>  __( 'User', 'buddyclients-free' ),
                'legal_type'                =>  __( 'Legal Type', 'buddyclients-free' ),
            ],
            'bc_file_upload' => [
                'file_types'                =>  __( 'File Types', 'buddyclients-free' )
            ],
        ];
        
        /**
         * Filters the admin columns.
         *
         * @since 0.3.4
         *
         * @param array  $columns An array of admin columns info.
         */
         $columns = apply_filters( 'bc_admin_columns', $columns );
        
        return $columns[$post_type] ?? [];
    }
    
    /**
     * Defines columns to make sortable.
     * 
     * @since 0.3.3
     * 
     * @param   string  $post_type  The post type.
     */
    public function sortable_columns() {
        $sortable_columns = [
            'bc_brief_field' => [
                'brief_types'               =>  __( 'Brief Types', 'buddyclients-free' ),
            ],
            'bc_service' => [
                'valid'                     =>  __( 'Valid', 'buddyclients-free' ),
            ],
            'bc_service_type' => [
                'visible'                   =>  __( 'Visibility', 'buddyclients-free' ),
            ],
            'bc_quote' => [
                'client_id'                 =>  __( 'Client', 'buddyclients-free' ),
                'valid'                     =>  __( 'Valid', 'buddyclients-free' ),
            ]
        ];
        
        /**
         * Filters the sortable admin columns.
         *
         * @since 0.3.4
         *
         * @param array  $columns An array of sortable admin columns.
         */
         $sortable_columns = apply_filters( 'bc_sortable_admin_columns', $sortable_columns );
         
        return $sortable_columns[$this->post_type] ?? [];
    }
    
    /**
     * Static method to get or create an instance.
     * 
     * @since 0.1.0
     * 
     * @param string $post_type The post type for which to get the instance.
     * 
     * @return self
     */
    public static function get_instance( $post_type ) {
        if ( ! isset( self::$instances[$post_type] ) ) {
            self::$instances[$post_type] = new self( $post_type );
        }
        return self::$instances[$post_type];
    }
    
    /**
     * Constructor
     * 
     * @since 0.1.0
     */
    public function __construct( $post_type ) {
        $this->post_type = $post_type;
        $this->columns = self::columns_data( $this->post_type );
        
        if ( $this->columns ) {
            // Define hooks
            $this->define_hooks();
            
            // Make columns sortable
            $this->make_sortable();
        }
    }
    
    /**
     * Retrieves the columns for the post type.
     * 
     * @since 0.1.0
     * @updated 0.2.5
     */
    public function columns( $columns ) {
        foreach ( $this->columns as $key => $value ) {
            if ( ! isset( $columns[$key] ) ) {
                $columns[$key] = $value;
            }
        }
        
        // Remove the date column
        if ( isset( $columns['date'] ) ) {
            unset( $columns['date'] );
        }
        
        $columns = $this->filter_columns( $columns );
        return $columns;
    }
    
    /**
     * Makes columns sortable.
     * 
     * @since 0.3.3
     */
    private function make_sortable() {
        
        // Get sortable columns
        $this->sortable_columns = self::sortable_columns();
        
        // Add filter
        add_filter( 'manage_edit-' . $this->post_type . '_sortable_columns', [$this, 'sortable_columns'], 10, 1 );
    }
    
    /**
     * Filters columns.
     * 
     * @since 0.1.0
     * 
     * @param   array   $columns    Columns to filter.
     */
    private function filter_columns( $columns ) {
        // Initialize
        $filtered_columns = [];
        
        // Loop through colums
        foreach ( $columns as $column_key => $column_title ) {
            
            // Check for Freelancer Mode
            if ( bc_freelancer_mode() ) {
                if ( in_array( $column_key, $this->freelancer_exclusions() ) ) {
                    continue;
                }
            }
            
            // Add back to array
            $filtered_columns[$column_key] = $column_title;
        }
        return $filtered_columns;
    }
    
    /**
     * Defines Freelancer Mode exclusions.
     * 
     * @since 0.1.0
     */
    private function freelancer_exclusions() {
        return [
            'team_member_percentage'
        ];
    }
    
    /**
     * Defines hooks and filters.
     *
     * @since 1.0.0
     */
    private function define_hooks() {
        add_filter( 'manage_edit-' . $this->post_type . '_columns', [$this, 'columns'], 10, 1 );
        add_action( 'manage_' . $this->post_type . '_posts_custom_column', [$this, 'display_column'], 10, 2 );
    }
    
    /**
     * Displays custom column.
     * 
     * @since 0.1.0
     */
    public function display_column( $column, $post_id ) {

        // Initialize output
        $output = '';
        
        // Get meta value
        $value = get_post_meta($post_id, $column, true);

        switch ( $column ) {
            case 'client_id':
            case 'speakers':
            case 'user_id':
            case 'sponsor_id':
                $output = $this->user_column( $value );
                break;
            case 'updated_date':
                $output = $this->submitted_column( $value );
                break;
            case 'valid':
                $output = $this->valid_column( $value );
                break;
            case 'rate_value':
                $rate_type = get_post_meta($post_id, 'rate_type', true);
                $output = $this->rate_value_column( $value, $rate_type );
                break;
            case 'team_member_percentage':
                $output = $this->percentage_column( $value );
                break;
            case 'visible':
                $output = $this->visibility_column( $value );
                break;
            case 'project_id':
                $output = $this->project_column( $value );
                break;
            case 'xprofile_field';
                $output = $this->xprofile_column( $value );
                break;
            case 'xprofile_field_type';
                $value = get_post_meta($post_id, 'xprofile_field', true);
                $output = $this->xprofile_type_column( $value );
                break;
            case 'brief_types';
                $output = $this->term_names( $value );
                break;
            case 'location':
            case 'event_year':
                $output = $this->post_title( $value );
                break;
            case 'start_timestamp':
            case 'end_timestamp':
                $output = $this->timestamp( $value );
                break;
            case 'start_date':
            case 'end_date':
                $output = $this->date( $value );
                break;
            case 'start_time':
            case 'end_time':
                $output = $this->time( $value );
                break;
            case 'event_block_time':
                $event_block_id = get_post_meta($post_id, 'event_block', true);
                $output = $this->event_block_time( $event_block_id );
                break;
            case 'business_url':
            case 'business_tagline':
                $output = $value;
                break;
            case 'sponsor_option_ids':
                $output = $this->sponsorship_options( $value, $post_id );
                break;
            default:
                $output = $this->default_display( $value );
                break;
        }
            
        echo wp_kses_post( $output );
    }
    
    /**
     * Outputs the meta value directly.
     * 
     * @since 0.3.2
     */
    private function default_display( $value ) {
        return is_array( $value ) ? implode( ', ', $value ) : ucwords( str_replace( '_', ' ', $value ) );
    }
    
    /**
     * Displays array of taxonomy term names.
     * 
     * @since 0.3.2
     * 
     * @param   array   $value  An array of taxonomy term IDs.
     */
    private function term_names( $value ) {
        $term_names = [];
        foreach ( $value as $term_id ) {
            $term_names[] = get_term( $term_id )->name;
        }
        return implode( ', ', $term_names );
    }
    
    /**
     * Displays user link.
     * 
     * @since 0.1.0
     * @updated 0.3.4
     * 
     * @param mixed $value The value of the meta field.
     */
    private function user_column( $value ) {
        if ( is_array( $value ) ) {
            $user_links = [];
            foreach ( $value as $user_id ) {
                $user_links[] = bp_core_get_userlink( $user_id );
            }
            return implode( ', ', $user_links );
        }
        return bp_core_get_userlink( $value );
    }
    
    /**
     * Displays post title.
     * 
     * @since 0.3.4
     * 
     * @param mixed $value The value of the meta field.
     */
    private function post_title( $value ) {
        if ( is_array( $value ) ) {
            $titles = [];
            foreach ( $value as $post_id ) {
                $titles[] = get_the_title( $post_id );
            }
            return implode( ', ', $titles );
        }
        return get_the_title( $value );
    }
    
    /**
     * Displays sponsorship options link.
     * 
     * @since 0.3.4
     * 
     * @param   mixed   $value      The value of the meta field.
     * @param   int     $post_id    The ID of the business post.
     */
    private function sponsorship_options( $value, $post_id ) {
        if ( $value && is_array( $value ) ) {

            if ( function_exists( 'be_registration_year') ) {
                $value = isset( $value[be_registration_year()] ) ? $value[be_registration_year()] : null;
                
                if ( ! $value || empty( $value ) ) {
                    return;
                }
                
                // Initialize
                $names = [];
                $value = is_array( $value ) ? $value : [$value];
                
                // Loop through option ids
                foreach ( $value as $option_id ) {
                    if ( $option_id && $option_id !== '' ) {
                        // Build names array
                        $names[] = get_the_title( $option_id );
                    }
                }
                
                // Implode string
                $string = implode( ', ', $names );
                
                // Build link
                $link = admin_url( 'admin.php?page=bc-sponsor-bookings&business_id_filter=' . $post_id );
                
                return '<a href="' . $link . '">' . $string . '</a>';
            }
        }
    }
    
    /**
     * Displays timestamp as date time.
     * 
     * @since 0.3.4
     * 
     * @param mixed $value The value of the meta field.
     */
    private function timestamp( $value ) {
        // Get the WordPress site's timezone
        $timezone_string = get_option( 'timezone_string' );
        if ( ! $timezone_string ) {
            $gmt_offset = get_option( 'gmt_offset' );
            $timezone_string = sprintf( 'Etc/GMT%+d', $gmt_offset );
        }
        $timezone = new DateTimeZone( $timezone_string );
        
        $timestamp = $value;
        
        // Create DateTime object from timestamp
        $datetime = new DateTime();
        $datetime->setTimestamp( $timestamp );
        $datetime->setTimezone( $timezone );
        
        // Format the DateTime object
        $readable_date = $datetime->format( 'F j, Y g:i A' );
        return $readable_date;
    }
    
    /**
     * Displays 24-hour time as 12-hour time.
     * 
     * @since 0.3.4
     * 
     * @param mixed $value The value of the meta field.
     */
    private function time( $value ) {
        // Create a DateTime object from the 24-hour time string
        $dateTime = new DateTime($value);

        // Format the time to 12-hour format with AM/PM
        $formattedTime = $dateTime->format('g:i A');

        return $formattedTime;
    }
    
    /**
     * Displays the date time for the event block ID.
     * 
     * @since 0.3.4
     * 
     * @param mixed $event_block_id The value of the event block ID meta field.
     */
    private function event_block_time( $event_block_id ) {
        if ( function_exists( 'be_time' ) ) {
            $time = be_time( $event_block_id );
            return $time->human_start_end;
        }
    }
    
    /**
     * Displays a date as a human readable date.
     * 
     * @since 0.3.4
     * 
     * @param mixed $value The value of the meta field.
     */
    private function date( $value ) {
        // Format the date
        $date = strtotime( $value );
        if ( $date ) {
            $readable_date = gmdate( 'F j, Y', $date );
            return $readable_date;
        }
    }
    
    /**
     * Displays group link.
     * 
     * @since 0.1.0
     * 
     * @param mixed $value The value of the meta field.
     */
    private function project_column( $value ) {
        ob_start();
        bp_group_link( groups_get_group( $value ) );
        return ob_get_clean();
    }
    
    /**
     * Submitted column. Brief.
     * 
     * @since 0.1.0
     * 
     * @param mixed $value The value of the meta field.
     */
    private function submitted_column( $value ) {
        return $value ? bc_admin_icon('check') . '<br>' . __( 'Last Update: ', 'buddyclients-free' ) . gmdate('F j, Y', strtotime($value)) : bc_admin_icon('x');
    }
    
    /**
     * Valid column. Service.
     * 
     * @since 0.1.0
     * 
     * @param mixed $value The value of the meta field.
     */
    private function valid_column( $value ) {
        return $value === 'valid' ? bc_admin_icon('check') : $value;
    }
    
    /**
     * Rate value column. Service.
     * 
     * @since 0.1.0
     * 
     * @param mixed $value The value of the meta field.
     */
    private function rate_value_column( $value, $rate_type ) {
        if (!$value || $value == 0) {
            return __( 'Free', 'buddyclients-free' );
        } else {
            if ($rate_type === 'flat') {
                $type_name = __( ' flat', 'buddyclients-free' );
            } else {
                $singular = get_post_meta($rate_type, 'singular', true);
                $type_name = $singular ? __( ' per ', 'buddyclients-free' ) . strtolower($singular) : '';
            }
            if ($value && $rate_type) {
                return __( '$', 'buddyclients-free' ) . $value . ' ' . $type_name;
            }
        }
    }
    
    /**
     * Percentage column.
     * 
     * @since 0.1.0
     * 
     * @param mixed $value The value of the meta field.
     */
    private function percentage_column( $value ) {
        if ( ! bc_freelancer_mode() ) {
            return $value !== '' ? $value . __( '%', 'buddyclients-free' ) : $value;
        }
    }
    
    /**
     * Displays xprofile column.
     * 
     * @since 0.1.0
     * 
     * @param mixed $value The value of the meta field.
     */
    private function xprofile_column( $value ) {
        $field = xprofile_get_field( $value );
        if ( $field ) {
            return $field->name;
        }
    }
    
    /**
     * Displays xprofile type column.
     * 
     * @since 0.1.0
     * 
     * @param mixed $value The value of the meta field.
     */
    private function xprofile_type_column( $value ) {
        $field = xprofile_get_field( $value );
        if ( $field ) {
            return ucfirst( $field->type );
        }
    }
    
    /**
     * Displays visibility column.
     * 
     * @since 0.1.0
     * 
     * @param mixed $value The value of the meta field.
     */
    private function visibility_column( $value ) {
        return $value === 'visible' ? bc_admin_icon('eye') : bc_admin_icon('eye-slash');
    }
}