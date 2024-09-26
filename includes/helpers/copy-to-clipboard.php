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
            <p style="display: none" id="<?php echo $field_id ?>"><?php echo $content ?></p>
            <input type="text" value="<?php echo $content ?>" size="<?php echo strlen($content) ?>" readonly>
            <span class="bb-icon-copy copy-to-clipboard-icon" onclick="copyToClipboard('<?php echo $field_id ?>')"></span>
            <div class="bc-copy-success" style="font-size: 14px; color: <?php echo bc_color('accent') ?>; font-weight: 500; margin: 10px"></div>
        </div>
    </div>
    
    <?php
    return ob_get_clean();
}