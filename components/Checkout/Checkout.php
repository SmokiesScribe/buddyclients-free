<?php
namespace BuddyClients\Components\Checkout;

use BuddyEvents\Includes\Registration\RegistrationIntent;
use BuddyEvents\Includes\Sponsor\SponsorIntent;

use BuddyClients\Components\Booking\BookingIntent;
use BuddyClients\Components\Stripe\StripeForm;
use BuddyClients\Components\Stripe\StripeKeys;
use BuddyClients\Includes\Form\Form;
use BuddyClients\Includes\Client;
use BuddyClients\Includes\Popup;

/**
 * Checkout page content.
 * 
 * Generates content for the checkout page using the
 * current booking intent data.
 *
 * @since 0.1.0
 */
class Checkout {
    
    /**
     * Booking intent.
     * 
     * @var BookingIntent
     */
    public $booking_intent;
    
    /**
     * The email of the client.
     * 
     * @var string
     */
    public $client_email;
    
    /**
     * Whether it's a registration checkout.
     * 
     * @var bool
     */
    public $is_registration;
    
    /**
     * Whether it's a sponsorship checkout.
     * 
     * @var bool
     */
    public $is_sponsor;
    
    /**
     * Whether skip payment is enabled.
     * 
     * @var bool
     */
    protected $skip_payment;
    
    /**
     * StripeForm object.
     * 
     * @var StripeForm
     */
    protected $stripe_form;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     */
    public function __construct() {
        @session_start();
        
        // Skip payment setting
        $this->skip_payment = bc_get_setting( 'booking', 'skip_payment' ) === 'yes';

        // Retrieve booking intent
        if ( isset( $_GET['booking_id'] ) ) {
            // Get BookingIntent ID from session
            $booking_id = intval( wp_unslash( $_GET['booking_id'] ) );

            // Retrieve BookingIntent by ID
            $this->booking_intent = BookingIntent::get_booking_intent( $booking_id );

            // Set client email
            $this->client_email = $this->booking_intent->client_email;
        }
        
        // Check for registration intent
        if ( isset( $_GET['registration_id'] ) && class_exists( RegistrationIntent::class ) ) {
            $registration_id = intval( wp_unslash( $_GET['registration_id'] ) );
            $this->booking_intent = RegistrationIntent::get_registration_intent( $registration_id );
            $this->client_email = $this->booking_intent->attendee_email;
        } else {
            $this->is_registration = false;
        }
        
        // Check for sponsorship intent
        if ( isset( $_GET['sponsor_id'] ) && class_exists( SponsorIntent::class ) ) {
            $sponsor_id = intval( wp_unslash( $_GET['sponsor_id'] ) );
            $this->booking_intent = SponsorIntent::get_sponsor_intent( $sponsor_id );
            $this->client_email = $this->booking_intent->user_email;
        } else {
            $this->is_sponsor = false;
        }
        
        // Enqueue scripts
        $this->enqueue_free_checkout_script();
    }
    
    /**
     * Build checkout page.
     * 
     * @since 0.1.0
     * 
     * @todo    Add test mode tag.
     *          Handle free checkout.
     */
    public function build() {
        
        // Check if a booking intent is available
        if ( ! $this->booking_intent ) {
            return $this->back_to_form();
        } else {
            return $this->checkout_page();
        }
    }
    
    /**
     * Builds the checkout page content.
     * 
     * @since 0.1.0
     */
    private function checkout_page() {
        
        // Initialize and open container
        $content = '<div class="booking-checkout-container">';
        
        // Check if booking is complete
        if ( $this->booking_intent->status === 'succeeded' ) {
            return $this->back_to_form();
        }
        
        // Sales message
        if ( $this->is_sales() ) {
            $content = $this->test_mode( true );
            $content .= $this->sales_message();
            return $content;
        }
        
        // Open Stripe form column
        $content .= '<div class="stripe-form-column">';
        
        // Test mode
        $content .= $this->test_mode();
        
        // Stripe form
        $content .= $this->form();
        
        // Close Stripe column
        $content .= '</div>';
        
        // Checkout table column
        $content .= '<div class="checkout-fee-column">';
        $content .= $this->checkout_table();
        $content .= '</div>';
        
        // Close container
        $content .= '</div>';
        
        return $content;
    }
    
    /**
     * Generates the test mode tag.
     * 
     * @since 0.1.0
     */
    private function test_mode( $tag_only = false ) {
        if ( class_exists( StripeKeys::class ) ) {
            $stripe_keys = new StripeKeys;
            $mode = $stripe_keys->mode;
            if ( $mode === 'test' ) {
                $content = '<div><p class="bc-test-mode-tag">' . __( 'Test Mode', 'buddyclients' ) . '</p></div>';
                if ( ! $tag_only ) {
                    $content .= '<p class="bc-test-instructions">' . __( 'Use card number 4242 4242 4242 4242 to simulate a successful payment.', 'buddyclients' ) . '</p>';
                }
                return $content;
            }
        }
    }
    
