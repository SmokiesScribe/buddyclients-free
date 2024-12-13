<?php
namespace BuddyClients\Components\Booking;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BuddyClients\Includes\{
    Client          as Client,
    Project         as Project,
    FileHandler     as FileHandler,
    ObjectHandler   as ObjectHandler,
    PDF             as PDF
};

use BuddyClients\Components\Booking\BookedService\{
    Payment         as Payment,
    BookedService   as BookedService
};

/**
 * Single booking intent.
 * 
 * The object generated on submission of the booking form.
 * Represents a single intended purchase, whether suceeded or incomplete.
 *
 * @since 0.1.0
 */
class BookingIntent {
    
    /**
     * ObjectHandler instance.
     *
     * @var ObjectHandler|null
     */
    private static $object_handler = null;
    
    /**
     * The ID of the BookingIntent.
     * 
     * @var int
     */
    public $ID;
    
    /**
     * The class name.
     * 
     * @var string
     */
    public $class = 'BookingIntent';
    
    /**
     * The ID of the PaymentIntent.
     * 
     * @var int
     */
    public $payment_intent_id;
    
    /**
     * The current status.
     * Accepts 'incomplete' and 'succeeded'.
     * 
     * @var string
     */
    public $status;
    
    /**
     * Timestamp of creation..
     * 
     * @var string
     */
    public $created_at;
    
    /**
     * Global post data.
     * 
     * @var array
     */
    public $post;
    
    /**
     * Global file data.
     * 
     * @var ?array
     */
    public $files;
    
    /**
     * An array of File IDs.
     * 
     * @var array
     */
    public $file_ids;
    
    /**
     * The ID of the client or 'guest'.
     * 
     * @var string|int
     */
    public $client_id;
    
    /**
     * Client email.
     * 
     * @var ?int
     */
    public $client_email;
    
    /**
     * Project ID.
     * 
     * @var int
     */
    public $project_id;
    
    /**
     * Affiliate ID.
     * 
     * @var int
     */
    public $affiliate_id;
    
    /**
     * The ID of the salesperson.
     * 
     * @var int
     */
    public $sales_id;
    
    /**
     * Line items.
     * Array of LineItem objects.
     * 
     * @var array
     */
    public $line_items;
    
    /**
     * Checkout link.
     * 
     * @var string
     */
    public $checkout_link;
    
    /**
     * The net fee.
     * 
     * @var float
     */
    public $net_fee;
    
    /**
     * The total client fee.
     * 
     * @var float
     */
    public $total_fee;
    
    /**
     * Whether the booking was previously paid.
     * 
     * @var ?bool
     */
    public $previously_paid;
    
    /**
     * The service agreement version.
     * 
     * @var ?int
     */
    public $terms_version;
    
    /**
     * The ID of the attendee agreement PDF.
     * 
     * @var string
     */
    public $terms_pdf;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     * 
     * @param array $post Global post data from form submission.
     */
    public function __construct( $post, $files = null ) {

        // Initialize object handler
        self::init_object_handler();
        
        // Default status to incomplete
        $this->status = $this->status ?? 'incomplete';
        
        // Get form submission data
        $this->post = $post;
        $this->files = $files;
        
        // Set variables
        $this->set_var();
        
        // Get affiliate ID
        $this->affiliate_id = $this->get_affiliate_id();
        
        // Handle file if not empty
        if ( $files ) {
            $this->file_ids = $this->handle_files( $files );
        }
        
        // Add object to database
        $this->ID = self::$object_handler->new_object( $this );
        
        // Create checkout link and update object
        $this->build_checkout_link();
        
        // Schedule abandoned booking check
        $this->schedule_abandoned_booking_check();
        
        // Succeed if previously paid
        if ( $this->previously_paid ) {
            new SuccessfulBooking( $this->ID );
        }
    }
    
    /**
     * Initializes ObjectHandler.
     * 
     * @since 0.1.0
     */
    private static function init_object_handler() {
        if ( ! self::$object_handler ) {
            self::$object_handler = new ObjectHandler( __CLASS__ );
        }
    }
    
