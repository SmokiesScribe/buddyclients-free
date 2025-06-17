<?php
namespace BuddyClients\Components\Booking\BookedService;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BuddyClients\Components\Booking\BookingIntent;
use BuddyClients\Components\Affiliate\Affiliate;
use BuddyClients\Components\Booking\BookedService\BookedService;

/**
 * Payment group.
 * 
 * Generates Payment objects for a single BookingIntent object.
 *
 * @since 0.1.0
 */
class PaymentGroup {
    
    /**
     * The ID of the BookingIntent object.
     * 
     * @var int
     */
    private $booking_intent_id;

    /**
     * The BookingIntent object.
     * 
     * @var BookingIntent
     */
    private $booking_intent;
    
    /**
     * Array of created Payment objects.
     * 
     * @var array
     */
    public $payments;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     * 
     * @param   int     $booking_intent_id  The ID of the BookingIntent object.
     * @param   array   $booked_services    Optional. An array of BookedService objects.
     */
    public function __construct( $booking_intent_id, $booked_services = [] ) {
        $this->booking_intent_id = $booking_intent_id;
        $this->booking_intent = BookingIntent::get_booking_intent( $booking_intent_id );
        
        // Check for existing payments
        if ( $this->payments_exist() ) {
            return;
        }
        
        // Initialize Payments
        $this->payments = [];
        
        // Create payments
        $this-> team_payments( $booked_services );
        $this-> affiliate_payment();
        $this-> sales_payment();
    }
    
    /**
     * Checks for existing Payments for the booking intent.
     * 
     * @since 0.2.12
     */
    private function payments_exist() {
        $existing = Payment::get_payments_by_booking_intent( $this->booking_intent_id );
        return ! empty( $existing ) ? true : false;
    }
    
    /**
     * Creates a team Payment.
     * 
     * @since 0.1.0
     * 
     * @param   array   $booked_services    An array of BookedService objects.
     */
    private function team_payments( $booked_services ) {

        if ( empty( $booked_services ) || ! is_array( $booked_services ) ) {
            return;
        }
        
        // Build a Payment for each BookedService
        foreach ( $booked_services as $booked_service ) {
            $this->create_payment( 'team', $booked_service->team_id, $booked_service->team_fee, $booked_service->name );
        }
    }
    
    /**
     * Creates an affiliate Payment.
     * 
     * @since 0.1.0
     */
    private function affiliate_payment() {
        
        // Make sure affiliate program enabled and affiliate ID exists
        if ( function_exists( 'buddyc_referral' ) && ! empty( $this->booking_intent->affiliate_id ) ) {

            // Build the new Referral
            $referral = buddyc_referral( $this->booking_intent );

            // Make sure commission exists
            if ( $referral->commission_amount > 0 ) {
                // Create the affiliate payment
                $this->create_payment( 'affiliate', $referral->affiliate_id, $referral->commission_amount );
            }
        }
    }
    
    /**
     * Creates a sales commission Payment.
     * 
     * @since 0.1.0
     */
    private function sales_payment() {
        
        if ( isset( $this->booking_intent->sales_id ) ) {
            
            $sales_id = $this->booking_intent->sales_id;
            $client_fee = $this->booking_intent->total_fee;
            
            // Make sure sales mode is enabled
            $sales_mode = buddyc_get_setting( 'booking', 'sales_team_mode' );
            if ( $sales_mode !== 'yes' ) {
                return;
            }
            
            // Get commission percentage
            $sales_percentage = buddyc_get_setting( 'sales', 'sales_commission_percentage' );
            
            // Calculate commission
            $commission_fee = ( $sales_percentage / 100 ) * $client_fee;
            
            // Make sure the salesperson is qualified to receive commission
            if ( $sales_id === $this->booking_intent->client_id ) {
                return;
            }
            
            // Create commission payment
            $this->create_payment( 'sales', $sales_id, $commission_fee );
        }
    }
    
    /**
     * Creates a new Payment.
     * 
     * @since 0.1.0
     * 
     * @param   string  $type   The type of Payment to create.
     */
    private function create_payment( $type, $payee_id, $amount, $service_name = null ) {
        
        // Exit if the payment amount is 0
        if ( $amount == 0 ) {
            return;
        }
     
        // Define args
        $args = [
            'booked_service_id'     => $booked_service_id,
            'booking_intent_id'     => $this->booking_intent->ID,
            'service_names'         => $this->booking_intent->service_names,
            'project_id'            => $this->booking_intent->project_id,
            'client_id'             => $this->booking_intent->client_id,
            'type'                  => $type,
            'payee_id'              => $payee_id,
            'amount'                => $amount
        ];
        
        // Retrieve the booked service if it exists
        if ( $service_name ) {
            $args['service_name'] = $service_name;
        }
        
        // Create new Payment and add to array
        $this->payments[] = (new Payment)->create( $args );
        
        // Update the BookingIntent net fee
        BookingIntent::update_net_fee( $this->booking_intent->ID, $amount );
    }
}