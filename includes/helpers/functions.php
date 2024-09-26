<?php
/**
 * Pretty prints an array.
 * 
 * @since 0.1.0
 * 
 * @ignore
 */
function bc_print( $value ) {
    echo '<pre>';
    print_r( $value );
    echo '</pre>';
}