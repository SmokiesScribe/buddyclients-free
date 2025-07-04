<?php
namespace BuddyClients\Components\Checkout;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BuddyClients\Components\Checkout\IntentHandler;
use BuddyClients\Components\Stripe\StripeForm;

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
     * The IntentHandler instance.
     * 
     * @var IntentHandler
     */
    private $intent_handler;
    
    /**
     * The BookingIntent defined in url params or session data.
     * 
     * @var BookingIntent
     */
    public $booking_intent;

    /**
     * The BookingPayment defined in url params or session data.
     * 
     * @var BookingPayment
     */
    public $payment;
    
    /**
     * The email of the client.
     * 
     * @var string
     */
    public $client_email;
    
    /**
     * Whether skip payment is enabled.
     * 
     * @var bool
     */
    protected $skip_payment;

    /**
     * Whether Stripe is enabled.
     * 
     * @var bool
     */
    private $stripe_enabled;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     */
    public function __construct() {
        // Make sure we're on the checkout page
        if ( ! $this->is_checkout() ) return;

        // Fetch IntentHandler and data
        $this->init_intent_handler();

        // Exit if no booking intent
        if ( ! $this->booking_intent ) {
            return;
        }

        // Check if Stripe is enabled
        $this->stripe_enabled = $this->stripe_is_enabled();

        // Succeed if previously paid
        $this->handle_previously_paid();

        // Set email
        $this->client_email = $this->booking_intent->client_email;
    }

    /**
     * Checks whether we're on the checkout page.
     * 
     * @since 1.0.27
     */
    private function is_checkout() {
        return buddyc_shortcode_exists( 'checkout' );
    }

    /**
     * Initializes the IntentHandler and fetches data. 
     * 
     * @since 1.0.27
     */
    private function init_intent_handler() {
        // Init IntentHandler
        $this->intent_handler = new IntentHandler;

        // Fetch intent object
        $this->booking_intent = $this->intent_handler->intent;

        // Fetch payment object
        $this->payment = $this->intent_handler->payment;
    }

    /**
     * Succeeds the BookingIntent and BookingPayment if previously paid.
     * 
     * @since 1.0.27
     */
    private function handle_previously_paid() {
        if ( $this->booking_intent->previously_paid ) {
            buddyc_booking_success( $this->booking_intent_id );
            buddyc_payment_success( $this->intent_handler->payment_id, $payment_intent = null );
        }
    }

    /**
     * Checks whether Stripe is enabled.
     * 
     * @since 1.0.20
     */
    private function stripe_is_enabled() {
        // Check if component is enabled
        $enabled = buddyc_component_enabled( 'Stripe' );
        if ( ! $enabled ) {
            return false;
        }

        if ( ! function_exists( 'buddyc_stripe_keys' ) ) {
            return false;
        }

        // Fetch Stripe keys
        $keys = buddyc_stripe_keys();
        $key_types = ['publish', 'secret', 'signing'];

        // Make sure keys exist
        foreach ( $key_types as $key_type ) {
            if ( empty( $keys->{$key_type} ) ) {
                return false;
            }
        }

        // Checks passed
        return true;
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
        // Make sure we're on the checkout page
        if ( ! $this->is_checkout() ) return;
        
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
        if ( $this->payment->status === 'paid' ) {
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
        if ( function_exists( 'buddyc_stripe_keys' ) ) {
            $stripe_keys = buddyc_stripe_keys();
            $mode = $stripe_keys->mode;
            if ( $mode === 'test' ) {
                $content = '<div><p class="buddyc-test-mode-tag">' . __( 'Test Mode', 'buddyclients-lite' ) . '</p></div>';
                if ( ! $tag_only ) {
                    $content .= sprintf(
                        '<p class="buddyc-test-instructions">%s</p>',
                        sprintf(
                            /* translators: %s: the card number */
                            __( 'Use card number %s to simulate a successful payment.', 'buddyclients-lite' ),
                            '4242 4242 4242 4242'
                        )
                    );
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
        $client_name = $this->booking_intent->client_id ? bp_core_get_user_displayname( $this->booking_intent->client_id ) : __( 'The client', 'buddyclients-lite' );
    
        $content = '<h4>' . __( 'The booking has been created!', 'buddyclients-lite' ) . '</h4>';
    
        // Make sure it's not a manual booking
        if ( ! $this->booking_intent->previously_paid ) {
    
            // Check if emails are enabled
            if ( function_exists( 'buddyc_email_enabled' ) && buddyc_email_enabled( 'sales_sub' ) ) {
                $content .= '<p>' . sprintf(
                    /* translators: %1$s: the name of the client; %2$s: the email address of the client */
                    __( '%1$s has been notified at %2$s.', 'buddyclients-lite' ),
                    $client_name,
                    $this->client_email )
                    . '</p>';
    
            // Emails not enabled
            } else {
                $content .= '<p>' . sprintf(
                    /* translators: %1$s: the name of the client; %2$s: the email address of the client */
                    __( 'Emails are not enabled. %1$s has NOT been notified at %2$s.', 'buddyclients-lite' ),
                    $client_name,
                    $this->client_email )
                    . '</p>';
            }
    
            // Copy paste link
            $checkout_link = buddyc_build_pay_link( $this->payment->ID );
            $content .= '<p>' . __( 'The client can check out at the following link.', 'buddyclients-lite' ) . '</p>';
            $content .= buddyc_copy_to_clipboard( $checkout_link, 'buddyc_checkout_link' );
        }
    
        return $content;
    }
    
    /**
     * Builds the return to form link.
     * 
     * @since 0.1.0
     */
    private function back_to_form() {
        $form_page = buddyc_get_setting( 'pages', 'booking_page' );
        return '<a href="' . esc_url( get_permalink( $form_page ) ) . '">' . __( 'Book services here.', 'buddyclients-lite' ) . '</a>';
    }
    
    /**
     * Builds checkout table args.
     * 
     * @since 0.1.0
     */
    private function checkout_table() {
        $args = [
            'line_items'            => unserialize( $this->booking_intent->line_items ),
            'total_fee'             => $this->booking_intent->total_fee,
            'total_due'             => $this->payment->amount,
            'project_name'          => $this->booking_intent->post['project_title'] ?? '',
            'payment_type'          => $this->payment->type,
            'payment_type_label'    => $this->payment->type_label,
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
        if ( $this->payment->amount == 0 ) {
            
            $content .= '<div class="buddyc-margin-20">';
            
            // Free header
            if ( ( $this->booking_intent->total_fee == 0 ) ) {
                $content .= '<h4>' . __( 'Yay! Your services are free.', 'buddyclients-lite' ) . '</h4>';
            } else {
                $content .= '<h4>' . __( 'You do not owe anything today! The full fee will be due on completion of services.', 'buddyclients-lite' ) . '</h4>';
            }
            
            // Free booking form
            $content .= $this->free_form();
            
            $content .= '</div>';
        
        // Skip payment
        } else if ( ! $this->stripe_enabled ) {
            
            $content .= '<div class="buddyc-margin-20">';
            
            // Skip payment header
            $content .= '<h4>' . __( 'Confirm Your Booking', 'buddyclients-lite' ) . '</h4>';
            $content .= '<p>' . __( 'Please use the button below to confirm. We will be in touch to arrange payment.', 'buddyclients-lite' ) . '</p>';
            
            // Free booking form
            $content .= $this->skip_payment_form();
            
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

            $content = buddyc_build_form( $args );
            $content .= '<div id="buddyc-create-account-success"></div>';
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
                'description' => sprintf(
                    /* translators: %1$s: the login url, %2$s: html linking to the privacy policy and/or service terms */
                    __( 'Have an account? <a href="%1$s">Log in here</a><br>%2$s', 'buddyclients-lite' ),
                    esc_url( wp_login_url( get_permalink() ) ),
                    $this->policies()
                ),
                'placeholder'   => 'Your name',
                'field_classes' => 'buddyc-create-account-field margin-free',
                'required'      => true
            ],
            'email' => [
                'key'           => 'create-account-email',
                'type'          => 'email',
                'placeholder'   => __( 'Your email', 'buddyclients-lite' ),
                'field_classes' => 'buddyc-create-account-field margin-free',
                'required'      => true
            ],
            'password' => [
                'key'           => 'create-account-password',
                'type'          => 'password',
                'placeholder'   => __( 'Create password', 'buddyclients-lite' ),
                'field_classes' => 'buddyc-create-account-field margin-free',
                'required'      => true,
                'field_classes' => 'buddyc-password-field'
            ],
            'booking-intent-id' => [
                'key'           => 'booking-intent-id',
                'type'          => 'hidden',
                'value'         => $this->booking_intent->ID,
                'field_classes' => 'buddyc-create-account-field'
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
        $privacy_policy = buddyc_get_setting( 'pages', 'privacy_policy' );
        $site_terms = buddyc_get_setting( 'pages', 'terms_of_service' );
        
        // Generate policy links
        if ($privacy_policy) {
            $policies[] = buddyc_help_link( $privacy_policy, __( 'privacy policy', 'buddyclients-lite' ) );
        }
        if ($site_terms) {
            $policies[] = buddyc_help_link( $site_terms, __( 'website terms', 'buddyclients-lite' ) );
        }
        
        // Generate policies message
        if ( ! empty($policies) ) {
            $policies_text = implode(' and ',$policies);
            return sprintf(
                /* translators: %s: the policies the user is agreeing to (e.g. privacy policy and service terms) */
                __('By creating an account, you agree to the %s.', 'buddyclients-lite'),
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
        if ( ! isset( $this->booking_intent->post['terms-checkbox'] ) ) {
            
            // Define args
            $args = [
                'key'                   => 'checkout-terms',
                'fields_callback'       => [$this, 'checkout_terms_field'],
                'submission_class'      => null,
                'submit_button'         => false
            ];
            
            return buddyc_build_form( $args );
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

        if ( function_exists( 'buddyc_legal_get_current_version' ) ) {
        
            // Define terms link
            $service_agreement_id = buddyc_legal_get_current_version( 'client' );
            
            if ( $service_agreement_id ) {

                $args[] = [
                    'key'           => 'checkout-terms-checkbox',
                    'type'          => 'checkbox',
                    'required'      => true,
                    'field_classes' => 'margin-free',
                    'options'       => [
                        'checkout-agree-terms-checkbox' => [
                        'label' => sprintf(
                            /* translators: %s: the html linking to the service terms */
                            __( 'I agree to the %s.', 'buddyclients-lite' ),
                            buddyc_help_link($service_agreement_id, __( 'service terms', 'buddyclients-lite' ) )
                        ),
                            'value' => $service_agreement_id,
                        ]
                    ]
                ];
            }
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
            'submission_class'      => 'free_checkout',
            'fields_callback'       => [$this, 'free_form_fields'],
            'submit_text'           => __( 'Complete Checkout', 'buddyclients-lite' )
        ];
        
        return buddyc_build_form( $args );
    }

    /**
     * Displays the skip paymnet booking form.
     * 
     * @since 0.1.0
     */
    private function skip_payment_form() {

        // Define form args
        $args = [
            'key'                   => 'skip-payment-checkout',
            'submission_class'      => 'skip_payment_checkout',
            'fields_callback'       => [$this, 'free_form_fields'],
            'submit_text'           => __( 'Complete Checkout', 'buddyclients-lite' )
        ];
        
        return buddyc_build_form( $args );
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
     * Displays the Stripe form.
     * 
     * @since 0.1.0
     */
    private function stripe_form() {
        if ( class_exists( StripeForm::class ) ) {
            return (new StripeForm)->build();
        } else {
            $message = '<p>' . __( 'Payments are not enabled on this website. ', 'buddyclients-lite' ) . buddyc_contact_message() . '</p>';
            echo wp_kses_post( $message );
        }
    }
}