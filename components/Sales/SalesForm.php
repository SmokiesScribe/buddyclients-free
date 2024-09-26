<?php
namespace BuddyClients\Components\Sales;

use BuddyClients\Includes\{
    PostQuery as PostQuery,
    Form\Form as Form
};

/**
 * Form for sales team and manual bookings.
 * 
 * Generates a form to initialize a manual/assisted booking.
 * Allows users to transfer PIF bookings from other sources.
 * 
 * @since 0.1.0
 */
class SalesForm {
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     */
    public function __construct() {
        // Define submit text
        $this->form_submit_text();
        
        // Define hooks
        $this->define_hooks();
    }
    
    /**
     * Defines hooks and filters.
     *
     * @since 1.0.0
     */
    private function define_hooks() {
        // Booking form
        add_filter('bc_before_booking_form', [$this, 'sales_content'], 10, 1);
        add_filter( 'bc_booking_submit_text', [$this, 'filter_form_submit_text'], 10, 1 );
        
        // Registration form
        add_filter('be_before_registration_form', [$this, 'sales_content'], 10, 1);
        add_filter( 'be_registration_submit_text', [$this, 'filter_form_submit_text'], 10, 1 );

        // Sponsor form
        add_filter('be_before_sponsor_form', [$this, 'sales_content'], 10, 1);
        add_filter( 'be_sponsor_submit_text', [$this, 'filter_form_submit_text'], 10, 1 );
    }
    
    /**
     * Adds the sales content to the booking page.
     * 
     * @since 0.1.0
     */
    public function sales_content( $content ) {
        
        // Make sure sales bookings are enabled
        if ( ! bc_sales_enabled() ) {
            return $content;
        }
        
        // Make sure the user is team member or admin
        if ( bc_is_sales() || bc_is_admin() ) {
            $content .= $this->sales_message();
            $content .= $this->sales_form();
        }
        return $content;
    }
    
    /**
     * Generates the current sales booking message.
     * 
     * @since 0.1.0
     */
    public function sales_message() {
        if ( isset( $_GET['sales_id'] ) ) {
            $sales_id = $_GET['sales_id'];
            $client_id = $_GET['sales_client_id'];
            $client_email = $_GET['sales_client_email'];
            
            $client_name = $client_id ? bp_core_get_user_displayname( $client_id ) : __( 'a new client', 'buddyclients' );
            $sales_name = bp_core_get_user_displayname( $sales_id );
            
            return sprintf(
                '<div style="max-width: 650px; margin: auto"><h4>' . __( '%s is currently booking services for %s.', 'buddyclients' ) . '</h4></div>',
                esc_html( $sales_name ),
                esc_html( $client_name )
            );
        }
    }
    
    /**
     * Defines the form submit text.
     * 
     * @since 0.1.0
     * 
     * @param   string  $submit_text    The booking form submit text.
     */
    public function form_submit_text() {
        if ( isset( $_GET['sales_id'] ) ) {
            $client_email = $_GET['sales_client_email'] ?? __( 'Client', 'buddyclients' );
            
            // Define text
            $this->submit_text = sprintf(
                __( 'Create booking and notify %s', 'buddyclients' ),
                esc_html( $client_email )
            );
        }
    }
    
    /**
     * Filters the form submit text.
     * 
     * @since 0.1.0
     * 
     * @param   string  $submit_text    The booking form submit text.
     */
    public function filter_form_submit_text( $submit_text ) {
        return $this->submit_text ?? $submit_text;
    }
    
    /**
     * Generates the sales form.
     * 
     * @since 0.1.0
     */
    public function sales_form() {
        if ( ! isset( $_GET['sales_id'] ) ) {
            // Define form args
            $args = [
                'key'                   => 'sales',
                'fields_callback'       => [$this, 'form_fields'],
                'submission_class'      => __NAMESPACE__ . '\SalesFormSubmission',
                'title'                 => __( 'Start a Booking for a Client', 'buddyclients' ),
                'submit_text'           => __( 'Start Booking', 'buddyclients' ),
            ];
            
            return (new Form( $args ) )->build();
        }
    }
    
    /**
     * Defines the args for the form fields.
     * 
     * @since 0.1.0
     */
    public function form_fields() {
        
        // Initialize
        $args = [];

        // Build client options
        $client_options = [
            '' => [
                'label' => __( 'New Client', 'buddyclients' ),
                'value' => ''
            ]
        ];
        
        $all_clients = bp_core_get_users(['per_page' => false, 'type' => 'alphabetical']);
        foreach ( $all_clients['users'] as $client ) {
            $client_options[$client->ID] =[
                'label' => $client->fullname . ' (' . $client->user_nicename . ')',
                'value' => $client->ID,
                'data_atts' => [
                    'email' => $client->user_email,
                ]
            ];
        }
        
        // Client dropdown
        $args[] = [
            'key' => 'sales_client_id',
            'type' => 'dropdown',
            'label' => __( 'Select a Client', 'buddyclients' ),
            'options' => $client_options,
        ];
        
        // Client email
        $args[] = [
            'key' => 'sales_client_email',
            'type' => 'email',
            'label' => __( 'Client Email', 'buddyclients' ),
            'placeholder' => 'example@example.com',
        ];
        
        // Previously paid checkbox
        $args[] = [
            'key' => 'previously_paid',
            'type' => 'checkbox',
            'label' => __( 'Manual Booking', 'buddyclients' ),
            'options'   => [
                'paid' => [
                    'label' => __( 'This booking has already been paid in full.', 'buddyclients' ),
                    'value' => 'paid'
                ]
            ]
        ];
        
        // Sales ID
        $args[] = [
            'key' => 'sales_id',
            'type' => 'hidden',
            'value' => get_current_user_id()
        ];
        
        return $args;
    }
    
}