<?php
// Get settings
$help_popup_display = bc_get_setting( 'help', 'help_popup_display' ) ?? 'always_show';
$popup_content = bc_get_setting( 'help', 'help_popup_content' );

// Initialize
$button_display = 'flex';
$desktop_display = 'flex';
$mobile_display = 'flex';

// Hide on mobile or desktop
if ($help_popup_display === 'desktop_only') {
    $mobile_display = 'none';
} else if ($help_popup_display === 'mobile_only') {
    $desktop_display = 'none';  
}

// Hide floating button
if ($help_popup_display === 'always_hide') {
    $button_display = 'none';
    $desktop_display = 'none';
    $mobile_display = 'none';
}

// Hide contact form initially
$display_contact = $popup_content === 'contact_only' ? 'block' : 'none';

?>
<style>
/* Hide form initially */
#contact-popup #bc-contact-form-container {
    display: <?php echo $display_contact ?>;
}

/* Search */
#ajax-docs-search {
    margin-bottom:15px;
    width: 85%;
    padding: 35px;
    font-size: 18px;
    border: 1px solid #d7d9dd;
    border-radius: 5px;
}

/* Search results container */
#search-results {
    border-radius: 10px;
}

/* Individual search result items */
#search-results a {
    text-decoration: none;
}
#search-results p {
    background-color: #ffffff;
    padding: 20px;
    margin: 0;
}
#search-results p:hover {
    background-color: #F5F7FA;
    cursor: pointer;
}

/* Search result link */
#search-results a {
    color: black;
}

/* Floating Button */
.floating-contact-button {
    display: <?php echo $button_display ?>;
    justify-content: center;
    align-items: center;
    position: fixed;
    bottom: 20px;
    right: 20px;
    padding: 10px;
    background-color: <?php echo bc_color('accent') ?>;
    border-radius: 50%;
    border: none;
    color: #ffffff;
}
.floating-contact-button-30 {
    height: 30px;
    width: 30px;
}
.floating-contact-button-50 {
    height: 50px;
    width: 50px;
}
.floating-contact-button i {
    text-align: center; /* Center the text horizontally */
}
.floating-contact-button:hover {
    box-shadow: 0 1px 6px rgba(0, 0, 0, 0.3);
    cursor: pointer;
}

@media only screen and (max-width: 767px) {
    .floating-contact-button {
        display: <?php echo $mobile_display ?>;
    }
}

@media only screen and (min-width: 767px) {
    .floating-contact-button {
        display: <?php echo $desktop_display ?>;
    }
}

/* Contact Popup */
.contact-popup {
    visibility: hidden; /* Set initial visibility to hidden */
    opacity: 0; /* Set initial opacity to 0 */
    transition: visibility 0s, opacity 0.5s ease; /* Add smooth transition */
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 650px; /* Adjust the width as needed */
    max-width: 80%;
    height: 500px; /* Adjust the height as needed */
    overflow: auto;
    background-color: #fff;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
    z-index: 1000;
    padding: 20px;
    border-radius: 15px;
}

/* Close button */
#close-bc-pop-up-button {
    display: none;
}

/* Search in popup */
#ajax-docs-search-container, #contact-popup #bc-contact-form-container {
    margin-top: 10px;
}

/* Mobile */
@media only screen and (max-width: 767px) {
    .contact-popup {
        width: 95%; /* Take up the entire width of the screen */
        max-width: none; /* Remove max-width restriction */
    }
    #ajax-docs-search-container, #contact-popup #bc-contact-form-container {
        margin-top: 40px;
    }
    #close-bc-pop-up-button {
        font-size: 20px;
        display: block;
        position: fixed;
        top: 10px; /* Adjust the distance from the bottom as needed */
        right: 10px;
        transform: translateX(-50%);
        z-index: 1001; /* Ensure it's above the container content */
    }
    #close-bc-pop-up-button:hover {
        color: gray;
    }
}

</style>