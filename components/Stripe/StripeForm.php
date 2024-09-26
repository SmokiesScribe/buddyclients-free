<?php
namespace BuddyClients\Components\Stripe;

/**
 * Stripe payment form.
 * 
 * Generates the form to accept payments.
 * Creates a PaymentIntent.
 *
 * @since 0.1.0
 */
class StripeForm {
    
    /**
     * Stripe keys.
     * 
     * @var StripeKeys
     */
    public $keys;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     */
    public function __construct() {
        $this->keys = new StripeKeys;
        $this->enqueue_script();
    }
    
    /**
     * Enqueues script.
     * 
     * @since 0.1.0
     */
    private function enqueue_script() {
        session_start();
        
        // Define booking intent
        $booking_intent = null;
        if ( isset( $_SESSION['booking_intent'] ) ) {
            $booking_intent = $_SESSION['booking_intent'];
        } else if ( isset( $_SESSION['registration_intent'] ) ) {
            $booking_intent = unserialize( $_SESSION['registration_intent'] );
        } else if ( isset( $_SESSION['sponsor_intent'] ) ) {
            $booking_intent = unserialize( $_SESSION['sponsor_intent'] );
        }
        
        // Make sure we have a booking intent
        if ( $booking_intent ) {
            // Enqueue script
            wp_enqueue_script_module('bc-stripe-stripe-checkout', plugin_dir_url( __FILE__ ) . 'stripe-assets/stripe-checkout.js', array(), BC_PLUGIN_VERSION, true);
            
            // Output params
            // Hook an anonymous function to the action
            add_action('wp_footer', function() use ($booking_intent) {
                $this->output_params($booking_intent);
            });
        }
    }
    
    /**
     * Outputs the script params.
     * 
     * @since 0.4.3
     */
    private function output_params( $booking_intent ) {
        // Sanitize the parameters
        $params = [
            'pubKey'            => esc_attr( $this->keys->publish ),
            'createIntentUrl'   => esc_url( plugins_url('/stripe-assets/create-payment-intent.php', __FILE__) ),
            'confirmationUrl'   => esc_url( get_permalink( bc_get_setting( 'pages', 'confirmation_page' ) ) ),
            'bookingIntent'     => $booking_intent,
            'logInUrl'          => esc_url( wp_login_url( esc_html( get_permalink() ) ) ),
        ];
        
        // Output as a data attribute in a <script> tag instead of a <div>
        echo '<script id="bc-stripe-stripe-checkout-params" type="application/json">' . json_encode( $params ) . '</script>';
    }

    
    /**
     * Builds Stripe payment form.
     * 
     * @since 0.1.0
     */
    public function build() {
        ob_start();
        ?>
        <div class="stripe-shortcode-wrapper">
            <head>
                <script src="https://js.stripe.com/v3/"></script>
            </head>
            <body>
                <!-- Display a payment form -->
                <form id="payment-form">
                    <div id="payment-element">
                        <!-- Stripe.js injects the Payment Element -->
                    </div>
                    <button id="stripe-submit">
                        <div class="spinner hidden" id="spinner" style="display: none"></div>
                        <span id="button-text"><?php _e('Pay now', 'buddyclients'); ?></span>
                    </button>
                    <div id="payment-message" class="hidden"></div>
                    
                </form>
            </body>
        </div>
        <?php
        return ob_get_clean();
    }
}