<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Template Name: Single BuddyClients Service
 * Description: A custom template for a single buddyc_service post.
 */

    // Get header
    get_header();
    
    // Get post id
    $post_id = get_the_id();
    
    // Initialize
    $booking_form_btn = '';
    
    // Start building the content
    $service_content = '<div class="buddyc-single-service">';
    
    // Breadcrumbs
    $sep = '<i class="fa-solid fa-angle-right" style="margin: 0 8px; font-size: 12px;"></i>';
    $services_link = '<a href="' . esc_url(site_url('/services/')) . '">' . esc_html__('Services', 'buddyclients-free') . '</a>';
    $service_type = get_the_title(get_post_meta($post_id, 'service_type', true));
    $service_content .= '<p class="buddyc-single-brief-breadcrumbs">' . $services_link . $sep . esc_html($service_type) . $sep . esc_html(get_the_title()) . '</p>';
    
    // Content wrap
    $service_content .= '<div class="buddyc-single-service-content">';
    
    // Title
    $service_content .= '<h1 class="buddyc-service-name">' . esc_html(get_the_title()) . '</h1>';
    
    // Get post meta
    $service_type = get_post_meta($post_id, 'service_type', true);
    $rate_value = get_post_meta($post_id, 'rate_value', true);
    $rate_type = get_post_meta($post_id, 'rate_type', true);
    $hide_from_booking_form = get_post_meta($post_id, 'hide', true);
    $adjustments = get_post_meta($post_id, 'adjustments', true);
    
    // Build the rate type label
    if ($rate_type === 'flat') {
        $rate_type_label = esc_html__('flat', 'buddyclients-free');
    } else if ($rate_type) {
        $rate_type_label = esc_html__('per', 'buddyclients-free') . ' ' . strtolower(esc_html(get_post_meta($rate_type, 'singular', true)));
    } else {
        $rate_type_label = '';
    }
    
    // Define the rate label
    $rate_label = $adjustments ? esc_html__('Starting At', 'buddyclients-free') : esc_html__('Rate', 'buddyclients-free');
    
    // Display button if not hidden from form
    if (!$hide_from_booking_form) {
        $booking_page_id = buddyc_get_setting('pages', 'booking_page');
        if ($booking_page_id) {
            $booking_page_url = get_the_permalink($booking_page_id);
            $booking_form_btn = '<a href="' . esc_url($booking_page_url) . '"><button class="booking-form-btn">' . esc_html__('Book Now', 'buddyclients-free') . '</button></a>';
        }
    }
    
    // Get dependencies links
    $dependency_link = buddyc_single_service_dependencies($post_id);
    
    // Build single service content
    $service_content .= '<div class="buddyc-service-post-content">';
    $service_content .= wp_kses_post(get_the_content());
    $service_content .= '</div>';
    
    $service_content .= $booking_form_btn;
    
    // Details
    $service_content .= '<div class="buddyc-service-details">';
    
    $dependency_link = $dependency_link ? '<p>' . esc_html__('This service requires', 'buddyclients-free') . ' ' . $dependency_link . '</p>' : '';
    $rate_line = $rate_value ? '<p><b>' . esc_html($rate_label) . '</b>: $' . esc_html($rate_value) . ' ' . esc_html($rate_type_label) . '</p>' : '';
    
    $service_content .= $rate_line;
    $service_content .= $dependency_link;
    
    $services_archive = esc_url(site_url('/services/'));
    $all_services_button = '<a href="' . $services_archive . '" class="all-services-btn">' . esc_html__('All Services', 'buddyclients-free') . '</a>';
    
    // Append the additional content to the post content
    $service_content .= $all_services_button;
    
    $service_content .= '</div>';
    $service_content .= '</div>';
    $service_content .= '</div>';
    
    echo wp_kses_post( $service_content );
    
    // Get footer
    get_footer();
    
    /**
     * Retrieves and formats service dependencies.
     * 
     * @since 0.1.0
     */
    function buddyc_single_service_dependencies($service_id) {
        // Get dependencies
        $dependencies = get_post_meta($service_id, 'dependency', true);
        
        // Initialize
        $dependency_array = array();
        $dependency_link = '';
        
        if ($dependencies) {
            // Process each element in the inner array
            foreach ($dependencies as $dependency_id) {
                // Ensure $value is not empty before using it
                if (!empty($dependency_id)) {
                    $dependency_name = get_the_title($dependency_id);
                    $dependency_url = get_permalink($dependency_id);
                    
                    $dependency_array[] = '<a href="' . esc_url($dependency_url) . '">' . esc_html($dependency_name) . '</a>';
                }
            }
            
            // Construct human-readable list
            if (!empty($dependency_array)) {
                $count = count($dependency_array);
                $dependency_list = '';
                for ($i = 0; $i < $count; $i++) {
                    // Add item number and link
                    $dependency_list .= $dependency_array[$i];
                    
                    // Add comma before "or" when there are more than two items
                    if ($count > 2 && $i == $count - 2) {
                        $dependency_list .= ', ' . esc_html__('or', 'buddyclients-free') . ' ';
                    } elseif ($i == $count - 2) {
                        $dependency_list .= ' ' . esc_html__('or', 'buddyclients-free') . ' ';
                    } elseif ($i < $count - 1) {
                        $dependency_list .= ', ';
                    }
                }
            }
    
            $dependency_link = $dependency_list . '.';
        }
        return $dependency_link;
    }