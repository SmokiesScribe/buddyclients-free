<?php
/**
 * Defines allowed html for forms.
 * 
 * @since 1.0.16
 */
function bc_allowed_html_form() {
    return [
        'form'     => ['method' => [], 'action' => [], 'class' => [], 'id' => [], 'style' => []],
        'input'    => ['type' => [], 'name' => [], 'value' => [], 'class' => [], 'id' => [], 'required' => [], 'placeholder' => [], 'style' => [], 'maxlength' => [], 'minlength' => [], 'pattern' => []],
        'label'    => ['for' => [], 'class' => []],
        'button'   => ['type' => [], 'class' => [], 'style' => []],
        'div'      => ['class' => [], 'id' => [], 'style' => []],
        'p'        => ['class' => [], 'style' => []],
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
        'thead'    => [],
        'tr'       => [],
        'th'       => ['colspan' => []],
        'tbody'    => [],
        'td'       => [],
        'a'        => ['href' => [], 'class' => []],
    ];
}

/**
 * Adds safe styles to the Wordpress list.
 * 
 * @since 1.0.16
 */
function bc_update_safe_styles() {
    add_filter( 'safe_style_css', function( $styles ) {
        $styles[] = 'display';
        return $styles;
    } );
}
add_action( 'init', 'bc_update_safe_styles' );