<?php
/**
 * 
 * Generates a copy to clipboard field.
 * 
 * @since 0.1.0
 */
function bc_copy_to_clipboard( $content, $field_id ) {
    ob_start();
    ?>
    
    <div class="copy-affiliate-link-container">
        <div>
            <p style="display: none" id="<?php echo esc_attr( $field_id ) ?>"><?php echo esc_html( $content ) ?></p>
            <input type="text" value="<?php echo esc_html( $content ) ?>" size="<?php echo esc_html( strlen( $content ) ) ?>" readonly>
            <span class="bb-icon-copy copy-to-clipboard-icon" onclick="copyToClipboard('<?php echo esc_attr( $field_id ) ?>')"></span>
            <div class="bc-copy-success" style="font-size: 14px; color: <?php echo esc_attr( bc_color('accent' ) ) ?>; font-weight: 500; margin: 10px"></div>
        </div>
    </div>
    
    <?php
    return ob_get_clean();
}