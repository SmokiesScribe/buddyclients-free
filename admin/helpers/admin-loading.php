<?php
/**
 * Generates admin loading message.
 * 
 * @since 0.1.3
 * 
 * @param string $message The loading message to display.
 */
function bc_admin_loading( $message ) {
    ob_start();
    echo '<style>
        #bc-admin-loading {
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            font-size: 20px;
            text-align: center;
            z-index: 99;
        }
        #bc-admin-spinner {
            font-size: 50px;
            margin-bottom: 10px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>';
    echo '<div id="bc-admin-loading">
            <i class="fa-solid fa-circle-notch" id="bc-admin-spinner"></i>
            <div>' . esc_html__( $message, 'buddyclients' ) . '</div>
          </div>';
    ob_end_flush();
    flush();
}

/**
 * Displays the loading spinner.
 * 
 * @since 1.0.0
 */
function bc_show_loading_spinner() {
    echo '<style>#bc-loading-indicator {visibility: visible; opacity: 1;}</style>';
    
}

/**
 * Hides the loading spinner.
 * 
 * @since 1.0.0
 */
function bc_hide_loading_spinner() {
    echo '<style>#bc-loading-indicator {visibility: hidden; opacity: 0;}</style>';
}

/**
 * Starts the loading message.
 * 
 * @since 1.0.0
 */
function bc_admin_loading_start() {
    add_action( 'admin_footer', 'bc_show_loading_spinner' );
    add_action( 'wp_footer', 'bc_show_loading_spinner' );
}

/**
 * Ends the loading message.
 * 
 * @since 1.0.0
 */
function bc_admin_loading_end() {
    add_action( 'admin_footer', 'bc_hide_loading_spinner' );
    add_action( 'wp_footer', 'bc_hide_loading_spinner' );
}