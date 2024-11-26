<?php
namespace BuddyClients\Includes;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BuddyClients\Components\Booking\BookedService\Payment;
use BuddyClients\Includes\Pagination;

/**
 * Commission list content.
 * 
 * Generates a list of payments for an affiliate or sales team member.
 *
 * @since 0.1.0
 */
class CommissionList {
    
    /**
     * The type of Payment.
     * 
     * Accepts 'team', 'affiliate', 'sales'.
     * 
     * @var string
     */
    private $type;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     * 
     * @param   string  $type   The type of payments to retrieve.
     *                          Accepts 'affiliate' or 'sales'.
     */
    public function __construct( $type ) {
        $this->type = $type;
    }
    
    /**
     * Outputs the commission list.
     * 
     * @since 0.1.0
     * 
     * @param   int     $user_id    The ID of the user.
     */
    public function build( $user_id ) {

        // Retrieve items
        $payments = $this->get_payments( $user_id, $this->type );
        
        // Paginate
        $pagination = new Pagination( $payments );
        $items = $pagination->paginated_items;
        
        // Build table
        return $this->table( $items, $pagination );
    }

    /**
     * Retrieves payments by type and user ID.
     * 
     * @since 1.0.17
     * 
     * @param   int     $user_id     The ID of the user.
     * @param   string  $type        The type of commission ('affiliate' or 'sales').
     */
    private function get_payments( $user_id, $type ) {
        $payments = Payment::get_payments_by_type( $type );
        return array_filter($payments, function($payment) use ($user_id) {
            return $payment->payee_id == $user_id;
        });
    }    
    
    /**
     * Defines the table headers.
     * 
     * @since 0.1.0
     */
    private static function header_list() {
        return [
            __( 'Date', 'buddyclients-free' ),
            __( 'Service', 'buddyclients-free' ),
            __( 'Client', 'buddyclients-free' ),
            __( 'Commission', 'buddyclients-free' ),
            __( 'Payment Status', 'buddyclients-free' )
        ];
    }
    
    /**
     * Builds the table headers.
     * 
     * @since 0.1.0
     */
    private function headers() {
        $content = '';
        
        // Open table row
        $content .= '<tr>';
        
        // Loop through headers
        foreach ( self::header_list() as $header ) {
            $content .= '<th>' . $header . '</th>';
        }
        $content .= '</tr>';
        
        return $content;
    }
    
    /**
     * Builds the commission list table.
     * 
     * @since 0.1.0
     * 
     * @param   array       $payments       An array of Payment objects.
     * @param   Pagination  $pagination     The Pagination object.
     */
    private function table( $payments, $pagination ) {
        $content = '';
        
        // Make sure payments exist
        if ( ! $payments ) {
            return '<p>' . __( 'No commission available.', 'buddyclients-free' ) . '</p>';
        }
        
        // Open table
        $content .= '<table class="buddyc-booked-services-table">';
        
        // Output headers
        $content .= $this->headers();
        
        // Loop thorugh payment items
        foreach ( $payments as $item ) {
            
            // Make sure client ID defined
            if ( property_exists( $item, 'client_id' ) ) {
                $content .= $this->table_row( $item );
            }
        }
        
        // Close table
        $content .= '</table>';
        
        // Add pagination controls
        $content .= $pagination->controls();
        
        return $content;
    }
    
    /**
     * Generates a single table row.
     * 
     * @since 1.0.0
     * 
     * @param   object  $item   The Payment item.
     */
    private function table_row( $item ) {
        
        // Initialize and open row
        $content = '<tr>';
        
        // Build group link
        ob_start();
        bp_group_link( groups_get_group( $item->project_id ) );
        $group = ob_get_clean();
        
        // Define column data
        $columns = [
            'Date' => gmdate( 'F j, Y', strtotime( $item->created_at ) ),
            'Service' => $item->service_name ?? $item->service_names,
            'Client' => bp_core_get_user_displayname( $item->client_id ),
            'Commission' => '$' . $item->amount,
            'Payment Status' => buddyc_format_status( $item->status, true ),
        ];
        
        // Loop through columns
        foreach ( $columns as $header => $value ) {
            // Make sure the column header exists
            if ( in_array( $header, self::header_list() ) ) {
                $content .= '<td>' . $value . '</td>';
            }
        }
        
        // Close table row
        $content .= '</tr>';
        
        // Return row
        return $content;
    }
}
