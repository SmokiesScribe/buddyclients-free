<?php
namespace BuddyClients\Components\Stripe;

/**
 * Stripe keys.
 * 
 * Retrieves and validates the Stripe keys from settings.
 * Handles publishable, secret, and webhooks keys.
 *
 * @since 0.1.0
 */
class StripeKeys {
    
    /**
     * Stripe mode.
     * 
     * @var string Accepts 'test' or 'live'.
     */
    public $mode;
    
    /**
     * Endpoint URL.
     * 
     * @var string
     */
    public $endpoint_url;
    
    /**
     * Publishable key.
     * 
     * @var string
     */
    public $publish;
    
    /**
     * Secret key.
     * 
     * @var string
     */
    public $secret;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     */
    public function __construct() {
        $this->mode = bc_get_setting( 'stripe', 'stripe_mode' );
        $this->endpoint_url = self::endpoint_url();
        $this->get_settings();
    }
    
    /**
     * Defines endpoint url.
     * 
     * @since 0.1.0
     */
    public static function endpoint_url() {
        return plugin_dir_url(__FILE__) . 'endpoint.php';
    }
    
    /**
     * Retrieves Stripe settings.
     * 
     * @since 0.1.0
     */
    private function get_settings() {
        
        // Define settings based on current mode
        $settings = [
            'secret'        => 'secret_key_' . $this->mode,
            'publish'       => 'public_key_' . $this->mode,
            'signing'       => 'signing_' . $this->mode,
        ];
        
        // Retrieve settings and assign to var
        foreach ( $settings as $key => $setting ) {
            $this->{$key} = bc_get_setting( 'stripe', $setting );
        }
    }
    
    /**
     * Validates all Stripe keys for the current mode.
     * 
     * @since 0.1.0
     * 
     * @return bool
     */
    public function validate_mode() {
        
        $data = [
            'secret'    => self::validate_secret( $this->secret ),
            'publish'   => self::validate_publish( $this->publish ),
            'signing'   => self::validate_signing( $this->signing ),
        ];
        
        // Return false if any fail
        return in_array( false, $data ) ? false : true;
    }
    
    /**
     * Validates Stripe keys.
     * 
     * @since 0.1.0
     * 
     * @param   string  $key    Optional. The key to validate.
     *                          Otherwise validates all keys.
     * @return  bool|array
     */
    public static function validate( $key = null, $value ) {
        
        $data = [
            'secret'    => self::validate_secret( $value ),
            'publish'   => self::validate_publish( $value ),
            'signing'   => self::validate_signing( $value ),
        ];
        
        // Return bool for key or array
        return $key ? $data[$key] : $data;
    }

    /**
     * Validates a secret key.
     * 
     * @since 0.1.0
     * 
     * @param   string  $secret_key    The secret key to validate.
     * @return  bool
     */
    private static function validate_secret( $secret_key ) {

        // No key provided
        if ( ! $secret_key ) {
            return false;
        }
        
        // Load Stripe library
        bc_stripe_library();
    
        // Make a test API request to Stripe
        $response = wp_remote_get(
            'https://api.stripe.com/v1/charges',
            array(
                'headers' => array(
                    'Authorization' => 'Bearer ' . $secret_key,
                ),
            )
        );
    
        // Check if the request was successful and if it returned a 200 status code
        if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
            return true; // Stripe API is connected
        } else {
            return false; // Stripe API connection failed
        }
    }
    
    /**
     * Validates a publishable key.
     * 
     * @since 0.1.0
     * 
     * @param   string  $publish_key    The publishable key to validate.
     * @return  bool
     */
    private static function validate_publish( $publish_key ) {
    
        // No key provided
        if ( ! $publish_key ) {
            return false;
        }
    
        // Initialize cURL session
        $ch = curl_init();
    
        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, "https://api.stripe.com/v1/tokens");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "card[number]=4242424242424242&card[exp_month]=12&card[exp_year]=2017&card[cvc]=123");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_USERPWD, $publish_key . ":");
    
        // Execute cURL request
        $response = json_decode(curl_exec($ch), true);
    
        // Check for cURL errors
        if (curl_errno($ch)) {
            // Handle cURL error
            echo 'Error:' . curl_error($ch);
            curl_close($ch);
            return false;
        }
    
        // Close cURL session
        curl_close($ch);
    
        // Check if the response contains an error message indicating an invalid API key
        if (isset($response["error"]["message"]) && substr($response["error"]["message"], 0, 24) == "Invalid API Key provided") {
            return false;
        }
    
        // Key is valid
        return true;
    }
    
    /**
     * Validates a webhook signing key.
     * 
     * @since 0.1.0
     * 
     * @param   string  $signing_key    The signing key to validate.
     * @return  bool
     */
    private static function validate_signing( $signing_key ) {
        // No key provided
        if ( ! $signing_key ) {
            return false;
        }

        // Define a sample payload
        $payload = '{"id":"evt_test","type":"payment_intent.created","data":{"object":{"id":"pi_test"}}}';
    
        // Define the timestamp and signature
        $timestamp = time();
        $signature = hash_hmac('sha256', $timestamp . '.' . $payload, $signing_key);
    
        // Simulate a webhook event
        $response = wp_remote_post(
            self::endpoint_url(), // Replace with your webhook URL
            array(
                'body' => $payload,
                'headers' => array(
                    'Stripe-Signature' => 't=' . $timestamp . ',v1=' . $signature,
                ),
            )
        );
    
        // Check if the request was successful and if it returned a 200 status code
        if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
            return true; // Stripe webhook signature is valid
        } else {
            return false; // Stripe webhook signature validation failed
        }
    }
    
}