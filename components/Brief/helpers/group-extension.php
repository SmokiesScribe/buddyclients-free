<?php
use BuddyClients\Components\Brief\GroupBriefs;
/**
 * Register brief group screen.
 * 
 * @since 0.1.0
 */
function brief_add_group_extension() {
	if ( bp_is_active( 'groups' ) ) :
		class Brief_Group_Extension extends BP_Group_Extension {
			function __construct() {
				$args = array(
					'slug'              => 'brief',
					'name'              =>  __( 'Project Briefs', 'buddyclients' ),
					'nav_item_position' => 200,
					'enable_nav_item'   => true,
					'screens' => array(
						'edit' => array(
							'name'      => __( 'Project Briefs', 'buddyclients' ),
						),
						'create'        => array( 'position' => 0, ),
					),
				);
				parent::init( $args );
			}
			
			function display( $group_id = NULL ) {
				$group_id = bp_get_group_id();

				$group_extension_status = groups_get_groupmeta( $group_id, 'group_extension_setting' );
				$title = '<h3 class="project-brief-title">' . __( 'Project Briefs', 'buddyclients' ) . '</h3>' . esc_attr( $group_extension_status );
                echo wp_kses_post( $title );
				
				// List project briefs
				( new GroupBriefs( $group_id ) )->build();
			}
    }
    
	bp_register_group_extension( 'Brief_Group_Extension' );
	endif;
}
add_action('bp_init', 'brief_add_group_extension');

/**
 * Group project briefs content.
 * 
 * @since 0.1.0
 */
function bc_project_briefs( $group_id ) {
    // Initialize
    $content = '';
    
    $content .= '<div class="brief-type-terms-container">';
    
    // Initialize card link
    $card_link = '#';
    
    // Get brief posts for the group
    $args = array(
        'post_type' => 'bc_brief',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'project_id',
                'value' => $group_id,
                'compare' => '=',
            ),
        ),
    );
            
    $query = new WP_Query($args);
    
    // Check if posts were found
    if ($query->have_posts()) {
        
        // Output the term card for each post
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            $updated_date = get_post_meta($post_id, 'updated_date', true);
            $card_link = get_permalink($post_id);
            $brief_type = ucfirst(implode(', ', wp_get_post_terms($post_id, 'brief_type', array('fields' => 'names'))));
            
            $icon = $updated_date ? 'complete' : 'todo';
            $icon_class = bc_brief_icon($icon);
            $click_to = $updated_date ? 'view' : 'complete';
            
            // Output the term card
            $content .= '<a class="brief-type-term-link" href="' . esc_url( $card_link ) . '">';
            $content .= '<div class="brief-type-term">';
            $content .= '<h3 style="margin-bottom: 10px;">' . sprintf(
                /* translators: %s: the brief type (e.g. Editing) */
                __('%s Brief', 'buddyclients'),
                $brief_type )
                . '</h3>';
                $content .= '<icon class="' . esc_attr( $icon_class ) . '" style="font-size: 24px; color: ' . bc_color('accent') . ';"></icon>';
                $content .= '<p>Click to ' . esc_html( $click_to ) . '.</p>';
                $content .= '</div>';
                $content .= '</a>';
        }
    
        // Reset post data after the loop
        wp_reset_postdata();
    
    } else {
        __e( 'No briefs available.', 'buddyclients' );
    }
    
    $content .= '</div>'; // Close terms container

    echo wp_kses_post( $content );
}

/**
 * Brief icons.
 * 
 * @since 0.1.0
 */
function bc_brief_icon($icon) {
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
    
    $bb = bc_buddyboss_theme() ? 'bb' : 'fa';
    
    return $classes[$icon][$bb];
}