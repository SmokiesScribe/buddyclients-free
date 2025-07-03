<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Adds a preview column to the brief type taxonomy.
 * 
 * @since 1.0.3
 * 
 * @param   array   $columns    The array of columns data.
 * 
 * @return  array   The modified array of columns data.
 */
function buddyc_add_preview_column( $columns ) {
    // Add preview column
    $columns['preview_brief'] = __( 'Preview', 'buddyclients-lite' );
    
    // Unset slug column
    unset( $columns['slug'] );
    
    // Return modified columns
    return $columns;
}
add_filter( 'manage_edit-brief_type_columns', 'buddyc_add_preview_column' );

/**
 * Populates the new column with preview buttons.
 * 
 * @since 1.0.3
 * 
 * @param   string  $content    The content of the column.
 * @param   string  $column_name The name of the column.
 * @param   int     $term_id    The ID of the term.
 * 
 * @return  string  The modified content of the column.
 */
function buddyc_render_preview_button( $content, $column_name, $term_id ) {
    if ( $column_name === 'preview_brief' ) {
        $preview_url = add_query_arg( array(
            'preview_brief'   => 1,
            'taxonomy_term_id' => $term_id,
        ), home_url() );

        $content = '<a href="' . esc_url( $preview_url ) . '" class="button" target="_blank">' . __( 'Preview', 'buddyclients-lite' ) . '</a>';
    }
    return $content;
}
add_action( 'manage_brief_type_custom_column', 'buddyc_render_preview_button', 10, 3 );

/**
 * Handles the preview request by redirecting to the preview.
 * 
 * @since 1.0.3
 */
function buddyc_handle_preview_request() {
    $preview_brief = buddyc_get_param( 'preview_brief' );
    $taxonomy_term_id = buddyc_get_param( 'taxonomy_term_id' );
    if ( $preview_brief && $taxonomy_term_id ) {        
        buddyc_redirect_to_preview( intval( $taxonomy_term_id ) );
        exit;
    }
}
add_action( 'init', 'buddyc_handle_preview_request' );

/**
 * Creates a draft post for preview based on the taxonomy term ID.
 * 
 * @since 1.0.3
 * 
 * @param   int     $taxonomy_term_id  The taxonomy term ID.
 * 
 * @return  int|false The post ID of the draft, or false on failure.
 */
function buddyc_create_preview_draft( $taxonomy_term_id ) {
    $term_name = get_term( $taxonomy_term_id )->name;

    $post_id = wp_insert_post( array(
        'post_title' => sprintf(
            /* translators: %s: the name of the brief type (e.g. Editing) */
            __( 'Preview %s Brief', 'buddyclients-lite' ),
            ucfirst( $term_name )
        ),
        'post_type'   => 'buddyc_brief',
        'post_status' => 'draft',
    ) );

    if ( is_wp_error( $post_id ) ) {
        return false;
    }

    wp_set_object_terms( $post_id, array( $taxonomy_term_id ), 'brief_type' );
    update_post_meta( $post_id, 'is_preview_draft', true );

    return $post_id;
}

/**
 * Redirects to the draft post for preview.
 * 
 * @since 1.0.3
 * 
 * @param   int     $taxonomy_term_id  The taxonomy term ID.
 */
function buddyc_redirect_to_preview( $taxonomy_term_id ) {
    $post_id = buddyc_create_preview_draft( $taxonomy_term_id );

    if ( $post_id ) {
        wp_redirect( get_permalink( $post_id ) );
        exit;
    } else {
        wp_die( esc_html( __( 'Unable to create preview. Please try again.', 'buddyclients-lite' ) ) );
    }
}

/**
 * Deletes draft brief preview posts.
 * 
 * @since 1.0.3
 */
function buddyc_delete_brief_preview_drafts() {
    $post_type = buddyc_get_param( 'post_type' );
    if ( ! is_admin() || empty( $post_type ) || $post_type !== 'buddyc_brief' ) {
        return;
    }

    $args = array(
        'post_type'   => 'buddyc_brief',
        'post_status' => 'draft',
        'meta_key'    => 'is_preview_draft',
        'meta_value'  => true
    );

    $old_drafts = get_posts( $args );

    foreach ( $old_drafts as $draft ) {
        wp_delete_post( $draft->ID, true );
    }
}

/**
 * Runs draft deletion before the main query is executed on the buddyc_brief archive page.
 * 
 * @since 1.0.3
 * 
 * @param WP_Query $query The WP_Query instance (passed by reference).
 */
function buddyc_check_and_run_draft_deletion( $query ) {
    // Check if we are in the admin area and if the query is for the `buddyc_brief` archive
    if ( is_admin() && $query->is_main_query() && $query->get( 'post_type' ) === 'buddyc_brief' ) {
        buddyc_delete_brief_preview_drafts();
    }
}
add_action( 'pre_get_posts', 'buddyc_check_and_run_draft_deletion' );