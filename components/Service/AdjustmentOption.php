<?php
namespace BuddyClients\Components\Service;

/**
 * A single option for an adjustment.
 * 
 * Retrieves the label, operator, and value for a single option.
 * Validates the adjustment option.
 *
 * @since 0.1.0
 * 
 * @see Adjustment
 */
class AdjustmentOption {
    
    /**
     * The adjustment post ID.
     * 
     * @var int
     */
     public $adjustment_id;
     
    /**
     * The option number.
     * 
     * @var int
     */
     public $option_number;
     
    /**
     * The option label.
     * 
     * @var string
     */
     public $label;
     
    /**
     * The operator. Accepts '-', '+', and 'x'.
     * 
     * @var string
     */
     public $operator;
     
    /**
     * The value on which to implement the operator.
     * 
     * @var string
     */
     public $value;
    
    /**
     * Constructor method.
     *
     * @since 0.1.0
     *
     * @param   string    $option_id    The adjustment option ID.
     *                                  Adjustment post id - option number.
     */
    public function __construct( $option_id ) {
        // Split option id
        $parts = explode( '-', $option_id );
        $this->adjustment_id = $parts[0];
        $this->option_number = $parts[1];
        
        // Retrieves meta values
        $this->get_meta();
    }
    
    /**
     * Retrieves option meta values.
     * 
     * @since 0.1.0
     */
    private function get_meta() {
        $this->value = get_post_meta( $this->adjustment_id, 'option_' . $this->option_number . '_value', true );
        $this->label = get_post_meta( $this->adjustment_id, 'option_' . $this->option_number . '_label', true );
        $this->operator = get_post_meta( $this->adjustment_id, 'option_' . $this->option_number . '_operator', true );
    }
    
    /**
     * Validates the option.
     * 
     * @since 0.1.0
     */
    public function validate() {
        return ( $this->label && ( $this->label !== '' ) );
    }
}