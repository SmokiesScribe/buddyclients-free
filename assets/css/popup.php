<style>
/* Popup link */
.bc-popup-link {
    color: <?php echo bc_color('accent') ?>;
}
.bc-popup-link:hover {
    color: <?php echo bc_color('tertiary') ?>;
}

/* Popup visibility */
.bc-popup-hidden {
    visibility: hidden; /* Set initial visibility to hidden */
    opacity: 0; /* Set initial opacity to 0 */
}

.bc-popup-visible {
    visibility: visible;
    opacity: 1;
}

/* Popup */
.bc-popup {
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
    padding: 25px;
    border-radius: 15px;
    display: flex; /* Use Flexbox */
    flex-direction: column; /* Arrange items vertically */
}

/* Content container */
.bc-popup-content {
    margin-top: auto; /* Push content to the bottom */
    margin-bottom: auto; /* Push content to the top */
}


/* Close button */
#bc-close-btn {
    position: absolute;
    top: 10px;
    right: 20px;
    margin-bottom: 20px;
    cursor: pointer;
    color: gray;
    text-decoration: none;
}
#bc-close-btn:hover {
    color: #afb0ae;
}

/* Mobile */
@media only screen and (max-width: 767px) {
    #help-link-popup {
        width: 95%; /* Take up the entire width of the screen */
        max-width: none; /* Remove max-width restriction */
    }
    #bc-close-btn {
        font-size: 20px;
        display: block;
        position: fixed;
        top: 10px; /* Adjust the distance from the bottom as needed */
        right: 10px;
        transform: translateX(-50%);
        z-index: 1001; /* Ensure it's above the container content */
    }
    #bc-close-btn:hover {
        color: gray;
    }
}
</style>