    /**
     * Checks whether the current user is the salesperson.
     * 
     * @since 0.1.0
     * 
     * @return bool
     */
    private function is_sales() {
        if ( property_exists( $this->booking_intent, 'sales_id' ) ) {
            if ( $this->booking_intent->sales_id && get_current_user_id() ) {
                return $this->booking_intent->sales_id == get_current_user_id();
            } else {
                return false;
            }
        }
    }
    
    /**
     * Outputs the message for salespeople.
     * 
     * @since 0.1.0
     */
    private function sales_message() {
        $client_name = $this->booking_intent->client_id ? bp_core_get_user_displayname( $this->booking_intent->client_id ) : __( 'The client', 'buddyclients' );
    
        $content = '<h4>' . __( 'The booking has been created!', 'buddyclients' ) . '</h4>';
    
        // Make sure it's not a manual booking
        if ( ! $this->booking_intent->previously_paid ) {
    
            // Check if emails are enabled
            if ( function_exists( 'bc_email_enabled' ) && bc_email_enabled( 'sales_sub' ) ) {
                $content .= '<p>' . sprintf( __( '%s has been notified at %s.', 'buddyclients' ), $client_name, $this->client_email ) . '</p>';
    
            // Emails not enabled
            } else {
                $content .= '<p>' . sprintf( __( 'Emails are not enabled. %s has NOT been notified at %s.', 'buddyclients' ), $client_name, $this->client_email ) . '</p>';
            }
    
            // Copy paste link
            $checkout_link = $this->booking_intent->checkout_link;
            $content .= '<p>' . __( 'The client can check out at the following link.', 'buddyclients' ) . '</p>';
            $content .= bc_copy_to_clipboard( $checkout_link, 'bc_checkout_link' );
        }
    
        return $content;
    }
    
    /**
     * Builds the return to form link.
     * 
     * @since 0.1.0
     */
    private function back_to_form() {
        $form_page = bc_get_setting( 'pages', 'booking_page' );
        return '<a href="' . esc_url( get_permalink( $form_page ) ) . '">' . __( 'Book services here.', 'buddyclients' ) . '</a>';
    }
    
    /**
     * Builds checkout table args.
     * 
     * @since 0.1.0
     */
    private function checkout_table() {
        $args = [
            'line_items'    => unserialize( $this->booking_intent->line_items ),
            'total_fee'     => $this->booking_intent->total_fee,
            'project_name'  => $this->booking_intent->post['project_title'] ?? '',
        ];
        return (new CheckoutTable( $args ))->build();
    }
    
    /**
     * Displays the checkout form.
     * 
     * @since 0.1.0
     */
    private function form() {
        
        // Create account form
        $content = $this->create_account();
        
        // Service agreement checkbox
        $content .= $this->service_agreement();
        
        // Check total
        if ( $this->booking_intent->total_fee == 0 ) {
            
            $content .= '<div style="margin: 20px">';
            
            // Free header
            $content .= '<h4>' . __( 'Yay! Your services are free.', 'buddyclients' ) . '</h4>';
            
            // Free booking form
            $content .= $this->free_form();
            
            $content .= '</div>';
        
        // Skip payment
        } else if ( $this->skip_payment ) {
            
            $content .= '<div style="margin: 20px">';
            
            // Skip payment header
            $content .= '<h4>' . __( 'Confirm Your Booking', 'buddyclients' ) . '</h4>';
            $content .= '<p>' . __( 'We will be in touch to arrange your payment.', 'buddyclients' ) . '</p>';
            
            // Free booking form
            $content .= $this->free_form();
            
            $content .= '</div>';
            
        } else {
            
            // Stripe form
            $content .= $this->stripe_form();
        }
        
        return $content;
    }
    
    /**
     * Displays the create account form.
     * 
     * @since 0.1.0
     */
    public function create_account() {
        // Make sure user is not logged in
        if ( ! is_user_logged_in() ) {
            
            // Define args
            $args = [
                'key'                   => 'create-account',
                'fields_callback'       => [$this, 'create_account_fields'],
                'submission_class'      => null,
                'submit_button'         => false
            ];
            
            $content = (new Form( $args ) )->build();
            $content .= '<div id="bc-create-account-success"></div>';
            return $content;
        }
    }
    
    /**
     * Displays the create account fields.
     * 
     * @since 0.1.0
     */
     public function create_account_fields() {
        
        $fields = [
            'name' => [
                'key'           => 'create-account-name',
                'type'          => 'text',
                'description' => __( 'Have an account? <a href="' . wp_login_url( esc_html( get_permalink() ) ) . '">Log in here</a><br>' . $this->policies(), 'buddyclients' ),
                'placeholder'   => 'Your name',
                'field_classes' => 'bc-create-account-field margin-free',
                'required'      => true
            ],
            'email' => [
                'key'           => 'create-account-email',
                'type'          => 'email',
                'placeholder'   => __( 'Your email', 'buddyclients' ),
                'field_classes' => 'bc-create-account-field margin-free',
                'required'      => true
            ],
            'password' => [
                'key'           => 'create-account-password',
                'type'          => 'password',
                'placeholder'   => __( 'Create password', 'buddyclients' ),
                'field_classes' => 'bc-create-account-field margin-free',
                'required'      => true,
                'field_classes' => 'bc-password-field'
            ],
            'registration-intent-id' => [
                'key'           => 'registration-intent-id',
                'type'          => 'hidden',
                'value'         => $this->is_registration ? $this->booking_intent->ID : '',
                'field_classes' => 'bc-create-account-field'
            ],
            'sponsor-intent-id' => [
                'key'           => 'sponsor-intent-id',
                'type'          => 'hidden',
                'value'         => $this->is_sponsor ? $this->booking_intent->ID : '',
                'field_classes' => 'bc-create-account-field'
            ],
            'booking-intent-id' => [
                'key'           => 'booking-intent-id',
                'type'          => 'hidden',
                'value'         => ! $this->is_registration ? $this->booking_intent->ID : '',
                'field_classes' => 'bc-create-account-field'
            ]
        ];
        
        return $fields;
        
    }
    
