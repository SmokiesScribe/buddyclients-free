<?php
namespace BuddyClients\Includes\Integrations;

use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;
use FacebookAds\Object\ServerSide\Content;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\DeliveryCategory;
use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\EventRequest;
use FacebookAds\Object\ServerSide\Gender;
use FacebookAds\Object\ServerSide\UserData;

/**
 * Integrates with Meta events.
 * 
 * Allows users to track actions in Facebook ads.
 * 
 * @since 0.4.2
 */
class Meta {
    
    /**
     * Facebook access token.
     * 
     * @var string
     */
    private $access_token;
    
    /**
     * Facebook pixel ID.
     * 
     * @var string
     */
    private $pixel_id;

    /**
     * Constructor method.
     * 
     * @since 0.4.2
     */
    public function __construct() {
        // Load vendor files
        if ( ! include BC_PLUGIN_DIR . 'vendor/meta-business-sdk/vendor/autoload.php' ) {
            error_log('Failed to load Meta Business SDK autoloader.');
            return;
        }

        $this->define_var();
        $this->define_hooks();
    }
    
    /**
     * Defines hooks to send payloads.
     * 
     * @since 0.4.2
     */
    private function define_hooks() {
        // Event Registration
        add_action( 'be_successful_registration', [ $this, 'send_meta_payload' ], 10, 1 );
    }
    
    /**
     * Defines the class variables.
     * 
     * @since 0.4.2
     */
    private function define_var() {
        $this->access_token = bc_get_setting( 'integrations', 'meta_access_token' );
        $this->pixel_id = bc_get_setting( 'integrations', 'meta_pixel_id' );
    }

    /**
     * Sends a payload to Meta on successful registration.
     * 
     * @since 0.1.0
     * 
     * @param Registration $registration The Registration object.
     */
    public function send_meta_payload( $registration ) {
        // Exit if class does not exist
        if ( ! class_exists( Api::class ) ) {
            error_log( __( 'Facebook API class does not exist.', 'buddyclients' ) );
            return;
        }

        // Check that we have an access token and pixel ID
        if ( empty( $this->access_token ) || empty( $this->pixel_id ) ) {
            error_log( __( 'Meta payload not sent: Access token or Pixel ID is missing.', 'buddyclients' ) );
            return;
        }
        
        // Initialize API
        try {
            Api::init(null, null, $this->access_token);
            $api = Api::instance();
            $api->setLogger(new CurlLogger());
        } catch ( Exception $e ) {
            error_log( __( 'Error initializing Facebook API: ', 'buddyclients' ) . $e->getMessage());
            return;
        }
        
        $events = [];
        
        $user_data = (new UserData())
            ->setEmails([ hash('sha256', $registration->attendee_email ) ]);
            
            // ->setPhones(array()); @todo Add phone support.
        
        $custom_data = (new CustomData())
            ->setValue( $registration->total_fee )
            ->setCurrency("USD");
        
        $event = (new Event())
            ->setEventName( "CompleteRegistration" )
            ->setEventTime( time() )
            ->setUserData( $user_data )
            ->setCustomData( $custom_data )
            ->setActionSource("website");

        $events[] = $event;
        
        try {
            $request = ( new EventRequest( $this->pixel_id ) )
                ->setEvents( $events );
            
            $request->execute();
        } catch ( Exception $e ) {
            error_log('Error sending Meta event: ' . $e->getMessage());
        }
    }
}
