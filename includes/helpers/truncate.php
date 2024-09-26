<?php
/**
 * Clips content by word count.
 * 
 * @since 0.1.0
 *  
 * @param string $content Content to truncate.
 * @param int $word_count Number of words.
 * 
 * @return string $content Truncated content.
 * 
 */
function bc_truncate_content($content, $word_count) {
    $content = strip_tags($content); // Remove HTML tags
    $content = preg_replace('/\s+/', ' ', $content); // Remove extra whitespace
    $words = explode(' ', $content);
    if (count($words) > $word_count) {
        $words = array_slice($words, 0, $word_count);
        $content = implode(' ', $words);
        $content .= '...'; // Add ellipsis
    }
    return $content;
}

/**
 * Clips content by character count.
 * 
 * @since 0.1.0
 *  
 * @param string $content Content to truncate.
 * @param int $char_count Number of characters.
 * 
 * @return string $content Truncated content.
 * 
 */
function bc_truncate_content_by_char($content, $char_count) {
    $content = strip_tags($content); // Remove HTML tags
    $content = preg_replace('/\s+/', ' ', $content); // Remove extra whitespace
    if (strlen($content) > $char_count) {
        $content = substr($content, 0, $char_count); // Truncate by character count
        $content = rtrim($content); // Remove trailing spaces
        $content .= '...'; // Add ellipsis
    }
    return $content;
}
