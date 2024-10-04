<?php
namespace BuddyClients\Includes;

/**
 * Creates pagination for front-end tables.
 * 
 * @since 0.1.0
 */
class Pagination {
    
    /**
     * The current page.
     * 
     * @var int
     */
    public $current_page;
    
    /**
     * The number of items per page.
     * 
     * Defaults to 10.
     * 
     * @var int
     */
    public $per_page = 10;
    
    /**
     * The total number of pages.
     * 
     * @var int
     */
    public $total_pages;
    
    /**
     * The paginated items.
     * 
     * @var array
     */
    public $paginated_items;
    
    /**
     * Constructor method.
     * 
     * @since 1.0.0
     * 
     * @param   array   $items      The array of items to paginate.
     * @param   array   $args
     *     An optional array of args. {
     * 
     *     @type    int     $per_page   The number of items per page.   
     *                                  Defaults to 10.
     * }
     */
    public function __construct( $items, $args = null) {
        $this->per_page = $args['per_page'] ?? 10;
        $this->current_page = isset( $_GET['page'] ) ? intval( $_GET['page'] ) : 1;
        $this->total_pages = $this->calculate_total_pages( $items );
        $this->paginated_items = $this->paginated_items( $items );
    }
    
    /**
     * Calculates the total number of pages.
     * 
     * @since 1.0.0
     * 
     * @param   array   $items      The array of items to paginate.
     */
    private function calculate_total_pages( $items ) {
        $total_items = count( $items );
        $total_pages = ceil( $total_items / $this->per_page );
        return $total_pages;
    }
    
    /**
     * Outputs paginated items.
     * 
     * @since 1.0.0
     * 
     * @param   array   $items              The items to paginate.
     * @param   int     $items_per_page     Optional. The number of items per page.
     *                                      Defaults to 10.
     */
    private function paginated_items( $items ) {
        $offset = ( $this->current_page - 1 ) * $this->per_page;
        $paginated_items = array_slice( $items, $offset, $this->per_page );
        return $paginated_items;
    }
    
    /**
     * Generates pagination controls.
     * 
     * @param int $current_page The current page number.
     * @param int $total_pages The total number of pages.
     * @return string The HTML for the pagination controls.
     */
    public function controls() {
        if ( $this->total_pages <= 1 ) {
            return ''; // No pagination needed
        }

        // Get the current URL without the page parameter
        $url = isset( $_SERVER["REQUEST_URI"] ) ? sanitize_text_field( wp_unslash( $_SERVER["REQUEST_URI"] ) ) : '';
        $url = strtok( $url, '?' );

        if ( isset( $_SERVER["QUERY_STRING"] ) ) {
            $query_string = sanitize_text_field( wp_unslash( $_SERVER["QUERY_STRING"] ) );
            parse_str( $query_string, $query_params );
        }        

        // Initialize the content for pagination controls
        $content = '<div class="pagination">';
        
        // Previous page link
        if ( $this->current_page > 1 ) {
            $query_params['page'] = $this->current_page - 1;
            $content .= '<a href="' . $url . '?' . http_build_query( $query_params ) . '">&laquo; ' . __( 'Previous', 'buddyclients' ) . '</a>';
        }
        
        // Page number links
        for ( $i = 1; $i <= $this->total_pages; $i++ ) {
            $query_params['page'] = $i;
            if ( $i == $this->current_page ) {
                $content .= '<span class="current-page">' . $i . '</span>';
            } else {
                $content .= '<a href="' . $url . '?' . http_build_query( $query_params ) . '">' . $i . '</a>';
            }
        }
        
        // Next page link
        if ( $this->current_page < $this->total_pages ) {
            $query_params['page'] = $this->current_page + 1;
            $content .= '<a href="' . $url . '?' . http_build_query( $query_params ) . '">' . __( 'Next', 'buddyclients' ) . ' &raquo;</a>';
        }
        
        $content .= '</div>';
        
        return $content;
    }
}
