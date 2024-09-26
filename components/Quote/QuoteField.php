<?php
namespace BuddyClients\Components\Quote;

use BuddyClients\Includes\{
    PostQuery as PostQuery
};

/**
 * Custom quote dropdown field.
 * 
 * Generates the custom quote field on the booking form.
 * Displays valid custom quotes available to the current user.
 * 
 * @since 0.1.0
 */
class QuoteField {
    
    /**
     * Adds the quote field to the booking form.
     * 
     * @since 0.1.0
     */
    public function add_quote_field( $callbacks ) {
        // Add to the beginning of field callbacks
        array_unshift($callbacks, [$this, 'build_quote_field']);
        return $callbacks;
    }
    
    /**
     * Builds the quote field.
     * 
     * @since 0.1.0
     */
    public function build_quote_field() {

       // Initialize
        $args = [];
            
        // Get all custom quotes
        $quotes = new PostQuery( 'bc_quote', ['valid' => 'valid'] );
        
        // Check expiration and use numbers
        // Check client id
        
        // Exit if no quotes available
        if ( ! $quotes->posts ) {
            return;
        }
        
        // Initialize options
        $options = [];
        
        // Loop through quotes
        foreach ( $quotes->posts as $quote_post ) {
            
            // New quote object
            $quote = new Quote( $quote_post->ID );
            
            // Skip if invalid
            if ( ! $quote->valid ) {
          //      continue;
            }
            
            // Add to the options array
            $options['service-' . $quote->ID] = [
                'label' => $quote->title,
                'value' => $quote->ID,
                'classes' => 'service-option',
                'data_atts' => [
                    'role-id' => $quote->team_member_role,
                    'rate-type' => $quote->rate_type,
                    'dependency' => is_array($quote->dependency) ? implode(',', $quote->dependency) : '',
                    'adjustments' => is_array($quote->adjustments) ? implode(',', $quote->adjustments) : '',
                    'file-upload' => is_array($quote->file_uploads) ? implode(',', $quote->file_uploads) : '',
                    'assigned-team-member' => $freelancer ?? ($quote->assigned_team_member ?? ''),
                    'project-id' => $quote->project_id,
                    'client-id' => $quote->client_id,
                ]
            ];
        }
        
        // Build form if options are not empty
        if ( ! empty( $options ) ) {
            
            $args[] = [
                'key' => 'service-field-quote',
                'type' => 'checkbox',
                'label' => __( 'Custom Quotes', 'buddyclients' ),
                'description' => __( 'Select your custom quote.', 'buddyclients' ),
                'options' => $options,
            ];
            
        }
        return $args;
    }
    
}