    /**
     * Creates new FileHandler.
     * 
     * @since 0.1.0
     */
    private function handle_files( $files ) {
        
        $args = [
            'user_id'           => $this->client_id,
            'project_id'        => $this->project_id,
            'booking_intent_id' => $this->ID,
            'temporary'         => true,
        ];
        
        $file_handler = new FileHandler( $files, $args );
        return $file_handler->file_ids;
    }
    
    /**
     * Retrieves the affiliate ID from user data or session data.
     * 
     * @since 0.4.3
     */
    private function get_affiliate_id() {
        @session_start();
        
        // Initialize
        $affiliate_id = null;
        
        // Check for affilaite ID in user meta
        $affiliate_id = get_user_meta( $this->client_id, 'buddyc_affiliate', true );
        
        // Check for affiliate ID in session
        if ( ! $affiliate_id && isset( $_SESSION['buddyc_affiliate'] ) ) {
            $affiliate_id = isset( $_SESSION['buddyc_affiliate'] ) ? sanitize_text_field( wp_unslash( $_SESSION['buddyc_affiliate'] ) ) : null;
        }
        
        return $affiliate_id;
    }
    
    /**
     * Sets variables from post data.
     * 
     * @since 0.1.0
     */
    private function set_var() {
        $this->client_id            = $this->post['user-id'];
        $this->client_email         = $this->post['user-email'] ?? null;
        $this->project_id           = $this->post['buddyc_projects'] ?? null;
        $this->total_fee            = $this->post['total-fee'];
        $this->line_items           = serialize(json_decode(stripslashes( $this->post['hidden-line-items'] )));
        $this->service_names        = $this->service_names();
        $this->sales_id             = $this->post['sales-id'] ?? null;
        $this->previously_paid      = $this->post['previously-paid'] ?? null;
        $this->checkout_link        = null;
        $this->payment_intent_id    = 0;
        $this->terms_version        = isset( $this->post['terms-checkbox'] ) ? $this->post['terms-checkbox'][0] : null;
        $this->terms_pdf            = $this->generate_terms_pdf( $this->terms_version );
        
        /**
         * Fires when user ID passed to checkout.
         * 
         * @since 0.1.0
         * 
         * @param   int $user_id            The ID of the user.
         * @param   int $booking_intent_id  The ID of the BookingIntent.
         */
        do_action('buddyc_user_checkout', $this->client_id, $this->ID);
        
        return $this;
    }
    
    /**
     * Generates a PDF from the terms version.
     * 
     * @since 0.1.0
     * 
     * @param   int     $post_id    The ID of the terms post.
     * @return  int     The ID of the newly created PDF.
     */
    private function generate_terms_pdf( $post_id ) {
        if ( $post_id ) {
            // Define args
            $args = [
                'content'       => get_post_field('post_content', $post_id),
                'user_id'       => $this->client_id,
                'type'          => 'service_terms',
                'title'         => get_the_title($post_id),
                'items'         => [
                    sprintf(
                        /* translators: %s: the url of the site */
                        __('Accepted via checkbox on %s', 'buddyclients-free'),
                        site_url()
                    ),
                    gmdate('F d, Y')
                ],
            ];
            
            // Create PDF
            return buddyc_create_pdf( $args );
        }
    }
    
    /**
     * Builds the checkout link.
     * 
     * @since 0.1.0
     */
    private function build_checkout_link() {
        
        // Get checkout page id
        $checkout_page = buddyc_get_setting('pages', 'checkout_page');
        $checkout_url = get_permalink($checkout_page);
        
        // Build checkout link
        $this->checkout_link = $checkout_url . '?booking_id=' . $this->ID;
        
        // Update object in database
        self::$object_handler->update_object_properties( $this->ID, ['checkout_link' => $this->checkout_link] );
    }
    
    /**
     * Creates string of service names.
     * 
     * @since 0.1.0
     */
    private function service_names() {
        $service_names = [];
        foreach ( unserialize( $this->line_items ) as $line_item ) {
            $service_names[] = $line_item->service_name;
        }
        return implode( ', ', $service_names );
    }
    
