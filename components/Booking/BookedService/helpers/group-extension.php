<?php
use BuddyClients\Components\Booking\BookedService\BookedServiceList;
/**
 * Register services group screen.
 * 
 * @since 0.1.0
 */
function bc_services_add_group_extension() {
	if ( bp_is_active( 'groups' ) ) :
		class Services_Group_Extension extends BP_Group_Extension {
			function __construct() {
				$args = array(
					'slug'              => 'services',
					'name'              =>  __( 'Project Services', 'buddyclients' ),
					'nav_item_position' => 200,
					'enable_nav_item'   => true,
					'screens' => array(
						'edit' => array(
							'name'      => __( 'Project Services', 'buddyclients' ),
						),
						'create'        => array( 'position' => 0, ),
					),
				);
				parent::init( $args );
			}
			
			function display( $group_id = NULL ) {
				$group_id = bp_get_group_id();

				$group_extension_status = groups_get_groupmeta( $group_id, 'group_extension_setting' );
				echo '<h3 class="project-brief-title">' . __( 'Project Services', 'buddyclients' ) . '</h3>' . esc_attr( $group_extension_status );
				
				// List project briefs
			    $list = new BookedServiceList;
                echo $list->build( $group_id );
			}
    }
    
	bp_register_group_extension( 'Services_Group_Extension' );
	endif;
}
add_action('bp_init', 'bc_services_add_group_extension');