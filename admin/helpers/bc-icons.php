<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Defines icons for use across the plugin.
 * 
 * @since 0.1.0
 * 
 * @param   string  $icon   Icon key.
 * @return  string  HTML-formatted icon.
 */
function buddyc_icon( $icon ) {
    
    // Check for BuddyBoss theme
    $theme = buddyc_buddyboss_theme();
    
    // Define icon class
    $class = $theme ? 'bb_class' : 'fa_class';
    
    // Define icons array
    $icons = array(
        'backward' => [
            'bb_class' => 'bb-icon-backward bb-icon-l',
            'fa_class' => 'fa-solid fa-backward',
            'color' => 'gray'
        ],
        'clock' => [
            'bb_class' => 'bb-icon-clock bb-icon-l',
            'fa_class' => 'fa-solid fa-clock',
        ],
        'paperclip' => [
            'bb_class' => 'bb-icon-l bb-icon-paperclip',
            'fa_class' => 'fa-solid fa-paperclip',
        ],
        'download' => [
            'bb_class' => 'ms-download-icon bb-icon-download',
            'fa_class' => 'ms-download-icon fa fa-download',
        ],
        'toggle_off' => [
            'bb_class' => 'bb-icon-toggle-off bb-icon-l',
            'fa_class' => 'fa-solid fa-toggle-off',
        ],
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
    );
    
    // Check if the key exists
    if ( isset( $icons[$icon][$class] ) ) {
        $font_size = $theme ? '20' : '16';
        $color = $icons[$icon]['color'] ?? buddyc_color( 'secondary' );
        $output = '<i class="' . $icons[$icon][$class] . '" style="font-size: ' . $font_size . 'px; color: ' . $color . '"></i>';
        return $output;
    }
}