    /**
     * Schedules abandoned booking.
     * 
     * @since 0.1.0
     */
    private function schedule_abandoned_booking_check() {
        // Define timeout timestamp (current time + timeout period)
        $timeout_timestamp = strtotime( "+10 minutes", current_time( 'timestamp' ) );
    
        // Schedule event to check abandoned bookings after timeout
        wp_schedule_single_event( $timeout_timestamp, 'buddyc_check_abandoned_booking', array( $this->ID ) );
        
        // Define the callback directly
        add_action('buddyc_check_abandoned_booking', function( $booking_intent_id ) {
            // Check if the status is succeeded
            if ( $this->status !== 'succeeded' ) {
                new AbandonedBooking( $booking_intent_id );
            }
        }, 10, 1);
    }
    
    /**
     * Retrieves booking intent by ID.
     * 
     * @since 0.1.0
     * 
     * @param   int         $ID     Booking intent ID.
     * @return  object|bool         BookingIntent on success. False on failure.
     */
    public static function get_booking_intent( $ID ) {
        
        // Initialize object handler
        self::init_object_handler();
            
        // Get object
        return self::$object_handler->get_object( $ID );
    }
    
    /**
     * Retrieves all booking intents.
     * 
     * @since 0.1.0
     * 
     * @return  array   Array of BookingIntent objects.
     */
    public static function get_all_booking_intents() {
        
        // Initialize object handler
        self::init_object_handler();
            
        // Get all objects
        return self::$object_handler->get_all_objects();
    }
    
    /**
     * Retrieves all booking intents for a client.
     * 
     * @since 0.4.3
     * 
     * @param   int     $client_id      The ID of the client.
     * @return  array   Array of BookingIntent objects.
     */
    public static function get_booking_intents_by_client( $client_id ) {
        
        // Initialize object handler
        self::init_object_handler();
            
        // Get all objects
        return self::$object_handler->get_objects_by_property( 'client_id', $client_id );
    }
    
    /**
     * Retrieves project ID.
     * 
     * @since 0.1.0
     * 
     * @param int $ID The BookingIntent ID.
     */
    public static function get_project_id( $ID ) {
        
        // Retrieve booking intent
        $booking_intent = self::get_booking_intent( $ID );
        
        // Return project id
        return $booking_intent->project_id;
    }
    
    /**
     * Retrieves PaymentIntent ID.
     * 
     * @since 0.1.0
     * 
     * @param int $ID The BookingIntent ID.
     */
    public static function get_payment_intent_id( $ID ) {
        
        // Retrieve booking intent
        $booking_intent = self::get_booking_intent( $ID );
        
        // Return project id
        return $booking_intent->payment_intent_id ?? 0;
    }
    
    /**
     * Retrieves client ID.
     * 
     * @since 0.1.0
     * 
     * @param int $ID The BookingIntent ID.
     */
    public static function get_client_id( $ID ) {
        
        // Retrieve booking intent
        $booking_intent = self::get_booking_intent( $ID );
        
        // Return project id
        return $booking_intent->client_id;
    }
    
    /**
     * Updates status.
     * 
     * @since 0.1.0
     * 
     * @param   int     $ID         The BookingIntent ID.
     * @param   string  $new_status The status to update to.
     */
    public static function update_status( $ID, $new_status ) {
        
        // Initialize object handler
        self::init_object_handler();

        // Get old status
        $old_object = self::get_booking_intent( $ID );
        $old_status = $old_object->status;
        
        // Update status
        $booking_intent = self::update_booking_intent( $ID, 'status', $new_status );
        
        // Check if we transitioned to succeeded
        if ( $old_status !== $new_status && $new_status === 'succeeded' ) {
            
            /**
             * Fires on transition to completed BookingIntent.
             * 
             * @since 0.1.0
             * 
             * @param object $booking_intent The BookingIntent object.
             */
            do_action('buddyc_booking_intent_succeeded', $booking_intent);
        }

        // Return updated object
        return $booking_intent;
    }
    
