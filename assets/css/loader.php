<style>
/* Loading Indicator Container */
#bc-loading-indicator-container {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%); /* Center the container */
    width: auto; /* Allow width to adjust based on content */
    text-align: center; /* Center the text */
    transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out; /* Transition for both opacity and visibility */
}

/* Spinner Styling */
#bc-loading-indicator {
    margin: 0 auto; /* Center horizontally */
    margin-bottom: 10px; /* Space between spinner and message */
    border: 16px solid #f3f3f3; /* Light grey border */
    border-radius: 50%; /* Circular shape */
    border-top: 16px solid <?php echo esc_attr( bc_color( 'accent' ) ) ?>; /* Blue border on top */
    width: 60px; /* Adjust size if needed */
    height: 60px; /* Adjust size if needed */
    animation: spin 2s linear infinite; /* Spinning animation */
}

/* Loading Message Styling */
#bc-loading-indicator-message {
    font-size: 14px; /* Adjust font size if needed */
    color: gray; /* Adjust text color if needed */
    text-align: center;
}

/* Keyframes for spinning animation */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>