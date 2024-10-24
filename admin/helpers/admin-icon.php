<?php
/**
 * Admin icons.
 * 
 * @since 0.1.0
 * 
 * @param string $icon Icon key.
 * @return string HTML-formatted icon.
 */
function bc_admin_icon( $icon ) {
    // Initialize
    $output = '';

    // Check if bp is active
    $theme = bc_buddyboss_theme();
    
    // Define icon class
    $class = $theme ? 'bb_class' : 'fa_class';
    
    // Define icons array
    $icons = array(
        'check' => [
            'bb_class' => 'bb-icon-check bb-icon-rf',
            'fa_class' => 'fa-solid fa-circle-check',
            'color' => 'green',
        ],
        'x' => [
            'bb_class' => 'bb-icon-times bb-icon-rf',
            'fa_class' => 'fa-solid fa-circle-xmark',
            'color' => 'black',
        ],
        'eye' => [
            'bb_class' => 'bb-icon-eye bb-icon-l',
            'fa_class' => 'fa-regular fa-eye',
            'color' => 'gray',
        ],
        'eye-slash' => [
            'bb_class' => 'bb-icon-eye-slash bb-icon-l',
            'fa_class' => 'fa-regular fa-eye-slash',
            'color' => 'gray',
        ],
        'error' => [
            'bb_class' => 'bb-icon-exclamation-triangle bb-icon-l',
            'fa_class' => 'fa-solid fa-triangle-exclamation',
            'color' => 'red',
        ],
        'ready' => [
            'bb_class' => 'bb-icon-spinner bb-icon-l',
            'fa_class' => 'fa fa-spinner',
            'color' => 'green',
        ],
        'default' => [
            'bb_class' => 'bb-icon-circle bb-icon-l',
            'fa_class' => 'fa-regular fa-circle',
            'color' => 'black',
        ],
        'info' => [
            'bb_class' => 'bb-icon-info bb-icon-rl',
            'fa_class' => 'fa-solid fa-circle-info',
            'color' => 'blue',
        ],
    );

    // Check if icon exists
    if ( isset( $icons[$icon] ) ) {
        // Initialize array
        $classes = ['bc-icon'];

        // Add icon class
        $classes[] = $icons[$icon][$class] ?? null;

        // Add color class
        $icon_color = $icons[$icon]['color'] ?? null;
        if ( $icon_color ) {
            $classes[] = 'bc-icon-color-' . $icon_color;
        }

        // Implode classes
        $class_string = implode( ' ', $classes );

        // Build icon html
        $output = '<i class="' . $class_string . '"></i>';
    }

    return $output;
}