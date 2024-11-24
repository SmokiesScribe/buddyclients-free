<?php
namespace BuddyClients\Components\Checkout;

use BuddyEvents\Includes\Registration\RegistrationIntent;
use BuddyEvents\Includes\Sponsor\SponsorIntent;

use BuddyClients\Includes\PostQuery;
use BuddyClients\Includes\Project;
use BuddyClients\Components\Brief\GroupBriefs;
use BuddyClients\Components\Booking\BookingIntent;
use BuddyClients\Components\Checkout\IntentHandler;

/**
 * Confirmation page content.
 * 
 * Generates content for the page where the client is redirected after checkout.
 * Displays a success message with a link to the project group or an error message.
 *
 * @since 0.1.0
 */
class Confirmation {
    
    /**
     * Booking intent.
     * 
     * @var BookingIntent
     */
    public $booking_intent;
    
    /**
     * Whether it's a registration checkout.
     * 
     * @var bool
     */
    public $is_registration;
    
    /**
     * Whether it's a sponsorship checkout.
     * 
     * @var bool
     */
    public $is_sponsor;
    
    /**
     * The link to the checkout page.
     * 
     * @var string
     */
    public $checkout_page_link;
    
    /**
     * The link to the contact page.
     * 
     * @var string
     */
    public $contact_page_link;
    
    /**
     * Whether it's a free checkout.
     * 
     * @var bool
     */
    private $free;
    
    /**
     * The status of the checkout.
     * 
     * Accepts 'succeeded' and 'failed'.
     * 
     * @var string
     */
    private $status;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     */
    public function __construct() {
        @session_start();
        
        // Retrieve the booking intent from the session data
        $this->get_booking_intent();
        
        // Check redirect status
        $this->get_status();
        
        // Get links
        $this->checkout_page_link = buddyc_get_page_link( 'checkout_page' );
        $this->contact_page_link = buddyc_get_page_link( 'contact' );
    }
    
    /**
     * Defines the purchase form link.
     * 
     * @since 0.4.0
     */
    private function define_form_link() {
        // Registration form
        if ( $this->is_registration ) {
            return buddyc_get_page_link( 'registration' );
            
        // Sponsorship form
        } else if ( $this->is_sponsor ) {
            return buddyc_get_page_link( 'sponsor_form' );
            
        // Booking form
        } else {
            return buddyc_get_page_link( 'booking_page' );
        }
    }
    
    /**
     * Retrieves the redirect status from the URL.
     * 
     * @since 0.4.0
     */
    private function get_status() {
        // Initialize to failed
        $this->status = 'failed';

        // Fetch redirect status param
        $redirect_status = buddyc_get_param( 'redirect_status' );
        
        // Check if redirect_status is 'succeeded'
        if ( $redirect_status === 'succeeded' ) {
            
            // Set to succeeded
            $this->status = 'succeeded';
            
            // Check if free
            $free = buddyc_get_param( 'free' );
            $this->free = $free === 'true';
        }
    }
    
    /**
     * Retrieves the BookingIntent, RegistrationIntent, or SponsorIntent.
     * 
     * @since 0.4.0
     */
    private function get_booking_intent() {
        // Init IntentHandler
        $intent_handler = new IntentHandler;

        // Fetch intent object
        $this->booking_intent = $intent_handler->intent;

        // Check intent type
        $this->is_registration = $intent_handler->intent_type === 'registration';
        $this->is_sponsor = $intent_handler->intent_type === 'sponsor';
    }
    
    /**
     * Builds checkout page.
     * 
     * @since 0.1.0
     * @updated 0.4.0
     */
    public function build() {
        $this->destroy_session();
        
        // No session data set
        if ( ! $this->booking_intent ) {
            return $this->profile_link();
        }
        
        // Registration intent
        if ( $this->is_registration ) {
            return $this->registration_confirmation();
        
        // Sponsor intent
        } else if ( $this->is_sponsor ) {
            return $this->sponsor_confirmation();
        
        // Booking intent
        } else {
            return $this->booking_confirmation();
        }
    }
    
    /**
     * Destroys the session and re-establishes the affiliate ID.
     * 
     * @since 0.4.3
     */
    private function destroy_session() {
        $affiliate_id = isset( $_SESSION['affiliate_id'] ) ? intval( wp_unslash( $_SESSION['affiliate_id'] ) ) : null;
        session_destroy();
        if ( $affiliate_id ) {
            $_SESSION['affiliate_id'] = $affiliate_id;
        }
    }
    
    /**
     * Outputs a profile link when no booking intent is set.
     * 
     * @since 0.4.0
     */
    private function profile_link() {
        $profile_url = bp_loggedin_user_domain();
        return '<a href="' . $profile_url . '">' . __( 'View your profile.', 'buddyclients' ) . '</a>';
    }
    
    /**
     * Outputs the registration confirmation content.
     * 
     * @since 0.4.0
     */
    private function registration_confirmation() {
        // Build message
        return $this->status === 'succeeded' ? $this->registration_success_message() : $this->failure_message();
    }
    
    /**
     * Outputs the registration success message.
     * 
     * @since 0.1.0
     */
    private function registration_success_message() {
        // Free or successful payment
        $content = $this->free ? '<p>' . __( 'Your registration has been confirmed.', 'buddyclients' ) . '</p>' : '<p>' . __( 'Your payment has been processed, and your registration has been confirmed.', 'buddyclients' ) . '</p>';
        
        // Build button
        $link = trailingslashit( bp_loggedin_user_domain() ) . 'event';
        $link_text = __( 'View Your Event Info', 'buddyclients' );
        
        return $this->output_message( 'success', __( 'Success!', 'buddyclients' ), $content, $link, $link_text );
    }
    
