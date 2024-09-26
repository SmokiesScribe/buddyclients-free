<?php
namespace BuddyClients\Components\Availability;

use BuddyClients\Includes\{
    Alert as Alert,
    Form\Form as Form,
    ProfileExtension as ProfileExtension
};

/**
 * Availability profile content.
 * 
 * Generates content for the availability profile tab.
 * Content includes the team member's current availability (public) and
 * a form to update their availability (visible only to the profile owner).
 * 
 * @since 0.1.0
 */
class AvailabilityProfile {
    
    /**
     * The link to the profile page.
     * 
     * @var string
     */
    public $link;
    
    /**
     * The ID of the displayed user.
     * 
     * @var int
     */
    public $user_id;
    
    /**
     * The availability of the user.
     * 
     * @var string
     */
    public $availability;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     */
    public function __construct() {
        if ( ! bc_component_enabled( 'Availability' ) ) {
            return;
        }
    }
    
    /**
     * Builds the profile screen content.
     * 
     * @since 0.1.0
     */
    public function build() {
        
        // Get displayed user id
        $this->user_id = bp_displayed_user_id();
        
        // Get availability
        $this->availability = Availability::get_availability( $this->user_id );
        
        // Display the current availability
        $this->display();
        
        // Display form for profile owner
        if ( ProfileExtension::is_owner() ) {
            $this->form();
        }
    }
    
    /**
     * Displays the user's current availability.
     * 
     * @since 0.1.0
     */
    private function display() {
        $availability = Availability::format( $this->availability );
        $availability = $availability === '' ? __( 'Not Set', 'buddyclients' ) : $availability;
        echo '<div class="bc-profile-availability"><p>' . __( 'Next Available: ', 'buddyclients' ) . '<strong>' . $availability . '</strong></p></div>';
    }
    
    /**
     * Builds the availability form.
     * 
     * @since 0.1.0
     */
    public function form() {
        
        // Define existing form values
        $values = [
            'available_date'  => $this->availability ?? null,
            'available_immediately' => $this->availability ?? null,
        ];

        $args = [
            'key'                   => 'availability',
            'title'                 => __( 'Update Availability', 'buddyclients' ),
            'fields_callback'       => [$this, 'form_fields'],
            'submission_class'      => __NAMESPACE__ . '\AvailabilitySubmission',
            'values'                => $values
        ];
        
        echo ( new Form( $args ) )->build();
        
    }
    
    /**
     * Builds the form fields.
     * 
     * @since 0.1.0
     * 
     * @param   ?array  $values     Optional. An array of values to populate the fields.
     */
    public function form_fields( $values = null ) {
        
       // Initialize
        $args = [];
        
        // Available Date
        $args[] = [
            'key'           => 'available_date',
            'label'         => __( 'Next Available Date', 'buddyclients' ),
            'type'          => 'date',
            'value'         => $values['available_date'],
        ];
        
        // Available Immediately Checkbox
        $options = [
            'immediately'   => [
                'label' => __( 'Immediately', 'buddyclients' ),
                'value' => 'immediately',
            ]
            
        ];
        $args[] = [
            'key'           => 'available_immediately',
            'label'         => '',
            'type'          => 'checkbox',
            'options'       => $options,
            'value'         => $values['available_immediately'],
        ];
        
        // Hidden User ID
        $args[] = [
            'key'           => 'user_id',
            'type'          => 'hidden',
            'value'         => get_current_user_id()
        ];
        
        return $args;
        
    }
}