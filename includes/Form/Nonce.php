<?php
namespace BuddyClients\Includes\Form;

/**
 * Nonce manager.
 * 
 * Handles nonce creation and verification.
 *
 * @since 0.1.0
 */
class Nonce {
    
    /**
     * Nonce key.
     * 
     * @var string
     */
    private $key;
    
    /**
     * Nonce field name.
     * 
     * @var string
     */
    private $field_name;
    
    /**
     * Constructor method.
     *
     * @since 0.1.0
     */
    public function __construct( $key ) {
        $this->key = $key;
    }
    
    /**
     * Generates nonce field name.
     * 
     * @since 0.1.0
     */
    private function field_name() {
        $prefix = 'buddyclients_';
        $action_name = $this->key;
        $field_name = $prefix . $action_name . '_nonce';
        return $field_name;
    }
    
    /**
     * Generates nonce field.
     * 
     * @since 0.1.0
     * 
     * @param str Action name.
     */
    public function build() {
        $nonce_field_name = $this->field_name();
        $nonce_field = wp_nonce_field($this->key, $nonce_field_name);
        return $nonce_field;
    }
    
    /**
     * Checks nonce submission.
     * 
     * @since 0.1.0
     */
    public function check() {
        
        // Build full field name
        $nonce_field_name = $this->field_name();
        
        // Check if nonce is set and verified
        if (isset($_POST[$nonce_field_name]) && wp_verify_nonce($_POST[$nonce_field_name], $this->key)) {
            return true;
        } else {
            return false;
        }
    }
}