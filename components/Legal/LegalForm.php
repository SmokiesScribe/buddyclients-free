<?php
namespace BuddyClients\Components\Legal;

use BuddyClients\Includes\{
    Form\Form as Form,
    Popup as Popup,
    PDF as PDF
};

/**
 * Legal agreement form content.
 * 
 * Generates a legal agreement form with a signature field.
 * 
 * @since 0.1.0
 */
class LegalForm {
    
    /**
     * Type of Legal agreement.
     * 
     * @var string
     */
    public $type;
    
    /**
     * Legal instance.
     * 
     * @var Legal
     */
    private $legal;
    
    /**
     * User legal data.
     * 
     * @var array
     */
    private $user_data;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     * 
     * $param   string  $type       Type of Legal agreement.
     *                              Accepts 'team', 'client', 'affiliate'.
     * @param   array   $fields     Optional. An array of optional fields to include.
     *                              Accepts 'email' and 'payment'. Defaults to empty array.
     */
    public function __construct( $type, $fields = [] ) {
        $this->type = $type;
        $this->fields = $fields;
        
        // New legal instance
        $this->legal = new Legal( $type );
        $this->user_data = $this->legal->get_user_data();
    }
    
    /**
     * Builds the content.
     * 
     * @since 0.1.0
     */
    public function build() {
        if ( ! $this->legal->curr_version ) {
            $message = '<p>' . __( 'The agreement is unavailable at this time.', 'buddyclients' ) . '</p>';
            $message .= '<p>' . __( 'Please', 'buddyclients' ) . ' ' . bc_contact_message( false, true ) . ' ' . __( 'for more information.', 'buddyclients' ) . '</p>';
            return $message;
        }
        
        // Initialize
        $output = '';
        $form_args = [];
        
        // Open the container
        $output .= '<div class="bc-legal-container" style="max-width: 650px; margin: auto">';

        // Check if the user's agreement is up to date
        $this->status = $this->user_data['status'];
        
        // Check the user's agreement status
        if ( $this->status ) {
            
            // Agreement is current
            if ( $this->status === 'current' ) {
                $output .= $this->current_message();
                $form_args['title'] = '<h3>' . __( 'Update Your Information', 'buddyclients' ) . '</h3>';
                
            // New version is available
            } else if ( $this->status === 'active' ) {
                $output .= $this->transition_message();
            }
            
        // Never completed the form
        } else {
            $form_args['title'] = sprintf(
                __( 'Review the %s Agreement', 'buddyclients' ),
                ucwords( $this->type )
            );
        }
        
        // Close the container
        $output .= '</div>';
        
        // Always output the form
        $output .= $this->form( $form_args );
        
        return $output;
    }
    
    /**
     * Echoes the form.
     * 
     * @since 0.1.0
     */
    public function echo_form() {
        echo $this->build();
    }
    
    /**
     * Displays content when the user's agreement is up to date.
     * 
     * @since 0.1.0
     */
    private function current_message() {
        // Initialize
        $output = '';
    
        // Build raw agreement content
        $signature_url = str_replace( ABSPATH, trailingslashit( site_url() ), $this->user_data['signature_file_path'] );
        $raw_content =  '<h1>' . ucfirst( $this->type ) . ' ' . __( 'Agreement', 'buddyclients' ) . '</h1>' . 
                        $this->user_data['content'] . // content
                        '<div><img style="max-width: 300px" src="' . $signature_url . '"></div>' .          // signature
                        '<p>' . $this->user_data['name'] . '</p>' .                                         // legal name
                        '<p>' . date('F j, Y', $this->user_data['date'] ) . '</p>';                         // date signed
    
        // Define items to add
        $items = [
            '<h3>' . sprintf( __( 'Your %s agreement is up to date.', 'buddyclients' ), ucfirst( $this->type ) ) . '</h3>', // Heading
            PDF::download_link( $this->user_data['pdf'] ), // Download PDF
            (new Popup )->link( null, sprintf( __( 'View your %s agreement.', 'buddyclients' ), ucfirst( $this->type ) ), null, $raw_content ), // View link
            '<div style="max-width: 300px">' . $this->user_data['signature_image'] . '</div>', // Signature image
            sprintf( __( '%s<br>%s', 'buddyclients' ), $this->user_data['name'], date('F j, Y', $this->user_data['date'] ) ) // Name and date
        ];
    
        // Loop through items
        foreach ( $items as $item ) {
            $output .= '<p>' . $item . '</p>';
        }
    
        return $output;
    }

