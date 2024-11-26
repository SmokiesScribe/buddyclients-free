<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * 
 * Generates a copy to clipboard field.
 * 
 * @since 0.1.0
 */
function buddyc_copy_to_clipboard( $content, $field_id ) {
    ob_start();
    ?>
    
    <div class="copy-affiliate-link-container">
        <div>
            <p style="display: none" id="<?php echo esc_attr( $field_id ) ?>"><?php echo esc_html( $content ) ?></p>
            <input type="text" value="<?php echo esc_html( $content ) ?>" size="<?php echo esc_html( strlen( $content ) ) ?>" readonly>
            <span class="bb-icon-copy copy-to-clipboard-icon" onclick="buddycCopyToClipboard('<?php echo esc_attr( $field_id ) ?>')"></span>
            <div class="buddyc-copy-success" style="font-size: 14px; color: <?php echo esc_attr( buddyc_color('accent' ) ) ?>; font-weight: 500; margin: 10px"></div>
        </div>
    </div>
    
    <?php
    return ob_get_clean();
}