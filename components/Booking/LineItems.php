<?php
namespace BuddyClients\Components\Booking;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BuddyClients\Components\Service\{
    Service,
    RateType,
    Adjustment,
    AdjustmentOption
};

/**
 * Generates a line item for a single service.
 * 
 * Calculates, adjusts, and formats the fee.
 * Centralizes information for the assignment.
 *
 * @since 0.1.0
 */
class LineItems {
    
    /**
     * Service object.
     * 
     * @var Service
     */
    public $service;
    
    /**
     * The ID of the service.
     * 
     * @var int
     */
    public $service_id;
    
    /**
     * Service fee.
     * 
     * @var float
     */
    public $service_fee;
    
    /**
     * Service name.
     * 
     * @var string
     */
    public $service_name;
    
    /**
     * Line item objects.
     * 
     * @var array
     */
    public $line_items;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     * 
     * @param Service
     */
    public function __construct( $args ) {
        $this->service_id       = $args['service_id'];
        $this->adjustments      = $args['adjustment_options'] ?? null;
        $this->rate_count       = $args['rate_count'] ?? null;
        $this->team_id          = $args['team_id'];
        $this->team_member_role = $args['team_member_role'] ?? null;
        $this->line_items       = [];
        
        // Create service object
        $this->service = new Service( $this->service_id );
        
        // Get service name and rate type label
        $this->service_name = $this->service->title;
        $this->unit_label = $this->service->rate_value > 0 ? '$' . $this->service->rate_value . ' ' . (new RateType ( $this->service->rate_type ))->unit_label : 'Free';
        $this->adjustment_label = '';

        // Calculate service fee
        $this->calculate_fee();
    }
    
    /**
     * Calculates service fee.
     * 
     * @since 0.1.0
     */
    private function calculate_fee() {
        
        // Get rate value from service
        $this->rate_value =  $this->service->rate_value ?? 0;
        
        // Adjust rate value and format service fee
        $this->format_fee()->adjust_fee();
        
        return $this;
    }
    
    /**
     * Adjusts fee.
     * 
     * @since 0.1.0
     */
    private function adjust_fee() {
        // Make sure adjustments exist
        if ( $this->adjustments && is_array( $this->adjustments ) ) {
            
            // Sort the adjustments
            $adjustments = $this->sort_adjustments();
            
            // Initialize names
            $adjustment_names = [];
            
            foreach ( $adjustments as $adjustment_option ) {
                
                // Skip invalid
                if ( ! $adjustment_option->validate() ) {
                    continue;
                }
                
                // Add label to names array
                $adjustment_names[] = $adjustment_option->label;
                
                switch ( $adjustment_option->operator ) {
                    case 'x':
                        $this->service_fee *= $adjustment_option->value;
                        $this->adjustment_label .= ' x ' . $adjustment_option->value;
                        break;
                    case '+':
                        $this->service_fee += $adjustment_option->value;
                        $this->adjustment_label .= ' + $' . $adjustment_option->value;
                        break;
                    case '-':
                        $this->service_fee -= $adjustment_option->value;
                        $this->adjustment_label .= ' - $' . $adjustment_option->value;
                        break;
                }
            }
            if ( ! empty( $adjustment_names ) ) {
                $this->service_name .= ' (' . implode(', ', $adjustment_names) . ')';
            }
        }
        // Remove comma
        $this->service_fee = str_replace( ',', '', $this->service_fee );
        
        // Format to two decimal places
        $this->service_fee = number_format($this->service_fee, 2);
        
        return $this;
    }
    
    /**
     * Sorts adjustments by operator.
     * 
     * @since 0.1.0
     */
    private function sort_adjustments() {
        $adjustment_objects = [];
        foreach ( $this->adjustments as $option_key ) {
            $adjustment_objects[] = new AdjustmentOption( $option_key );
        }
        
        // Define the desired order of operators
        $order = ['x', '+', '-'];
        
        // Sort $adjustment_options array by operator inline
        usort( $adjustment_objects, function( $a, $b ) use ( $order ) {
            $indexA = array_search( $a->operator, $order );
            $indexB = array_search( $b->operator, $order );
            return $indexA - $indexB;
        });
        return $adjustment_objects;
    }
    
    /**
     * Formats service fee based on rate type.
     * 
     * @since 0.1.0
     */
    private function format_fee() {
        
        // Calculate service fee based on rate type
        if ($this->service->rate_type !== 'flat') {
            $rate_value = number_format($this->rate_value, 6); // round to 6 decimal places
            $rate_value = rtrim($rate_value, '0'); // remove trailing zeroes
            $rate_value = rtrim($rate_value, '.'); // remove trailing decimal
            $this->service_fee = $rate_value * $this->rate_count;
        } else {
            $this->service_fee = number_format($this->rate_value, 2); // round to 2 decimal places
        }
        return $this;
    }
}