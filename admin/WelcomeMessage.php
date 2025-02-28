<?php
namespace BuddyClients\Admin;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Generates the plugin welcome message.
 * 
 * @since 1.0.25
 */
class WelcomeMessage {

    /**
     * Whether the current plugin is BuddyClients Free.
     * 
     * @var bool
     */
    private $free;

    /**
     * The name of the dismissed transient.
     * 
     * @var string
     */
    private $dismissed_trans = 'buddyc_welcome_message_dismissed';

    /**
     * Constructor method.
     * 
     * @since 1.0.25
     */
    public function __construct() {
        $this->free = defined( 'BUDDYC_PLUGIN_NAME' ) ? BUDDYC_PLUGIN_NAME === 'BuddyClients Free' : true;
        $this->define_hooks();
    }

    /**
     * Defines hooks.
     * 
     * @since 1.0.25
     */
    private function define_hooks() {
        add_action( 'admin_init', [$this, 'build'] );
    }

    /**
     * Generates the content for the welcome message.
     * 
     * @since 1.0.25
     */
    public function build() {
        // Make sure it's not dismissed
        if ( ! $this->dismissed() ) {
            return $this->output_notice();
        }
    }

    /**
     * Checks whether the welcome message has been dismissed.
     * 
     * @since 1.0.25
     */
    private function dismissed() {
        return get_transient( $this->dismissed_trans, false );
    }

    /**
     * Outputs the admin notice.
     * 
     * @since 1.0.25
     */
    private function output_notice() {
        // Output the admin notice
        $args = [
            'message'            => $this->content(),
            'dismissable'        => 'true',
            'color'              => 'green',
            'icon'               => '',
            'priority'           => 1, // show at to
            'display'            => ['dashboard']
        ];
        buddyc_admin_notice( $args );
    }

    /**
     * Generates the content for the regular welcome message.
     * 
     * @since 1.0.25
     */
    private function content() {

        // Initialize container
        $message = '<div class="buddyc-welcome-message">';

        // Heading
        $message .= $this->heading();

        // Description
        $message .= $this->description();

        // Get Started
        $message .= '<h4 class="buddyc-bot-margin-small">' . __( 'Get Started:', 'buddyclients-free' ) . '</h4>';

        // List
        $message .= $this->link_list();

        // Quick Links
        $message .= $this->quick_links();
        
        // Dismiss button
        $message .= $this->dismiss_btn();

        // Close container
        $message .= '</div>';

        // Return content
        return $message;
    }

    /**
     * Builds the heading.
     * 
     * @since 1.0.25
     */
    private function heading() {
        return '<h2 class="buddyc-bot-margin-small">🎉 ' . __( 'Welcome to BuddyClients!', 'buddyclients-free' ) . '</h2>';
    }

    /**
     * Builds the description.
     * 
     * @since 1.0.25
     */
    private function description() {
        // Initialize
        $content = '<p class="buddyc-bot-margin-med">';

        // Regular description
        if ( ! $this->free ) {
            $content .= sprintf(
                /* translators: %s: the name of the plugin (BuddyClients) */
                __( 'Congratulations on upgrading to %s! We\'re excited to have you using the full version of our plugin, packed with even more features to help your business thrive.', 'buddyclients-free' ),
                '<strong>BuddyClients</strong>'
            );

        // Free description
        } else {
            $content .= __( 'We\'re excited to help you grow your business.', 'buddyclients-free' );
        }

        // Close paragraph
        $content .= '</p>';

        // Return content
        return $content;
    }

    /**
     * Generates the link list.
     * 
     * @since 1.0.25
     */
    private function link_list() {

        // Build all items
        $items = [
            'user_guides' => [
                'icon'          => 'key',
                'url'           => admin_url( 'admin.php?page=buddyc-license-settings' ),
                'target'        => '_blank',
                'link_text'     => __( 'Enter your license key', 'buddyclients-free' ),
                'follow_text'   => __( 'to activate premium features', 'buddyclients-free' ),
                'free'          => false,
                'regular'       => true,
            ],
            'add_website' => [
                'icon'          => 'globe',
                'url'           => trailingslashit( BUDDYC_URL ) . 'license',
                'target'        => '_blank',
                'link_text'     => __( 'Add this website', 'buddyclients-free' ),
                'follow_text'   => __( 'to your BuddyClients account', 'buddyclients-free' ),
                'free'          => false,
                'regular'       => true,
            ],
            'update_components' => [
                'icon'          => 'gear',
                'url'           => admin_url( 'admin.php?page=buddyc-license-settings' ),
                'target'        => false,
                'link_text'     => __( 'Update components', 'buddyclients-free' ),
                'follow_text'   => __( 'to ensure all features are enabled', 'buddyclients-free' ),
                'free'          => false,
                'regular'       => true,
            ],
            'explore_guides' => [
                'icon'          => 'circle-info',
                'url'           => trailingslashit( BUDDYC_URL ) . 'help',
                'target'        => false,
                'link_text'     => __( 'Explore user guides', 'buddyclients-free' ),
                'follow_text'   => __( 'to get the most out of BuddyClients', 'buddyclients-free' ),
                'free'          => true,
                'regular'       => true,
            ],
            'upgrade' => [
                'icon'          => 'rocket',
                'url'           => trailingslashit( BUDDYC_URL ) . 'pricing',
                'target'        => false,
                'link_text'     => __( 'Upgrade BuddyClients', 'buddyclients-free' ),
                'follow_text'   => __( 'to accept payments and enable premium features.', 'buddyclients-free' ),
                'free'          => true,
                'regular'       => false,
            ],
        ];

        // Initialize
        $content = '<ul>';

        // Build items based on plugin type
        $filtered_items = [];
        foreach ( $items as $key => $data ) {
            if ( $this->free && $data['free'] || ! $this->free && $data['regular'] ) {
                $filtered_items[$key] = $data;
            }
        }

        // Build content list content
        foreach ( $filtered_items as $key => $data ) {
            $content .= self::link_list_item( $data );
        }

        // Close list
        $content .= '</ul>';

        // Return html
        return $content;
    }

