<?php
use BuddyClients\Components\Booking\BookedService\BookedServiceList;
/**
 * Register services group screen.
 * 
 * @since 0.1.0
 */
function buddyc_services_add_group_extension() {
	if ( bp_is_active( 'groups' ) ) :
		class Buddyc_Services_Group_Extension extends BP_Group_Extension {
			function __construct() {
				$args = array(
					'slug'              => 'services',
					'name'              =>  __( 'Project Services', 'buddyclients-free' ),
					'nav_item_position' => 200,
					'enable_nav_item'   => true,
					'screens' => array(
						'edit' => array(
							'name'      => __( 'Project Services', 'buddyclients-free' ),
						),
						'create'        => array( 'position' => 0, ),
					),
				);
				parent::init( $args );
			}
			
			function display( $group_id = NULL ) {
				$group_id = bp_get_group_id();

				$group_extension_status = groups_get_groupmeta( $group_id, 'group_extension_setting' );
				$brief_title = '<h3 class="project-brief-title">' . __( 'Project Services', 'buddyclients-free' ) . '</h3>' . esc_attr( $group_extension_status );
				echo wp_kses_post( $brief_title );
				
				// List project briefs
			    $list = new BookedServiceList;
				$service_list = $list->build();
				if ( $service_list ) {
                	echo wp_kses_post( $service_list );
				}
			}
    }
    
	bp_register_group_extension( 'Buddyc_Services_Group_Extension' );
	endif;
}
add_action('bp_init', 'buddyc_services_add_group_extension');