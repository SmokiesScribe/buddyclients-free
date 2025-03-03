<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * 
 * Generates a copy to clipboard field.
 * 
 * @since 0.1.0
 * 
 * @param   string  $content    The content to copy.
 * @param   bool    $input      Optional. Whether to generate an input field.
 *                              Defaults to true.
 */
function buddyc_copy_to_clipboard( $content, $input = true ) {

    $field = $input ?
        sprintf(
            '<input class="buddyc-copy-content" type="text" value="%1$s" size="%2$d" readonly>',
            esc_html( $content ),
            strlen( $content ) // size to content
        ) :
        sprintf(
            '<span class="buddyc-copy-content">%1$s</span>',
            esc_html( $content ),
        );


    return sprintf(
        '<div class="buddyc-copy-to-clipboard">
            %1$s
            <span class="bb-icon-copy copy-to-clipboard-icon"></span>
        </div>',
        $field
    );    
}