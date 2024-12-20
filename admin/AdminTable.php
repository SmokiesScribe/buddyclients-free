<?php
namespace BuddyClients\Admin;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use stdClass;
use BuddyClients\Admin\AdminTableItem;

/**
 * Generates a single admin table.
 */
class AdminTable {
    
    /**
     * Key for the admin table.
     * 
     * @var string
     */
    public $key;
    
    /**
     * The title for the admin table.
     * 
     * @var string
     */
    public $title;
    
    /**
     * The description displayed below the title.
     * 
     * @var string
     */
    public $description;
    
    /**
     * Data to generate the filters.
     * 
     * @var array
     */
    public $filters;
    
    /**
     * The H2 table header displayed below the description.
     * 
     * @var string
     */
    public $table_header;

    
    /**
     * Array of headings.
     * 
     * @var array
     */
     public $headings;
     
    /**
     * Associative array of keys and values.
     * 
     * @var array
     */
     public $columns;
     
    /**
     * An array of items to build the rows.
     * 
     * @var array
     */
     public $items;
     
    /**
     * The filtered array of items
     * 
     * @var array
     */
     public $filtered_items;

    /**
     * Number of items per page.
     *
     * @var int
     */
    private $items_per_page = 10;

    /**
     * Current page number.
     *
     * @var int
     */
    private $current_page;
    
    /**
     * Admin page constructor.
     *
     * Sets up the admin page using the provided data.
     *
     * @since 0.1.0
     *
     * @param array $args {
     *     An associative array of data to build the admin table.
     *
     *     @type    array          $headings    The key used to build the slug.
     *     @type    array          $columns     The parent menu slug.
     *     @type    array          $items       The title of the admin page.
     *     @type    array          $filters
     *          Keyed array of data to build the filters.
     * 
     *          @type   string                  Array key or property to check agaist filter value.
     *          @type   string                  Accepts 'array_key' or 'property'.
     *          @type   array       $options    Keyed array of filter options.
     *          @type   string                  Default filter option.
     * }
     */
    public function __construct( $args ) {
        
        // Extract args
        $this->extract_args( $args );
        
        // Calculate current page based on URL parameter or default to 1
        $paged = buddyc_get_param( 'paged' );
        $this->current_page = $paged ? absint( $paged ) : 1;
        
        // Build and output table
        $table = $this->build_table();
        $allowed_html = $this->allowed_html();
        echo wp_kses( $table, $allowed_html );
    }
    
    /**
     * Extracts properties from the args.
     * 
     * @since 1.0.3
     * 
     * @param   array   $args   See constructor.
     */
    private function extract_args( $args ) {
        $this->key              = $args['key'] ?? '';
        $this->headings         = $args['headings'];
        $this->column_data      = $args['columns'];
        $this->items            = $args['items'];
        $this->title            = $args['title'] ?? '';
        $this->description      = $args['description'] ?? '';
        $this->filters          = $args['filters'] ?? null;
        $this->table_header     = $args['header'] ?? '';
        $this->items_per_page   = $args['items_per_page'] ?? 10;
        
        // Ensure items are always an array
        if ( ! is_array( $this->items ) ) {
            $this->items = array( $this->items );
        }
        
        // Ensure items are objects
        $this->items_are_objects();
    }

    /**
     * Defines the allowed html for tables.
     * 
     * @since 1.0.16
     */
    private function allowed_html() {        
        $form_tags = buddyc_allowed_html_form();
        $additional_tags = [
            'script' => [],
            'i' => [ 'class' => [], 'style' => [] ],
            'span' => [ 'id' => [], 'class' => [] ],
            'button'   => ['onclick' => [], 'type' => [], 'class' => [], 'style' => []],
            'th'    => ['class' => []],
            'span'    => ['class' => []]
        ];        
        return array_merge( $form_tags, $additional_tags );
    }
    
    /**
     * Ensures that items are objects.
     * 
     * @since 0.2.6
     */
    public function items_are_objects() {
        foreach ( $this->items as $key => $item ) {
            if ( is_array( $item ) ) {
                $this->items[$key] = $this->array_to_object( $item );
            }
        }
    }
    
