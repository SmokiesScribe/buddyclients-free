<?php
namespace BuddyClients\Components\Checkout;

use BuddyClients\Admin\Settings as Settings;


/**
 * Table to display a client's order summary.
 * 
 * Generates a table with selected services and rates and the total fee.
 * Should be displayed on the checkout page and the booking form.
 *
 * @since 0.1.0
 */
class CheckoutTable {
    
    /**
     * Booking intent.
     * 
     * @var BookingIntent
     */
    public $booking_intent;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     * 
     * @param array $args
     *     Optional.
     */
    public function __construct( $args = array() ) {
        $this->line_items   = $args['line_items'] ?? array();
        $this->total_fee    = $args['total_fee'] ?? 0;
        $this->project_name = $args['project_name'] ?? '';
    }
    
    /**
     * Builds table.
     * 
     * @since 0.1.0
     */
    public function build() {
        
        // Initialize output html
        $output = '';
        
        // Build the mobile button
        $output .= '<div id="checkout-mobile-button">' . __( 'View Summary', 'buddyclients-free' ) . '</div>';
        
        // Table container div
        $output .= '<div class="checkout-details-container">';

        // Loading indicator
        $output .= '<div class="checkout-loading-indicator" id="buddyc-loading-indicator"></div>';
    
        // Add the table to the container
        $output .= '<table class="checkout-table">';
        $output .= '<tbody>';
        
        if ( $this->line_items ) {

            foreach ( $this->line_items as $item ) {
                
                $item_name = $item->service_name ?? $item->name;
                $item_fee = $item->service_fee ?? $item->fee;
                $description = property_exists( $item, 'unit_label' ) ? $item->unit_label . $item->adjustment_label : $item->description;
                
                if ( isset( $item_name ) && isset( $item_fee ) ) {
                    $item_price = (float)str_replace(['$', ','], '', $item_fee ); // Remove '$' and commas, and convert the price to a float
    
                    // Generate a table row for each item
                    $output .= '<tr>';
                    $output .= '<td>' . $item_name . '<br><span class="checkout-unit-label">' . $description . '</span></td>';
                    $output .= '<td>$' . number_format($item_price, 2) . '</td>';
                    $output .= '</tr>';
                }
            }
        }

        // Close the table and add the total fee row
        $output .= '<h4 id="buddyc-checkout-project">' . $this->project_name . '</h4>';
        
        $output .= '<p id="buddyc-checkout-total" class="buddyc-checkout-total buddyc-checkout-total-fee">$' . number_format($this->total_fee, 2) . '</p>';
        
        $output .= '</tbody>';
        $output .= '<tfoot>';
        $output .= '<tr>';
        $output .= '<th>' . __( 'Total:', 'buddyclients-free' ) . '</th>';
        $output .= '<td><span class="buddyc-checkout-total-fee">$' . number_format($this->total_fee, 2) . '</span></td>';
        $output .= '</tr>';
        $output .= '</tfoot>';
        $output .= '</table>';

        // Close the container div
        $output .= '</div>';

        // Output the HTML
        return $output;
    }
}