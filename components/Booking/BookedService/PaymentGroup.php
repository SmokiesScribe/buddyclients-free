<?php
namespace BuddyClients\Components\Booking\BookedService;

use BuddyClients\Components\Booking\BookingIntent;
use BuddyClients\Components\Affiliate\Affiliate;

/**
 * Payment group.
 * 
 * Generates Payment objects for a single BookingIntent object.
 *
 * @since 0.1.0
 */
class PaymentGroup {
    
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
     * @param   BookingIntent  $booking_intent  The BookingIntent object.
     */
    public function __construct( BookingIntent $booking_intent ) {
        
        // Check for existing payments
        if ( self::payments_exist( $booking_intent->ID ) ) {
            return;
        }
        
        // Get BookingIntent
        $this->booking_intent = $booking_intent;
        
        // Initialize Payments
        $this->payments = [];
        
        // Create payments
        $this-> team_payments();
        $this-> affiliate_payment();
        $this-> sales_payment();
    }
    
    /**
     * Checks for existing Payments for the booking intent.
     * 
     * @since 0.2.12
     * 
     * @param   int     $booking_intent_id  The ID of the booking intent.
     */
    private static function payments_exist( $booking_intent_id ) {
        $existing = Payment::get_payments_by_booking_intent( $booking_intent_id );
        return ! empty( $existing ) ? true : false;
    }
    
    /**
     * Creates a team Payment.
     * 
     * @since 0.1.0
     */
    private function team_payments() {
        
        // Get all BookedService objects for the BookingIntent
        $booked_services = BookedService::get_all_services( $this->booking_intent->ID );
        
        // Build a Payment for each BookedService
        foreach ( $booked_services as $booked_service ) {
            $this->create_payment( 'team', $booked_service->team_id, $booked_service->team_fee, $booked_service->ID );
        }
    }
    
    /**
     * Creates an affiliate Payment.
     * 
     * @since 0.1.0
     */
    private function affiliate_payment() {
        
        // Check if affiliate ID exists
        if ( $this->booking_intent->affiliate_id ) {
            $affiliate_id = $this->booking_intent->affiliate_id;
            $client_id = $this->booking_intent->client_id;
            
            // Exit if the affiliate is not qualified to receive commission
            if ( ! ( new Affiliate( $affiliate_id ) )->is_qualified( $this->booking_intent ) ) {
                return;
            }
            
            // Update client user meta
            update_user_meta( $client_id, 'buddyc_affiliate', $affiliate_id );
            
            // Get client fee
            $client_fee = $this->booking_intent->total_fee;
            
            // Get affiliate percentage
            $affiliate_percentage = buddyc_get_setting( 'affiliate', 'affiliate_percentage' );
            
            if ( $affiliate_percentage == 0 || $client_fee == 0  ) {
                return;
            }
            
            // Calculate affiliate fee
            $affiliate_fee = ( $affiliate_percentage / 100 ) * $client_fee;
            
            // Exit if affiliate program not enabled
            if ( ! class_exists( Affiliate::class ) ) {
                return;
            }
            
            // Create the affiliate payment
            $this->create_payment( 'affiliate', $affiliate_id, $affiliate_fee );
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
    private function create_payment( $type, $payee_id, $amount, $booked_service_id = null ) {
        
        // Exit if the payment amount is 0
        if ( $amount == 0 ) {
            return;
        }
     
        // Define args
        $args = [
            'booking_intent_id'     => $this->booking_intent->ID,
            'service_names'         => $this->booking_intent->service_names,
            'project_id'            => $this->booking_intent->project_id,
            'client_id'             => $this->booking_intent->client_id,
            'type'                  => $type,
            'payee_id'              => $payee_id,
            'amount'                => $amount
        ];
        
        // Retrieve the booked service if it exists
        if ( $booked_service_id ) {
            $booked_service = BookedService::get_booked_service( $booked_service_id );
            $args['service_name'] = $booked_service->name;
        }
        
        // Create new Payment and add to array
        $this->payments[] = (new Payment)->create( $args );
        
        // Update the BookingIntent net fee
        BookingIntent::update_net_fee( $this->booking_intent->ID, $amount );
    }
}