    /**
     * Converts an array to an object.
     * 
     * @since 0.2.6
     * 
     * @param   $array  The array to convert to an object.
     */
    private function array_to_object( $array ) {
        $object = new stdClass();
        foreach ( $array as $key => $value ) {
            $object->$key = $value;
        }
        return $object;
    }

    /**
     * Filters items based on current page and items per page.
     *
     * @since 0.1.0
     *
     * @param array $items All items.
     * @return array Filtered items for the current page.
     */
    private function filter_items_for_current_page( $items ) {
        // Initialize
        $filtered_items = [];
        
        // Filter items by selection
        foreach ( $items as $item ) {
            $match = $this->filter_match( $item );
            if ( $match ) {
                $filtered_items[] = $item;
            }
        }
        
        // Assign filtered items
        $this->filtered_items = $filtered_items;
        
        // Calculate offset
        $offset = ( $this->current_page - 1 ) * $this->items_per_page;
        
        // Return items for the current page
        return array_slice( $filtered_items, $offset, $this->items_per_page );
    }

    /**
     * Calculates total number of pages.
     *
     * @since 0.1.0
     *
     * @param int $total_items Total number of items.
     * @return int Total number of pages.
     */
    private function calculate_total_pages( $total_items ) {
        return ceil( $total_items / $this->items_per_page );
    }

    /**
     * Builds pagination HTML.
     *
     * @since 0.1.0
     *
     * @param int $total_pages Total number of pages.
     * @return string Pagination HTML.
     */
    private function build_pagination_html( $total_pages ) {
        $output = '<div class="tablenav">';
        $output .= '<div class="tablenav-pages">';
        /* translators: %d: the number of items being displayed */
        $output .= '<span class="displaying-num">' . sprintf( esc_html__( '%d items', 'buddyclients-free' ), count( $this->filtered_items ) ) . '</span>';
        $output .= '<span class="pagination-links">';
        
        // First page link
        $output .= $this->current_page == 1 ? '<span class="tablenav-pages-navspan button disabled margin-3" aria-hidden="true">' : '';
        $output .= $this->current_page == 1 ? '«' : '<a class="first-page button margin-3" href="' . esc_url( add_query_arg( 'paged', 1 ) ) . '">&laquo;</a>';
        $output .= '</span>';
        
        // Previous page link
        $output .= $this->current_page == 1 ? '<span class="tablenav-pages-navspan button disabled margin-3" aria-hidden="true">' : '';
        $output .= $this->current_page == 1 ? '‹' : '<a class="prev-page button margin-3" href="' . esc_url( add_query_arg( 'paged', max( 1, $this->current_page - 1 ) ) ) . '">&lsaquo;</a>';
        $output .= '</span>';
        
        // Current page number of total
        $output .= '<span class="tablenav-pages-navspan screen-reader-text">' . esc_html__( 'Current Page', 'buddyclients-free' ) . '</span>';
        $output .= '<span id="table-paging" class="paging-input">';
        $output .= '<span class="tablenav-paging-text">' . sprintf(
            /* translators: %1$d: the number of the current page being displayed; %2$d: the total number of pages */
            esc_html__( '%1$d of %2$d', 'buddyclients-free' ),
            $this->current_page,
            $total_pages )
            . '</span>';
        
        // Next page link
        $output .= $this->current_page == $total_pages ? '<span class="tablenav-pages-navspan button disabled margin-3" aria-hidden="true">' : '';
        $output .= $this->current_page == $total_pages ? '›' : '<a class="next-page button margin-3" href="' . esc_url( add_query_arg( 'paged', min( $total_pages, $this->current_page + 1 ) ) ) . '">&rsaquo;</a>';
        $output .= '</span>';
        
        // Last page link
        $output .= $this->current_page == $total_pages ? '<span class="tablenav-pages-navspan button disabled margin-3" aria-hidden="true">' : '';
        $output .= $this->current_page == $total_pages ? '»' : '<a class="last-page button margin-3" href="' . esc_url( add_query_arg( 'paged', $total_pages ) ) . '">&raquo;</a>';
        $output .= '</span>';
        
        $output .= '</span>';
        $output .= '</div>';
        $output .= '</div>';
        
        return $output;
    }