    /**
     * Displays the website policies.
     * 
     * @since 0.1.0
     */
    private function policies() {
        // Initialize 
        $policies = [];
        
        // Get pages from settings
        $privacy_policy = bc_get_setting( 'pages', __( 'privacy_policy', 'buddyclients' ) );
        $site_terms = bc_get_setting( 'pages', __( 'terms_of_service', 'buddyclients' ) );
        
        // Generate policy links
        if ($privacy_policy) {
            $policies[] = Popup::link( $privacy_policy, __( 'privacy policy', 'buddyclients' ) );
        }
        if ($site_terms) {
            $policies[] = Popup::link( $site_terms, __( 'website terms', 'buddyclients' ) );
        }
        
        // Generate policies message
        if ( ! empty($policies) ) {
            $policies_text = implode(' and ',$policies);
            return sprintf(
                __('By creating an account, you agree to the %s.', 'buddyclients'),
                $policies_text
            );
        }
    }
    
    /**
     * Displays the service agreement checkbox.
     * 
     * @since 0.1.0
     */
    private function service_agreement() {
        // Make sure terms were not accepted on form
        if ( ! isset( $this->booking_intent->post['terms-option'] ) ) {
            
            // Define args
            $args = [
                'key'                   => 'checkout-terms',
                'fields_callback'       => [$this, 'checkout_terms_field'],
                'submission_class'      => null,
                'submit_button'         => false
            ];
            
            return (new Form( $args ) )->build();
        }        
    }
    
    /**
     * Displays the service agreement checkbox field.
     * 
     * @since 0.1.0
     */
    public function checkout_terms_field() {
        
        // Initialize
        $args = [];
        
        // Define terms link
        $service_terms = bc_get_setting( 'legal', 'client_legal_version' );
        $event_terms = bc_get_setting( 'legal', 'event_legal_version' );
        $sponsor_terms = bc_get_setting( 'legal', 'sponsor_legal_version' );
        
        if ( $this->is_sponsor ) {
            $terms = $sponsor_terms;
        } else if ( $this->is_registration ) {
            $terms = $event_terms;
        } else {
            $terms = $service_terms;
        }
        
        if ( $service_terms ) {

            $args[] = [
                'key'           => 'checkout-terms-checkbox',
                'type'          => 'checkbox',
                'required'      => true,
                'field_classes' => 'margin-free',
                'options'       => [
                    'checkout-agree-terms-checkbox' => [
                    'label' => sprintf(
                        __( 'I agree to the %s.', 'buddyclients' ),
                        Popup::link($service_terms, __( 'service terms', 'buddyclients' ) )
                    ),
                        'value' => true,
                    ]
                ]
            ];
        }
        return $args;
    }
    
    /**
     * Displays the free booking form.
     * 
     * @since 0.1.0
     */
    private function free_form() {

        // Define form args
        $args = [
            'key'                   => 'free-checkout',
            'submission_class'      => __NAMESPACE__ . '\FreeCheckout',
            'fields_callback'       => [$this, 'free_form_fields'],
            'submit_text'           => __( 'Complete Checkout', 'buddyclients' )
        ];
        
        return (new Form( $args ) )->build();
    }
    
    /**
     * Generates free checkout form fields.
     * 
     * @since 0.1.0
     */
    public function free_form_fields() {
         $args = [
            'type' => 'hidden',
            'key' => 'booking_intent_id',
            'value' => $this->booking_intent->ID,
        ];
        return [$args];
    }
    
    /**
     * Enqueues and localizes free checkout script.
     * 
     * @since 0.4.3
     */
    public function enqueue_free_checkout_script() {
        // Enqueue script
        wp_enqueue_script_module('bc-free-checkout-script', plugin_dir_url(__FILE__) . 'assets/free-checkout.js', array(), BC_PLUGIN_VERSION, true);
    }
    
    /**
     * Displays the Stripe form.
     * 
     * @since 0.1.0
     */
    private function stripe_form() {
        if ( class_exists( StripeForm::class ) ) {
            return (new StripeForm)->build();
        } else {
            echo '<p>' . __( 'Payments are not enabled on this website. ', 'buddyclients' ) . bc_contact_message() . '</p>';
        }
    }
}