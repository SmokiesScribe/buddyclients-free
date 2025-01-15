<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Defines allowed html for forms.
 * 
 * @since 1.0.16
 */
function buddyc_allowed_html_form() {
    return [
        'form'     => ['method' => [], 'action' => [], 'class' => [], 'id' => [], 'style' => [], 'enctype' => []],
        'input'    => ['type' => [], 'name' => [], 'value' => [], 'class' => [], 'id' => [], 'required' => [], 'placeholder' => [], 'style' => [], 'maxlength' => [], 'minlength' => [], 'pattern' => [], 'size' => [], 'readonly' => [], 'checked' => []],
        'label'    => ['for' => [], 'class' => []],
        'button'   => ['type' => [], 'id' => [], 'class' => [], 'style' => []],
        'div'      => ['class' => true, 'id' => [], 'style' => []],
        'p'        => ['class' => [], 'style' => [], 'id' => []],
        'br'       => [],
        'span'     => ['class' => [], 'id' => [], 'style' => []],
        'h1'       => ['class' => []],
        'h2'       => ['class' => []],
        'h3'       => ['class' => []],
        'h4'       => ['class' => []],
        'h5'       => ['class' => []],
        'h6'       => ['class' => []],
        'textarea' => ['name' => [], 'id' => [], 'class' => [], 'rows' => [], 'cols' => [], 'placeholder' => [], 'style' => []],
        'select'   => ['name' => [], 'id' => [], 'class' => [], 'style' => []],
        'option'   => ['value' => [], 'disabled' => [], 'selected' => [], 'style' => []],
        'strong'   => [],
        'em'       => [],
        'u'        => [],
        'small'    => [],
        'blockquote'=> ['cite' => []],
        'fieldset' => ['class' => [], 'id' => [], 'style' => []],
        'legend'   => ['class' => [], 'style' => []],
        'table'    => ['class' => []],
        'thead'    => ['class' => []],
        'tr'       => ['id' => [], 'class' => []],
        'th'       => ['colspan' => [], 'class' => [], 'scope' => [], 'id' => [], 'abbr' => []],
        'tbody'    => ['class' => []],
        'td'       => ['class' => [], 'data-colname' => []],
        'colgroup' => ['class' => []],
        'col'      => ['class' => []],
        'a'        => ['title' => [], 'href' => [], 'class' => [], 'download' => [], 'data-post-id' => [], 'data-url' => [], 'data-raw-content' => [], 'onclick' => [], 'target' => []],
        'i'        => ['class' => [], 'id' => [], 'style' => []]
    ];
}

/**
 * Adds safe styles to the Wordpress list.
 * 
 * @since 1.0.16
 */
function buddyc_update_safe_styles() {
    add_filter( 'safe_style_css', function( $styles ) {
        $styles[] = 'display';
        return $styles;
    } );
}
add_action( 'init', 'buddyc_update_safe_styles' );

/**
 * Defines allowed html for forms with signature script.
 * 
 * @since 1.0.16
 */
function buddyc_allowed_html_signature() {
    $form_html = buddyc_allowed_html_form();
    $signature_html = [
        'span'     => ['class' => [], 'id' => [], 'style' => [], 'onclick' => []],
        'canvas'   => ['id' => [], 'width' => [], 'height' => [], 'style' => [], 'data-signature' => []],
        'img'   => ['style' => [], 'class' => [], 'decoding' => [], 'src' => []]
    ];
    return array_merge( $form_html, $signature_html );
}