    /**
     * Builds the table.
     * 
     * @since 0.1.0
     */
    private function build_table() {
        // Initialize content variable
        $content = '';

        // Build HTML content
        $content .= '<div class="wrap">';
        
        // Display Title
        $content .= '<h1>' . esc_html( $this->title ) . '</h1>';
        
        // Display Description
        $content .= '<p>' . esc_html( $this->description ) . '</p>';
        
        // Filters
        $content .= $this->filter_forms();
        
        // H2 Table Header
        $content .= '<h2>' . esc_html( $this->table_header ) . '</h2>';
        
        // Table
        $content .= '<table class="wp-list-table widefat fixed striped">';
        $content .= '<thead><tr>';
        
        // Table Headings
        foreach ( $this->headings as $heading ) {
            $content .= '<th class="buddyc-admin-col-head-' . strtolower( $heading ) . '" scope="col">' . esc_html( $heading ) . '</th>';
        }
        
        $content .= '</tr></thead>';
        $content .= '<tbody id="the-list">';
        
        // Filter items for the current page
        $items_for_current_page = $this->filter_items_for_current_page( $this->items );
        foreach ( $items_for_current_page as $item ) {
            $content .= '<tr>';
            
            foreach ( $this->build_columns( $item ) as $key => $value ) {
                $content .= '<td class="buddyc-admin-column-' . esc_attr( $key ) . '">';
                if ( ! empty( $value ) ) {
                    $content .= $value;
                }
                $content .= '</td>';
            }
            
            $content .= '</tr>';
        }
        
        $content .= '</tbody></table>';
        
        // Calculate total number of pages
        $total_pages = $this->calculate_total_pages( count( $this->filtered_items ) );
        
        // Output pagination HTML only if there is more than one page
        if ( $total_pages > 1 ) {
            $content .= $this->build_pagination_html( $total_pages );
        }
        
        // Close wrap div
        $content .= '</div>';
        
        // Return the collected HTML content
        return $content;
    }
    
    /**
     * Builds column values.
     * 
     * @since 0.1.0
     */
    private function build_columns( $item ) {
        // Initialize
        $columns = [];
        
        // Loop through columns
        foreach ( $this->column_data as $key => $value_data ) {
            foreach ( $value_data as $property => $method ) {
                // Build item value
                $columns[$key] = $this->item_value( $item, $key, $property, $method );
            }
        }
        return $columns;
    }
    
    /**
     * Defines a single item value.
     * 
     * @since 0.1.0
     */
    private function item_value( $item, $key, $property, $method ) {
        // Make sure method is callable
        if ( is_callable( [AdminTableItem::class, $method] ) ) {
            return AdminTableItem::$method( $property, $item->$property, $item->ID, $key );
            
        // Default to uppercase first words
        } else {
            return AdminTableItem::uc_format( $item->$property );
        }
    }
    
