<style>

/** Mobile button **/
#checkout-mobile-button {
    display: none;
}

.booking-checkout-container {
    display: flex;
    flex-wrap: wrap; /* Ensure items wrap on smaller screens */
}

.booking-form-column {
    flex: 70%;
    max-width: 70%;
    margin-bottom: 20px; /* Add margin for spacing */
}

.checkout-fee-column {
    flex: 30%;
    max-width: 30%;
    margin: 20px;
}

.checkout-details-container {
    position: sticky;
    top: 150px;
    width: 100%;
}

/* Mobile */
@media (max-width: 768px) {
    .booking-form-column {
        flex-basis: 100%;
        max-width: 100%;
        margin: 0; /* Remove margin on mobile */
    }
    
    .checkout-fee-column {
        position: fixed;
        max-width: 95%;
    }

    .checkout-details-container {
        position: fixed;
        top: 200px;
        left: 50%;
        transform: translateX(-50%);
        margin: 20px auto;
        max-width: 95%;
        z-index: 999;
        
        /* New transition for smoother display change */
        transition: opacity 0.3s ease, transform 0.3s ease, visibility 0.3s ease, height 0.3s ease;
        opacity: 0; /* Initially hidden */
        visibility: hidden; /* Initially hidden */
    }

    .checkout-details-container.active {
        opacity: 1;
        visibility: visible;
    }
    
    /** Mobile button **/
    #checkout-mobile-button {
        display: block;
        position: fixed;
        top: 150px; /* Adjust as needed */
        right: 0;
        transform: translateX(-50%);
        z-index: 999; /* Ensure it stays on top */
        background-color: #e7e9ec; /* Button background color */
        color: #000; /* Button text color */
        padding: 10px 20px; /* Adjust padding as needed */
        border: none; /* Remove default border */
        border-radius: 30px; /* Rounded corners */
        font-size: 14px; /* Font size */
        font-weight: bold; /* Font weight */
        text-align: center; /* Center text */
        cursor: pointer; /* Pointer cursor on hover */
        transition: background-color 0.3s ease; /* Smooth background color transition */
    }
    
    /* Hover effect */
    #checkout-mobile-button:hover {
        background-color: #D4D6D8; /* Darker background on hover */
    }
}

.dz-default {
    z-index: 0 !important;
}

.checkout-table {
    border-collapse: collapse;
    width: 100%;
    border: none;
    z-index: 999;
}

.checkout-unit-label {
    color: gray;
}

#bc-checkout-project {
    font-weight: 300;
    margin: 10px;
}

.checkout-table tfoot {
    margin-top: 50px !important;
}

.checkout-table th,
.checkout-table td {
    border: none; /* Set the border to a light gray color */
    padding: 15px 8px;
    text-align: left;
}

.checkout-table th {
    background-color: ; /* Add a background color to table headers */
}

.checkout-table tfoot th,
.checkout-table tfoot td {
    color: #000;
}
#bc-checkout-total {
    font-size: 32px;
    font-weight: 600;
    color: #000;
    margin-top: 30px;
}

/* Create account form */
#bc-create-account {
    margin: auto;
    padding: 15px;
}

#bc-create-account .form-group {
    margin: 10px;
}

#bc-create-account input[type="text"],
#bc-create-account input[type="email"]{
    width: 100%;
}
.bc-test-mode-tag {
    display: inline-block; /* Make the tag only as wide as the content */
    padding: 8px 15px; /* Adjust padding as desired */
    border-radius: 5px;
    font-size: 14;
    font-weight: 500;
    background-color: #f9edbf; /* Set the base background color */  
    color: #9b3617;
}
.bc-test-instructions {
    color: gray;
    background-color: #fff;
    padding: 10px;
    border-radius: 5px;
    border: solid 1px #e7e9ec;
    max-width: 800px;
}
</style>