    /**
     * Displays a message when there is a new agreement to complete.
     * 
     * @since 0.1.0
     */
    private function transition_message() {
        return sprintf(
            __( 'Complete the new %s agreement by %s.', 'buddyclients' ),
            ucfirst( $this->type ),
            $this->legal->deadline
        );
    }
    
    /**
     * Builds the form.
     * 
     * @since 0.1.0
     * 
     * @param   array   $args
     *     @type    string  $title  The form title.
     *     @type    array   $values Optional. An array of values to populate the field.
     */
    public function form( $args ) {
        
        // Define existing form values
        $values = [
            'legal_name'  => $this->user_data['name'] ?? null,
            'legal_email' => $this->user_data['email'] ?? null,
            'payment_method' => $this->user_data['payment_method'] ?? null,
        ];
        
        $args = [
            'key'                   => $this->type . '_legal',
            'title'                 => $args['title'] ?? null,
            'fields_callback'       => [$this, 'form_fields'],
            'submission_class'      => __NAMESPACE__ . '\LegalSubmission',
            'values'                => $values
        ];
        
        return ( new Form( $args ) )->build();
    }
    
    /**
     * Creates the form field args.
     * 
     * @since 0.1.0
     * 
     * @param   array   $values     Optional. An array of values to populate the field.
     */
    public function form_fields( $values = null ) {
        
        // Initialize
        $args = [];
        
        // Legal Name
        $args[] = [
            'key'           => 'legal_name',
            'label'         => __( 'Legal Name', 'buddyclients' ),
            'type'          => 'text',
            'value'         => $this->user_data['name'] ?? bp_core_get_user_displayname( $this->user_data['user_id'] ),
        ];
        
        // Legal Email
        if ( in_array( 'email', $this->fields ) ) {
            $args[] = [
                'key'           => 'legal_email',
                'label'         => __( 'Payment Email', 'buddyclients' ),
                'type'          => 'email',
                'value'         => $this->user_data['email'] ?? bp_core_get_user_email( $this->user_data['user_id'] ),
            ];
        }
                
        // Payment Preference
        if ( in_array( 'payment', $this->fields ) && ! empty( $this->payment_methods ) ) {
            $args[] = [
                'key'           => 'payment_method',
                'label'         => __( 'Payment Method', 'buddyclients' ),
                'description'   => __( 'Please select your preferred payment method.', 'buddyclients' ),
                'type'          => 'dropdown',
                'value'         => $this->user_data['payment_method'] ?? bp_core_get_user_email( $this->user_data['payment_method'] ),
                'options'       => $this->payment_methods()
            ];
        }
        
        // Make sure we're not updating details
        if ( $this->status !== 'current' ) {
            
            $args[] = [
                'key'           => 'agreement_content',
                'type'          => 'display',
                    'label'     => sprintf(
                            __( '%s Agreement', 'buddyclients' ),
                            ucwords( $this->type )
                    ),
                    'value'     => $this->legal->content
            ];

            // Signature
            $args[] = [
                'key'           => 'agreement_signature',
                'type'          => 'signature'
            ];
            
            // Date
            $args[] = [
                'key'           => 'agreement_date',
                'type'          => 'display',
                'value'         => date( 'F j, Y')
            ];
        }
        
        // Hidden Type Field
        $args[] = [
            'key'           => 'legal_type',
            'type'          => 'hidden',
            'value'         => $this->type
        ];
        
        // Hidden Version Field
        $args[] = [
            'key'           => 'version',
            'type'          => 'hidden',
            'value'         => $this->legal->curr_version
        ];
        
        // Hidden User Status Field
        $args[] = [
            'key'           => 'user_status',
            'type'          => 'hidden',
            'value'         => $this->status
        ];
        
        // Hidden User ID
        $args[] = [
            'key'           => 'user_id',
            'type'          => 'hidden',
            'value'         => $this->user_data['user_id']
        ];
        
        return $args;
    }
    
    /**
     * Retrieves payment method options.
     * 
     * @since 0.1.0
     */
    private function payment_methods() {
        
        // Initialize
        $options = [];
        
        // Get all options from helper
        $all_options = bc_payment_method_options();
        
        // Get allowed options from settings
        $allowed_options = bc_get_setting( $this->type, 'payment_options');
        
        if ( ! is_array( $allowed_options ) ) {
            return;
        }
        
        // Loop through all options
        if ( $all_options && is_array( $all_options ) ) {
            foreach ( $all_options as $key => $label ) {
                
                // Check if the option is allowed
                if ( in_array( $key, $allowed_options ) ) {
                    
                    // Add to the array
                    $options[$key] = [
                        'value' => $key,
                        'label' => $label,
                    ];
                }
            }
        }
        return $options;
    }
    
}