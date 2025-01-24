<?php
namespace BuddyClients\Components\Brief;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

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
        $brief_posts = buddyc_post_query( 'buddyc_brief', ['project_id' => $this->group_id] );
        
        // Check if posts were found
        if ( $brief_posts ) {
            foreach ( $brief_posts as $brief_post ) {
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
        $content = '<div class="buddyc-group-briefs-container">';
        
        // Check if posts were found
        if ( $this->briefs && ! empty ( $this->briefs ) ) {
            
            // Output the term card for each post
            foreach ( $this->briefs as $brief ) {

                $content .= $this->single_brief( $brief );

            }
        
        } else {
            $content .= __( 'No briefs available.', 'buddyclients-free' );
        }
        
        $content .= '</div>'; // Close terms container

        echo wp_kses_post( $content );
    }

    /**
     * Builds a single brief item.
     * 
     * @since 1.0.21
     * 
     * @param   Brief   $brief  A Brief object.
     */
    private function single_brief( $brief ) {
        $complete = $brief->updated_date;
        $message = $this->brief_message( $complete );
        $header = $this->brief_header( $brief );
        
        // Output the term card
        $content = '<a href="' . get_permalink( $brief->ID ) . '">';
        $content .= '<div class="buddyc-group-brief">';
        $content .= '<h3>' . $header . '</h3>';
        $content .= $this->icon( $complete );
        $content .= '<p>' . $message . '</p>';
        $content .= '</div>';
        $content .= '</a>';

        return $content;
    }

    /**
     * Builds the message for the individual brief.
     * 
     * @since 1.0.21
     * 
     * @param   bool   $complete   Whether the brief has been submitted.
     */
    private function brief_message( $complete ) {
        $click_action = $complete ? __( 'view', 'buddyclients-free' ) : __( 'complete', 'buddyclients-free' );
        return sprintf(
            /* translators: %s: the click action (e.g. view) */
            __( 'Click to %s.', 'buddyclients-free' ),
            $click_action
        );
    }

    /**
     * Builds the brief header.
     * 
     * @since 1.0.21
     * 
     * @param   Brief   $brief   The Brief object.
     */
    private function brief_header( $brief ) {
        return __( sprintf(
            /* translators: %s: the type of brief (e.g. Design) */
            __( '%s Brief', 'buddyclients-free' ),
            $brief->brief_type_names
        ));
    }

    /**
     * Defines the icon for the individual brief.
     * 
     * @since 0.1.0
     * @since 1.0.21 use 'buddyc_icon'
     * 
     * @param   bool    $complete   Whether the brief has been submitted.
     */
    private function icon( $complete ) {
        if ( $complete ) {
            return buddyc_icon( 'checkbox' );
        } else {
            return buddyc_icon( 'square' );
        }
    }
}