    /**
     * Generates a single item for the link list.
     * 
     * @since 1.0.25
     * 
     * @param   array   $args {
     *     The array of args to build the item.
     * 
     *     @string      $icon           The final part of the icon class.
     *     @string|bool $target         The target attribute or false.
     *     @string      $url            The url for the link. 
     *     @string      $link_text      The text for the link. 
     *     @string      $follow_text    The text following the link.
     * }
     */
    private static function link_list_item( $args ) {
        $target_string = isset( $args['target'] ) && $args['target'] ? sprintf( ' target="%s"', esc_attr( $args['target'] ) ) : '';

        $content = '<ul class="buddyc-no-list">';
        $content .= '<li class="buddyc-bot-margin-small">';
        $content .= sprintf(
            '<i class="fa-solid fa-%1$s"></i> <strong><a href="%2$s"%3$s>%4$s</a></strong> %5$s. ',
            $args['icon'] ?? '',
            esc_url( $args['url'] ?? '#' ),
            $target_string,
            esc_html( $args['link_text'] ?? '' ),
            esc_html( $args['follow_text'] ?? '' )
        );
        $content .= '</li>';
        return $content;
    }

    /**
     * Generates the quick links.
     * 
     * @since 1.0.25
     */
    private function quick_links() {

        // Define links
        $links = [
            'settings' => [
                'link'      => admin_url( 'admin.php?page=buddyc-general-settings' ),
                'text'      => __( 'Settings', 'buddyclients-free' ),
                'target'    => false,
                'free'      => true,
                'regular'   => true,
            ],
            'user_guides' => [
                'link'      => trailingslashit( BUDDYC_URL ) . 'help',
                'text'      => __( 'User Guides', 'buddyclients-free' ),
                'target'    => '_blank',
                'free'      => true,
                'regular'   => true,
            ],
            'account' => [
                'link'      => trailingslashit( BUDDYC_URL ) . 'license',
                'text'      => __( 'Account', 'buddyclients-free' ),
                'target'    => '_blank',
                'free'      => false,
                'regular'   => true,
            ],
            'support' => [
                'link'      => trailingslashit( BUDDYC_URL ) . 'support',
                'text'      => __( 'Support', 'buddyclients-free' ),
                'target'    => '_blank',
                'free'      => true,
                'regular'   => true,
            ],
            'upgrade' => [
                'link'      => buddyc_upgrade_url(),
                'text'      => __( 'Upgrade', 'buddyclients-free' ),
                'target'    => '_blank',
                'free'      => true,
                'regular'   => true,
            ],
        ];

        // Build links
        $formatted_links = [];
        foreach ( $links as $key => $data ) {
            // Make sure the plugin type matches
            if ( $this->free && $data['free'] || ! $this->free && $data['regular'] ) {
                $target = isset( $data['target'] ) && $data['target'] ? ' target="' . esc_attr( $data['target'] ) . '"' : '';
                $formatted_links[] = sprintf(
                    '<a href="%1$s"%2$s>%3$s</a>',
                    $data['link'],
                    $target,
                    $data['text']
                );
            }
        }

        // Build content
        $content = '<div class="buddyc-quick-links">';
        $content .= '<span class="buddyc-quick-links-title">' . __( 'Quick Links:', 'buddyclients-free' ) . '</span>';
        $content .= implode( ' | ', $formatted_links );
        $content .= '</div>';
        return $content;
    }

    /**
     * Builds the dismiss button. 
     * 
     * @since 1.0.25
     */
    private function dismiss_btn() {
        $content = '<div class="buddyc-dismiss-welcome-btn-container">';
        $content .= '<a class="buddyc-dismiss-welcome-btn" href="' . esc_url( admin_url( 'admin-post.php?action=buddyc_dismiss_welcome_message' ) ) . '">';
        $content .= esc_html__( 'Dismiss this message', 'buddyclients-free' ) . '</a>';
        $content .= '</div>';
        return $content;
    }

    /**
     * Dismisses the welcome notice.
     * 
     * @since 1.0.25
     */
    public function dismiss() {
        set_transient( $this->dismissed_trans, true, 7 * DAY_IN_SECONDS ); // 7 days
        wp_redirect( wp_get_referer() ); // Redirect back to the referring page
        exit;
    }
}