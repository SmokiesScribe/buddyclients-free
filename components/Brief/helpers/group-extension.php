<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Components\Brief\GroupBriefs;
/**
 * Register brief group screen.
 * 
 * @since 0.1.0
 */
function buddyc_brief_add_group_extension() {
	if ( bp_is_active( 'groups' ) ) :
		class Buddyc_Brief_Group_Extension extends BP_Group_Extension {
			function __construct() {
				$args = array(
					'slug'              => 'brief',
					'name'              =>  __( 'Project Briefs', 'buddyclients-free' ),
					'nav_item_position' => 200,
					'enable_nav_item'   => true,
					'screens' => array(
						'edit' => array(
							'name'      => __( 'Project Briefs', 'buddyclients-free' ),
						),
						'create'        => array( 'position' => 0, ),
					),
				);
				parent::init( $args );
			}
			
			function display( $group_id = NULL ) {
				$group_id = bp_get_group_id();

				$group_extension_status = groups_get_groupmeta( $group_id, 'group_extension_setting' );
				$title = '<h3 class="project-brief-title">' . __( 'Project Briefs', 'buddyclients-free' ) . '</h3>' . esc_attr( $group_extension_status );
                echo wp_kses_post( $title );
				
				// List project briefs
				( new GroupBriefs( $group_id ) )->build();
			}
    }
    
	bp_register_group_extension( 'Buddyc_Brief_Group_Extension' );
	endif;
}
add_action('bp_init', 'buddyc_brief_add_group_extension');