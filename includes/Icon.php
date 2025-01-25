<?php
namespace BuddyClients\Includes;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use BuddyClients\Admin\Settings;

/**
 * Generates plugin icons.
 * 
 * Uses BuddyBoss or Font Awesome icons, depending on the enabled theme.
 *
 * @since 1.0.20
 */
class Icon {
    
    /**
     * Whether the BuddyBoss theme is active.
     * 
     * @var bool
     */
     public $bb_theme;
     
    /**
     * The formatted icon html.
     * 
     * @var string
     */
     public $html;

    /**
     * The icon classes.
     * 
     * @var string
     */
    public $class;

    /**
     * Defines all icon data.
     * 
     * @since 1.0.20
     */
    private static function icon_data() {
        return [
            'admin-info' => [
                'bb-icon-class' => 'bb-icon-rf bb-icon-info',
                'fa-icon-class' => 'fa-solid fa-circle-info',
            ],
            'edit' => [
                'bb-icon-class' => 'bb-icon-l bb-icon-edit',
                'fa-icon-class' => 'fa-solid fa-pen-to-square',
            ],
            'question' => [
                'bb-icon-class' => 'bb-icon-f bb-icon-question',
                'fa-icon-class' => 'fa-solid fa-question',
            ],
            'check' => [
                'bb-icon-class' => 'bb-icon-check bb-icon-rf',
                'fa-icon-class' => 'fa-solid fa-circle-check',
                'color' => 'green',
            ],
            'x' => [
                'bb-icon-class' => 'bb-icon-times bb-icon-rf',
                'fa-icon-class' => 'fa-solid fa-circle-xmark',
                'color' => 'black',
            ],
            'eye' => [
                'bb-icon-class' => 'bb-icon-eye bb-icon-l',
                'fa-icon-class' => 'fa-regular fa-eye',
                'color' => 'gray',
            ],
            'eye-slash' => [
                'bb-icon-class' => 'bb-icon-eye-slash bb-icon-l',
                'fa-icon-class' => 'fa-regular fa-eye-slash',
                'color' => 'gray',
            ],
            'error' => [
                'bb-icon-class' => 'bb-icon-exclamation-triangle bb-icon-l',
                'fa-icon-class' => 'fa-solid fa-triangle-exclamation',
                'color' => 'red',
            ],
            'ready' => [
                'bb-icon-class' => 'bb-icon-spinner bb-icon-l',
                'fa-icon-class' => 'fa fa-spinner',
                'color' => 'green',
            ],
            'default' => [
                'bb-icon-class' => 'bb-icon-circle bb-icon-l',
                'fa-icon-class' => 'fa-regular fa-circle',
                'color' => 'black',
            ],
            'info' => [
                'bb-icon-class' => 'bb-icon-info bb-icon-rl',
                'fa-icon-class' => 'fa-solid fa-circle-info',
                'color' => 'blue',
            ],
            'backward' => [
                'bb-icon-class' => 'bb-icon-backward bb-icon-l',
                'fa-icon-class' => 'fa-solid fa-backward',
                'color' => 'gray'
            ],
            'clock' => [
                'bb-icon-class' => 'bb-icon-clock bb-icon-l',
                'fa-icon-class' => 'fa-solid fa-clock',
            ],
            'paperclip' => [
                'bb-icon-class' => 'bb-icon-l bb-icon-paperclip',
                'fa-icon-class' => 'fa-solid fa-paperclip',
            ],
            'download' => [
                'bb-icon-class' => 'ms-download-icon bb-icon-download',
                'fa-icon-class' => 'ms-download-icon fa fa-download',
            ],
            'toggle_off' => [
                'bb-icon-class' => 'bb-icon-toggle-off bb-icon-l',
                'fa-icon-class' => 'fa-solid fa-toggle-off',
            ],
            'check' => [
                'bb-icon-class' => 'bb-icon-check bb-icon-rf',
                'fa-icon-class' => 'fa-solid fa-circle-check',
                'color' => 'green',
            ],
            'x' => [
                'bb-icon-class' => 'bb-icon-times bb-icon-rf',
                'fa-icon-class' => 'fa-solid fa-circle-xmark',
                'color' => 'black',
            ],
            'error' => [
                'bb-icon-class' => 'bb-icon-exclamation-triangle bb-icon-l',
                'fa-icon-class' => 'fa-solid fa-triangle-exclamation',
                'color' => 'red',
            ],
            'ready' => [
                'bb-icon-class' => 'bb-icon-spinner bb-icon-l',
                'fa-icon-class' => 'fa fa-spinner',
                'color' => 'green',
            ],
            'square' => [
                'bb-icon-class' => 'bb-icon-stop bb-icon-l',
                'fa-icon-class' => 'fa-regular fa-square',
                'color' => 'green',
            ],
            'checkbox' => [
                'bb-icon-class' => 'bb-icon-checkbox bb-icon-l',
                'fa-icon-class' => 'fa-regular fa-square-checked',
                'color' => 'green',
            ],
            'default' => [
                'bb-icon-class' => 'bb-icon-circle bb-icon-l',
                'fa-icon-class' => 'fa-regular fa-circle',
                'color' => 'black',
            ],
        ];
    }
     
    /**
     * Constructor method.
     *
     * @since 0.1.0
     *
     * @param   string  $key    The identifying key of the icon.
     */
    public function __construct( $key ) {
        $this->key = $key;
        $this->bb_theme = buddyc_buddyboss_theme();
        $this->build_icon();        
    }

    /**
     * Builds the icon from the key.
     * 
     * @since 1.0.20
     */
    private function build_icon() {
        $icon_data = self::icon_data();
        $class_type = $this->bb_theme ? 'bb-icon-class' : 'fa-icon-class';

        // Check if icon exists
        if ( isset( $icon_data[$this->key] ) ) {
            // Initialize array
            $classes = ['buddyc-icon', $this->key, $class_type];

            // Add icon class
            $classes[] = $icon_data[$this->key][$class_type] ?? null;

            // Add color class
            $icon_color = $icon_data[$this->key]['color'] ?? null;
            if ( $icon_color ) {
                $classes[] = 'buddyc-icon-color-' . $icon_color;
            }

            // Implode classes
            $this->class = implode( ' ', $classes );

            // Build icon html
            $this->html = '<i class="' . $this->class . '"></i>';
        }
    }
}