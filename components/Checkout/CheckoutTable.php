<?php
namespace BuddyClients\Components\Checkout;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

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
     * The array of LineItem objects.
     * 
     * @var array
     */
    public $line_items;

    /**
     * The name of the project.
     * 
     * @var string
     */
    public $project_name;

    /**
     * The total fee for the BookingIntent.
     * 
     * @var float
     */
    private $total_fee;

    /**
     * The amount of the BookingPayment.
     * 
     * @var float
     */
    private $total_due;

    /**
     * The type of payment (deposit or final).
     * 
     * @var string
     */
    private $payment_type;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     * 
     * @param   array   $args {
     *     An array of args to build the checkout table.
     * 
     *     @type    array   $line_items         An array of LineItem objects.
     *     @type    string  $project_name       The name of the project.
     *     @type    float   $total_fee          The total fee for the booking.
     *     @type    float   $total_due          The total amount due today.
     *     @type    string  $payment_type       The type of payment (deposit or final).
     *     @type    string  $payment_type_label The label for the payment type.
     * }
     */
    public function __construct( $args = [] ) {
        $this->extract_args( $args );
    }

    /**
     * Extracts the args passed to the constructor.
     * 
     * @since 1.0.27
     * 
     * @param   array   $args {
     *     An array of args to build the checkout table.
     * 
     *     @type    array   $line_items         An array of LineItem objects.
     *     @type    string  $project_name       The name of the project.
     *     @type    float   $total_fee          The total fee for the booking.
     *     @type    float   $total_due          The total amount due today.
     *     @type    string  $payment_type       The type of payment (deposit or final).
     *     @type    string  $payment_type_label The label for the payment type.
     * }
     */
    private function extract_args( $args ) {
        $this->line_items           = $args['line_items'] ?? [];
        $this->project_name         = $args['project_name'] ?? '';
        $this->total_fee            = $args['total_fee'] ?? 0;
        $this->total_due            = $args['total_due'] ?? 0;
        $this->payment_type         = $args['payment_type'] ?? '';
        $this->payment_type_label   = $args['payment_type_label'] ?? '';
    }
    
    /**
     * Builds table.
     * 
     * @since 0.1.0
     */
    public function build() {
        
        // Initialize with the mobile button
        $output = sprintf(
            '<div id="buddyc-checkout-mobile-button">%s</div>',
            __( 'View Summary', 'buddyclients-lite' )
        );
        
        // Open table container
        $output .= '<div class="buddyc-checkout-details-container">';

        // Loading indicator
        $output .= '<div class="buddyc-checkout-loading-indicator" id="buddyc-loading-indicator"></div>';

        // Working message
        $output .= '<div class="buddyc-working-message"></div>';
    
        // Add the table to the container
        $output .= '<table class="buddyc-checkout-table">';
        $output .= '<tbody>';

        // Build the line items
        $output .= $this->build_line_items();

        // Close the table and add the total fee row
        $output .= sprintf(
            '<h4 id="buddyc-checkout-project">%s</h4>',
            $this->project_name
        );
        
        $output .= '<p id="buddyc-checkout-total" class="buddyc-checkout-total buddyc-checkout-total-fee">$' . number_format($this->total_due, 2) . '</p>';
        
        $output .= '</tbody>';

        // Build the footer with the totals
        $output .= $this->build_footer();

        // Close the table
        $output .= '</table>';

        // Close the container div
        $output .= '</div>';

        // Output the HTML
        return $output;
    }

    /**
     * Builds the line item rows for the table.
     * 
     * @since 1.0.27
     */
    private function build_line_items() {
        $output = '';
        if ( $this->line_items ) {

            foreach ( $this->line_items as $item ) {
                
                $item_name = $item->service_name ?? $item->name;
                $item_fee = $item->service_fee ?? $item->fee;
                $description = property_exists( $item, 'unit_label' ) ? $item->unit_label . $item->adjustment_label : $item->description;
                
                if ( isset( $item_name ) && isset( $item_fee ) ) {
                    $item_price = (float)str_replace(['$', ','], '', $item_fee ); // Remove '$' and commas, and convert the price to a float
    
                    // Generate a table row for each item
                    $output .= '<tr>';
                    $output .= '<td>' . $item_name . '<br><span class="buddyc-checkout-unit-label">' . $description . '</span></td>';
                    $output .= '<td>$' . number_format($item_price, 2) . '</td>';
                    $output .= '</tr>';
                }
            }
        }
        return $output;
    }

    /**
     * Builds the footer containing the total fee and total due.
     * 
     * @since 1.0.27
     */
    private function build_footer() {
        
        $output = '<tfoot>';
        $output .= '<tr><td colspan="2" class="buddyc-checkout-footer-border"></td></tr>';        
        $output .= '<tr>';
        $output .= '<th class="buddyc-checkout-total-label">' . __( 'Total:', 'buddyclients-lite' ) . '</th>';
        $output .= '<td><span class="buddyc-checkout-total-fee">$' . number_format( $this->total_fee, 2 ) . '</span></td>';
        $output .= '</tr>';

        // Include payment row if different from total
        if ( $this->total_due != $this->total_fee ) {
            $output .= '<tr>';

            $output .= sprintf(
                '<th class="buddyc-checkout-total-label">%1$s %2$s:</th>',
                $this->payment_type_label,
                __( 'Due Today', 'buddyclients-lite' )
            );

            $output .= '<td><span>$' . number_format( $this->total_due, 2 ) . '</span></td>';
            $output .= '</tr>';
        }

        $output .= '</tfoot>';

        return $output;
    }

    /**
     * Builds a single footer row.
     * 
     * @since 1.0.27
     */
    private function build_footer_row() {

    }
}