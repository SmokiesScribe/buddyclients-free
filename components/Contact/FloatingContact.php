<?php
namespace BuddyClients\Components\Contact;

/**
 * Floating contact button.
 * 
 * Generates the floating contact button.
 * Includes the live search and contact form based on settings.
 *
 * @since 0.1.0
 */
class FloatingContact {
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     */
    public function __construct() {
        if ( ! bc_component_enabled( 'Contact' ) ) {
            return;
        }
        // Hook the button to the footer
        $this->define_hooks();
    }
    
    /**
     * Defines the hooks.
     * 
     * @since 0.1.0
     */
    private function define_hooks() {
        add_action( 'wp_footer', [$this, 'display'] );
    }
    
    /**
     * Displays the button.
     * 
     * @since 0.1.0
     */
    public function display() {
        //ob_start();
        
        // Exit if the page content includes the contact shortcode
        $page_content = get_the_content();
        if ( strpos( '[bc_contact_form]', $page_content ) !== false ) {
            return;
        }
        
        // Get setting
        $popup_content = bc_get_setting( 'help', 'help_popup_content' );
        
        // Define content
        switch ( $popup_content ) {
            case 'help_only': 
                $content = $this->search_content();
                break;
            case 'contact_only': 
                $content = ( new ContactForm() )->build();
                break;
            default:
                $content = $this->search_content();
                $content .= ( new ContactForm() )->build();
        }
        
        // Check for BB theme
        if (bc_buddyboss_theme()) {
            $size_class = 'floating-contact-button-50';
            $icon = '<i style="font-size: 35px" class="bb-icon-question bb-icon-f"></i>';
        } else {
            $size_class = 'floating-contact-button-30';
            $icon = '<i style="font-size: 25px" class="fa-solid fa-question"></i>';
        }
        
        ?>
        <!-- Floating Contact Button -->
        <div class="floating-contact-button <?php echo $size_class ?>" id="floating-contact-btn">
            <?php echo $icon ?>
        </div>
    
        <!-- Contact Popup -->
        <div class="contact-popup" id="contact-popup">
            <?php echo $content ?>
            <a id="close-bc-pop-up-button" href=""><i class="fa-solid fa-x"></i></a>
        </div>
        <?php
        //return ob_get_clean();
    }
    
    /**
     * Generates the search html.
     * 
     * @since 0.1.0
     */
    private function search_content() {
        ob_start(); ?>
        
        <div id="ajax-docs-search-container">
            <input type="text" id="ajax-docs-search" placeholder="<?php echo esc_attr( __('Search for Answers', 'buddyclients') ); ?>" autocomplete="off">
            <div id="search-results"></div>
        </div>
        
        <?php
        return ob_get_clean();
    }
}