    /**
     * Updates a single property of a BookingIntent.
     * 
     * @since 0.1.0
     * 
     * @param   int     $ID         The BookingIntent ID.
     * @param   string  $property   The property to update.
     * @param   mixed   $value      The new value for the property.
     */
    public static function update_booking_intent( $ID, $property, $value ) {
        // Initialize object handler
        self::init_object_handler();

        // Update properties
        $updated_intent = self::$object_handler->update_object_properties( $ID, [$property => $value] );

        // Return updated object
        return $updated_intent;
    }

    /**
     * Updates properties of a BookingIntent.
     * 
     * @since 0.1.0
     * 
     * @param   int     $ID         The BookingIntent ID.
     * @param   array  $properties  An associative array of property-value pairs.
     */
    public static function update_booking_intent_properties( $ID, $properties ) {
        // Initialize object handler
        self::init_object_handler();

        // Update properties
        $updated_intent = self::$object_handler->update_object_properties( $ID, $properties );

        // Return updated object
        return $updated_intent;
    }
    
    /**
     * Updates project id.
     * 
     * @since 0.1.0
     * 
     * @param   int     $ID         The BookingIntent ID.
     * @param   string  $new_status The status to update to.
     */
    public static function update_project_id( $ID, $project_id ) {
        return self::update_booking_intent( $ID, 'project_id', $project_id );
    }
    
    /**
     * Updates PaymentIntent id.
     * 
     * @since 0.1.0
     * 
     * @param   int     $ID         The ID of the BookingIntent.
     * @param   string  $new_status The ID of the PaymentIntent.
     */
    public static function update_payment_intent_id( $ID, $payment_intent_id ) {
        return self::update_booking_intent( $ID, 'payment_intent_id', $payment_intent_id );
    }
    
    /**
     * Updates client id.
     * 
     * @since 0.1.0
     * 
     * @param   int     $ID         The BookingIntent ID.
     * @param   int     $client_id  The new client ID.
     * @return  bool                True on success. False on failure.
     */
    public static function update_client_id( $ID, $client_id ) {
        return self::update_booking_intent( $ID, 'client_id', $client_id );
    }
    
    /**
     * Updates client email.
     * 
     * @since 0.1.0
     * 
     * @param   int     $ID             The BookingIntent ID.
     * @param   int     $client_email   The new client email.
     * @return  bool                True on success. False on failure.
     */
    public static function update_client_email( $ID, $client_email ) {
        return self::update_booking_intent( $ID, 'client_email', $client_email );
    }
    
    /**
     * Updates the net fee.
     * 
     * @since 0.1.0
     * 
     * @param   int     $ID             The BookingIntent ID.
     * @param   float   $payment_amount The amount of the payment to deduct.
     */
    public static function update_net_fee( $ID, $payment_amount ) {
        // Initialize object handler
        self::init_object_handler();
        
        // Get BookingIntent
        $booking_intent = self::get_booking_intent( $ID );
        
        // Get existing net fee or total fee
        $net_fee = $booking_intent->net_fee ?? $booking_intent->total_fee;
        
        // Subtract payment amount
        $net_fee -= $payment_amount;
        
        // Update object
        return self::update_booking_intent( $ID, 'net_fee', $net_fee );
    }
    
    /**
     * Deletes the Booking Intent.
     * 
     * @since 0.2.4
     * 
     * @param   int     $booking_intent_id  The ID of the BookingIntent to delete.
     */
    public static function delete_booking_intent( $booking_intent_id ) {
        // Initialize object handler
        self::init_object_handler();
        
        // Get booking intent
        $booking_intent = self::get_booking_intent( $booking_intent_id );
        
        // Delete associated payments
        $payments = Payment::get_payments_by_booking_intent( $booking_intent_id );
        if ( $payments ) {
            foreach ( $payments as $payment ) {
                Payment::delete_payment( $payment->ID );
            }
        }
        
        // Delete associated services
        $services = BookedService::get_services_by_booking_intent( $booking_intent_id );
        if ( $services ) {
            foreach ( $services as $service ) {
                BookedService::delete_booked_service( $service->ID );
            }
        }
        
        // Delete object
        self::$object_handler->delete_object( $booking_intent_id );
    }
}