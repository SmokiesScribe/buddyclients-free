<?php
namespace BuddyClients\Admin;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Generates the filter form for an admin table.
 * 
 * @since 1.0.32
 */
class AdminTableFilter {

    /**
     * Key for the admin table.
     * 
     * @var string
     */
    public $key;

    /**
     * Data to generate the filters.
     * 
     * @var array
     */
    public $filters;

    /**
     * Constructor method.
     * 
     * @since 1.0.32
     * 
     * @param   string  $form_key   The form key.
     * @param   array   $filters    The array of form filters.
     */
    public function __construct( $form_key, $filters ) {
        $this->form_key = $form_key;
        $this->filters = $filters;
    }

    /**
     * Generates filter forms.
     * 
     * @since 1.0.32
     */
    public function build() {

        // Exit if filters empty
        if ( empty( $this->filters ) ) {
            return;
        }

        // Build content
        $content = $this->content();

        // Return content if not empty
        if ( ! empty( $content ) ) {
            return $content;
        }
    }

    /**
     * Returns the html form content.
     * 
     * @since 1.0.32
     */
    private function content() {

        // Build fields content
        $fields_content = $this->fields_content();

        // Exit if no visible fields
        if ( empty( $fields_content ) ) {
            return;
        }

        // Build the nonce field
        $nonce_field = wp_nonce_field( 'buddyc_filter_nonce_action', 'buddyc_filter_nonce', true, false );

        // Build filter keys list
        $filter_list = $this->filter_list();

        // Return content
        return sprintf(
            '<form method="POST">
                %1$s
                %2$s
                <input type="hidden" name="buddyc_admin_filter_list" value="%3$s">
                <input type="hidden" name="buddyc_admin_filter_key" value="%4$s">
                <button type="submit" class="button action" name="%4$s_filter_submit">
                    %5$s
                </button>
            </form>',
            $nonce_field,
            $fields_content,
            $filter_list,
            esc_attr( $this->form_key ),
            __( 'Filter', 'buddyclients-lite' )
        );
    }

    /**
     * Builds the comma-separated list of filter keys.
     * 
     * @since 1.0.32
     * 
     * @return  string  Comma-separated list of keys.
     */
    private function filter_list() {
        if ( ! empty( $this->filters ) ) {
            return implode( ',', array_keys( $this->filters ) );
        }
    }

    /**
     * Builds the html for the form fields.
     * 
     * @since 1.0.32
     */
    private function fields_content() {
       // Initialize flag and content
       $visible_filters = false;
       $content = '';
        
       // Loop through the filters
       foreach ( $this->filters as $key => $data ) {
           
           // Skip if the filter has no options
           if ( ! isset( $data['options'] ) || empty ( $data['options'] ) ) {
               continue;
           }
           
           // Update flag
           $visible_filters = true;
           
           // Build the filter name
           $name = sprintf(
               '%s_filter',
               $key
           );

           // Get the current filter value
           $curr_value = buddyc_get_param( $name );

           // Build the label
           $label = sprintf(
                    '%1$s %2$s:',
                    __( 'Filter by', 'buddyclients-lite'),
                    $data['label'] ?? ''
           );

           // Build the options
           $options = $this->build_options( $data['options'], $curr_value );

           // Append field to content
           $content .= sprintf(
                '<label for="%1$s">%2$s</label>
                <select name="%1$s" class="buddyc-admin-filter-select">
                    %3$s
                </select>',
                esc_attr( $name ),
                $label,
                $options
           );
       }
       
       // Check flag and return content
       if ( $visible_filters ) {
           return $content;
       }
    }

    /**
     * Builds the options html. 
     * 
     * @since 1.0.32
     * 
     * @param   array   $options_data   The array of options data. 
     * @param   string  $curr_value     The current value of the filter.
     * @return  string  The options html. 
     */
    private function build_options( $options_data, $curr_value ) {
        // Initialize
        $content = '';

        // Loop through options data
        foreach ( $options_data as $option_key => $option_label ) {
            $content .= sprintf(
                '<option value="%1$s"%2$s>%3$s</option>',
                esc_attr( $option_key ),
                $curr_value == $option_key ? ' selected' : '',
                esc_html( $option_label )
            );
        }

        // Return html
        return $content;
    }

    /**
     * Checks for admin table filter form submission.
     * 
     * @since 1.0.32
     */
    public static function submission() {

        // Check for filter form submission
        if ( isset( $_POST['buddyc_admin_filter_key'] ) && isset( $_POST['buddyc_filter_nonce'] ) ) {

            // Verify the nonce
            $nonce = sanitize_text_field( wp_unslash( $_POST['buddyc_filter_nonce'] ) );
            if ( ! wp_verify_nonce( $nonce, 'buddyc_filter_nonce_action' ) ) {
                return;
            }
            
            // Initialize params by resetting to page 1
            $url_params = ['paged' => 1];

            // Get filter list
            $filter_list = self::get_filter_list();

            var_dump($filter_list);
            
            // Loop through post data
            foreach ( $filter_list as $filter_key ) {  
                
                // Build filter name
                $filter_name = sprintf(
                    '%s_filter',
                    $filter_key
                );

                // Get value
                if ( isset( $_POST[$filter_name] ) ) {
                    $filter_value = sanitize_text_field( wp_unslash( $_POST[$filter_name] ) );
                    
                    // Add to params
                    if ( ! empty( $filter_value ) ) {
                        $url_params[$filter_name] = $filter_value;
                    }
                }              
            }

            // Add params to the url
            $new_url = buddyc_add_params( $url_params );
        
            // Redirect to the new URL
            wp_redirect( $new_url );
            exit;
        }
    }

    /**
     * Retrieves and formats the list of filter keys.
     * 
     * @since 1.0.32
     * 
     * @return  array   An array of filter keys from the submitted form.
     */
    private static function get_filter_list() {
        if ( isset( $_POST['buddyc_admin_filter_list'] ) ) {
            $keys_list = sanitize_text_field( wp_unslash( $_POST['buddyc_admin_filter_list'] ) );
            if ( ! empty( $keys_list ) ) {
                return explode( ',', $keys_list );
            }
        }
    }
}