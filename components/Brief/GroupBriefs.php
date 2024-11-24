<?php
namespace BuddyClients\Components\Brief;

use BuddyClients\Includes\PostQuery;

/**
 * Displays all project briefs.
 * 
 * Generates content for the briefs group extension.
 * Displays links to all briefs for the displayed project group.
 * 
 * @since 0.1.0
 */
class GroupBriefs {
    
    /**
     * The ID of the project group.
     * 
     * @var int
     */
    protected $group_id;
    
    /**
     * Whether the group has briefs.
     * 
     * @var bool
     */
    public $has_briefs;
    
    /**
     * Constructor method.
     * 
     * @since 0.1.0
     */
    public function __construct( $group_id ) {
        $this->group_id = $group_id;
        $this->briefs = $this->get_briefs( $group_id );
    }
    
    /**
     * Retrieves briefs for the group.
     * 
     * @since 0.4.0
     */
    private function get_briefs( $group_id ) {
        // Initialize
        $briefs = [];
        
        // Get posts
        $brief_posts = new PostQuery( 'buddyc_brief', ['project_id' => $this->group_id] );
        
        // Check if posts were found
        if ( $brief_posts->posts ) {
            foreach ( $brief_posts->posts as $brief_post ) {
                $briefs[] = new Brief( $brief_post->ID );
            }
        }
        return $briefs;
    }
    
    /**
     * Outputs the content.
     * 
     * @since 0.1.0
     */
    public function build() {
        // Initialize
        $content = '';

        $content .= '<div class="brief-type-terms-container">';
        
        // Check if posts were found
        if ( $this->briefs && ! empty ( $this->briefs ) ) {
            
            // Output the term card for each post
            foreach ( $this->briefs as $brief ) {
                $icon_class = $this->icon( $brief->updated_date ? 'complete' : 'todo' );
                
                // Build click to message
                $click_action = $brief->updated_date ? __( 'view', 'buddyclients' ) : __( 'complete', 'buddyclients' );
                $click_to_message = sprintf(
                    /* translators: %s: the click action (e.g. view) */
                    __( 'Click to %s.', 'buddyclients' ),
                    $click_action
                );
                
                // Output the term card
                $content .= '<a class="brief-type-term-link" href="' . get_permalink( $brief->ID ) . '">';
                $content .= '<div class="brief-type-term">';
                $content .= '<h3 style="margin-bottom: 10px;">' . $brief->brief_type_names . __( ' Brief', 'buddyclients' ) . '</h3>';
                $content .= '<icon class="' . $icon_class . '" style="font-size: 24px; color: ' . buddyc_color('accent') . ';"></icon>';
                $content .= '<p>' . $click_to_message . '.</p>';
                $content .= '</div>';
                $content .= '</a>';
            }
        
        } else {
            $content .= __( 'No briefs available.', 'buddyclients' );
        }
        
        $content .= '</div>'; // Close terms container

        echo wp_kses_post( $content );
    }
    
    /**
     * Defines icons.
     * 
     * @since 0.1.0
     */
    private function icon( $icon ) {
        $classes = [
            'complete' => [
                'bb' => 'bb-icon-checkbox bb-icon-l',
                'fa' => 'fa-regular fa-square-check',
            ],
            'todo' => [
                'bb' => 'bb-icon-stop bb-icon-l',
                'fa' => 'fa-regular fa-square',
            ]
        ];
        
        $bb = buddyc_buddyboss_theme() ? 'bb' : 'fa';
        
        return $classes[$icon][$bb];
    }
}