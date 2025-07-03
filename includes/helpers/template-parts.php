<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Generates button html. 
 * 
 * @since 1.0.21
 * 
 * @param   array   $args
 *     An array of arguments.
 *
 *     @type    string  $text   The button text. Defaults to 'Learn More'.
 *     @type    string  $link   The button url.
 *     @type    string  $style  The type of style for the button. 
 *                              Accepts 'primary', 'secondary', 'outline'.
 *                              Defaults to 'primary'.
 *     @type    string  $size   Optional. The size of button. 
 *                              Accepts 'small', 'medium', 'large', 'wide'.
 *                              Defaults to 'medium'.
 */
function buddyc_btn( $args ) {
    $text   = $args['text'] ?? __( 'Learn More', 'buddyclients-lite' );
    $link   = $args['link'] ?? '#';
    $type   = $args['type'] ?? 'primary';
    $size   = $args['size'] ?? 'medium';

    // Build button classes
    $classes = [
        'buddyc-btn',
        esc_attr( $type ),
        esc_attr( $size ),
    ];
    $classes_string = implode( ' ', $classes );

    // Open button container
    $content = '<div class="buddyc-btn-container">';
    $content .= '<a href="' . esc_url( $link ) . '" class="' . $classes_string . '">';
    $content .= esc_html( $text );
    $content .= '</a>';
    $content .= '</div>';

    return $content;
}