    /**
     * Generates filter forms.
     * 
     * @since 0.1.0
     */
    protected function filter_forms() {
        ob_start();
        
        // Initialize flag
        $visible_filters = false;
        
        // Exit if no filters
        if ( ! $this->filters ) {
            return;
        }
        
        // Start building the filter form
        echo '<form method="POST" style="margin-bottom: 20px;">';

        // Nonce field
        wp_nonce_field( 'buddyc_filter_nonce_action', 'buddyc_filter_nonce' );
        
        // Loop through the filters
        foreach ( $this->filters as $key => $data ) {
            
            // Skip if the filter has no options
            if ( ! isset( $data['options'] ) || empty ( $data['options'] ) ) {
                continue;
            }
            
            // Update flag
            $visible_filters = true;
            
            // Build the filter name
            $name = $key . '_filter';

            // Get the current filter value
            $curr_value = buddyc_get_param( $name );
            
            // Filter label
            echo '<label for="' . esc_attr( $name ) . '">';
            echo esc_html__( 'Filter by', 'buddyclients-free') . ' ' . esc_html( $data['label'] ) . ': ';
            echo '</label>';
            
            // Build the dropdown
            echo '<select name="' .  esc_attr( $name ) . '" id="' . esc_attr( $name ) . '" style="margin-right: 5px;">';
            
            // Loop through the options
            foreach ( $data['options'] as $option_key => $option_label ) {
                echo '<option value="' . esc_attr( $option_key ) . '"' . ( $curr_value == $option_key ? ' selected' : '' ) . '>' . esc_html( $option_label ) . '</option>';
            }
        
            // Close the dropdown
            echo '</select>';
        }
        
        // Check flag
        if ( ! $visible_filters ) {
            return '';
        }
        
        // Submission verification field
        echo '<input type="hidden" name="buddyc_admin_filter_key" value="' . esc_attr( $this->key ) . '">';
        
        // Submit button
        echo '<button type="submit" class="button action" name="' . esc_attr( $this->key ) . '_filter_submit">';
        echo esc_html__( 'Filter', 'buddyclients-free' );
        echo '</button>';
        
        // Close the form
        echo '</form>';
        
        return ob_get_clean();
    }

    
    /**
     * Checks for filter match.
     * 
     * @since 0.1.0
     * @updated 0.4.0
     */
    protected function filter_match( $item ) {
        
        // Initialize
        $display = true;
        
        // No filters defined
        if ( ! $this->filters ) {
            return $display;
        }
        
        // Loop through filters
        foreach ( $this->filters as $key => $data ) {
            
            // Get property or array key to check
            $property = $data['property'] ?? null;
            $array_key = $data['array_key'] ?? null;
            
            // Get item value to check
            $item_value = $property ? $item->$property : $item[$array_key];
            
            // Get current filter value
            $filter_value = buddyc_get_param( $key . '_filter' );
            
            // No filters value
            if ( ! $filter_value ) {
                continue;
            }
            
            // Convert filter value to array if it's a string representation of an array
            if (is_string($filter_value) && substr($filter_value, 0, 1) === '[' && substr($filter_value, -1) === ']') {
                $filter_value = json_decode($filter_value, true);
            }
            
            // Check if item value and filter value are arrays
            if (is_array($item_value) && is_array($filter_value)) {
                // Check for any intersection
                if (empty(array_intersect($item_value, $filter_value))) {
                    $display = false;
                }
            } elseif (is_array($item_value)) {
                // Check if filter value is in item value array
                if (!in_array($filter_value, $item_value)) {
                    $display = false;
                }
            } elseif (is_array($filter_value)) {
                // Check if item value is in filter value array
                if (!in_array($item_value, $filter_value)) {
                    $display = false;
                }
            } else {
                // Direct comparison for non-array values
                if ($filter_value && $item_value != $filter_value) {
                    $display = false;
                }
            }
            
            // If one of the checks fails, no need to continue checking other filters
            if (!$display) {
                break;
            }
        }
        return $display;
    }
    
    /**
     * Updates variables for settings pages.
     * 
     * @since 0.1.0
     */
    protected function settings_page() {
        
        // Check whether we're creating a settings page
        $settings = $this->data['settings'] ?? false;
        
        if ($settings) {
            
            // Append 'settings' to key
            $this->key = $this->key . '-settings';
        
            // Create settings page instance
            $this->settings_page_instance = new SettingsPage($this->data);
            
            // Define callback
            $this->callback = array($this->settings_page_instance, 'render_page');
        }
    }
    
    /**
     * Builds slug.
     * 
     * @since 0.1.0
     */
    protected function build_slug() {
        $key = str_replace('_','-',$this->key);
        return 'buddyc-' . $key;
    }
    
    /**
     * Adds submenu page.
     * 
     * @since 0.1.0
     */
    public function add_submenu() {
        add_submenu_page(
            $this->data['parent_slug'] ?? null,
            $this->data['title'] ?? '',
            $this->data['title'] ?? '',
            $this->data['cap'] ?? 'manage_options',
            $this->build_slug(),
            $this->callback,
            $this->data['menu_order'] ?? null
        );
    }
}