    /**
     * Outputs the sponsor confirmation content.
     * 
     * @since 0.4.0
     */
    private function sponsor_confirmation() {
        // Build message
        return $this->status === 'succeeded' ? $this->sponsor_success_message() : $this->failure_message();
    }
    
    /**
     * Outputs the sponsor success message.
     * 
     * @since 0.1.0
     */
    private function sponsor_success_message() {
        // Free or successful payment
        $content = $this->free 
            ? '<p>' . __('Your sponsorship purchase has been confirmed.', 'buddyclients') . '</p>' 
            : '<p>' . __('Your payment has been processed, and your sponsorship purchase has been confirmed.', 'buddyclients') . '</p>';
        
        $content .= '<p>' . __('We will review your information and be in touch with any questions.', 'buddyclients') . '</p>';
        
        // Build button
        $link = buddyc_profile_link(['slug' => 'event?subnav=sponsor']);
        $link_text = __('View Your Sponsorships', 'buddyclients');
        
        return $this->output_message('success', __('Success!', 'buddyclients'), $content, $link, $link_text);
    }
    
    /**
     * Outputs the booking confirmation content.
     * 
     * @since 0.4.0
     */
    private function booking_confirmation() {
        
        $project = new Project( $this->booking_intent->project_id );

        // Build message
        return $this->status === 'succeeded' ? $this->booking_success_message( $project ) : $this->failure_message();
    }
    
    /**
     * Checks whether briefs are enabled and the project has briefs.
     * 
     * @since 0.4.3
     * 
     * @return  bool
     */
    private function has_briefs() {
        if ( ! class_exists( GroupBriefs::class ) ) {
            return false;
        }
        
        $group_briefs = new GroupBriefs( $this->booking_intent->project_id );
        
        return $group_briefs->has_briefs ?? false;
    }
    
    /**
     * Outputs the booking success message.
     * 
     * @since 0.1.0
     * 
     * @param   Project     $project        The Project object.
     * @param   GroupBriefs $group_briefs   The GroupBriefs object.
     */
    private function booking_success_message( $project ) {
        // Initialize
        $content = '';
        $link = null;
        $link_text = null;
        
        // Check if briefs exist
        $has_briefs = $this->has_briefs();
        
        // Free or successful payment
        $content .= $this->free 
            ? '<p>' . __('Your services have been booked.', 'buddyclients') . '</p>' 
            : '<p>' . __('Your payment has been processed, and your services have been booked.', 'buddyclients') . '</p>';
        
        // Project exists
        // @TODO Currently, success functions called by Stripe endpoint, which means project is not available here for paid checkouts
        if ( $project && $project->ID != 0 ) {
        
            // Briefs message
            $content .= $has_briefs 
                ? '<p><strong>' . __('What\'s next?', 'buddyclients') . '</strong> ' . __('Complete the briefs for ', 'buddyclients') . $project->name . ' ' . __('to make sure your team has all the information they need.', 'buddyclients') . '</p>' 
                : '';
            
            // Build button
            $link = $has_briefs ? trailingslashit($project->permalink) . 'brief' : $project->permalink;
            $link_text = $has_briefs ? __('View Your Briefs', 'buddyclients') : __('View Your Project', 'buddyclients');
            
        // No project available - link to groups
        } else if (is_user_logged_in()) {
            // Build button
            $link = buddyc_profile_link(['slug' => 'groups']);
            $link_text = __('View Your Projects', 'buddyclients');
            
        // Not logged in
        } else {
            $link = wp_login_url();
            $link_text = __('Log In to View Your Projects', 'buddyclients');
        }
        
        return $this->output_message('success', __('Success!', 'buddyclients'), $content, $link, $link_text);
    }
    
    /**
     * Outputs the failure message.
     * 
     * Generates a general failure message for all checkout types.
     * 
     * @since 0.1.0
     */
    private function failure_message() {
        // Initialize
        $content = '';
        
        // Failed
        $content .= '<p>' . __('Your payment failed.', 'buddyclients') . '</p>';
        
        $content .= $this->checkout_page_link !== '#' 
            ? '<a href="' . esc_url($this->checkout_page_link) . '"><button class="confirmation-page-button buddyc-button">' . __('Return to Checkout', 'buddyclients') . '</button></a>' 
            : '';
        
        $content .= $this->contact_page_link !== '#' 
            ? '<p>' . __('If you continue to have issues, please <a href="', 'buddyclients') . esc_url($this->contact_page_link) . '">' . __('contact us', 'buddyclients') . '</a>.</p>' 
            : '';
        
        return $this->output_message('failure', __('Uh oh!', 'buddyclients'), $content);
    }
    
    /**
     * Outputs the formatted confirmation message html.
     * 
     * @since 0.1.0
     * 
     * @param   string      $type       The type of message.
     *                                  Accepts 'success' and 'failure'.
     * @param   string      $header     The header text.
     * @param   string      $content    The content of the message.
     * @param   string      $link       Optional. The permalink for the button.
     * @param   string      $link_text  Optional. The text for the button.
     */
    private function output_message( $type, $header , $content, $link = null, $link_text = null ) {
        $message = '<div class="buddyc-confirmation-container payment-' . $type . '-message">';
        $message .= '    <h1>' . $header . '</h1>';
        $message .= '<div class="buddyc-confirmation-content">' . $content . '</div>';
        if ( $link && $link_text ) {
            $message .= '<div><a class="buddyc-confirmation-button" href="' . $link . '">' . $link_text . '</a></div>';
        }
        $message .= '</div>';
        return $message;
    }
}