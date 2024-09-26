/**
 * Set affiliate cookie.
 * 
 * This function appends the 'affiliate' parameter to an existing cookie.
 * 
 * @param {string} name - The name of the cookie.
 * @param {string} value - The value to be appended to the cookie.
 * @param {number} days - The number of days the cookie should last.
 * @since 1.0
 */
function bcSetCookie(name, value, days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    // Retrieve the current cookie value
    var currentCookie = decodeURIComponent(document.cookie);
    // Check if the 'affiliate' parameter is already in the cookie
    var affiliateIndex = currentCookie.indexOf(name + '=');
    var uniqueValues = new Set();
    
    if (affiliateIndex !== -1) {
        // Get the existing 'affiliate' value
        var startIndex = affiliateIndex + name.length + 1;
        var endIndex = currentCookie.indexOf(';', startIndex);
        if (endIndex === -1) {
            endIndex = currentCookie.length;
        }
        var existingValue = currentCookie.substring(startIndex, endIndex);
        
        // Split the existing values and add them to the set
        existingValue.split(',').forEach(function (v) {
            uniqueValues.add(v);
        });
    }
    
    // Add the new 'affiliate' value to the set
    uniqueValues.add(value);
    
    // Join the values in the set and set the updated cookie
    document.cookie = name + "=" + Array.from(uniqueValues).join(',') + expires + "; path=/";

    //console.log('Affiliate ID Cookie Value:', Array.from(uniqueValues).join(','));
}


/**
 * Get the value of a URL parameter by name.
 * 
 * @param {string} name - The name of the URL parameter to retrieve.
 * @returns {string|null} - The value of the URL parameter, or null if not found.
 */

function getParameterByName(name) {
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(window.location.search);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}

// Check if the 'affiliate' parameter is in the URL
var affiliateValue = getParameterByName('affiliate');
if (affiliateValue) {
    // Set a persistent cookie named 'affiliate' with the affiliate value for 30 days
    bcSetCookie('affiliate', affiliateValue, 30);
    // Update affiliate click count
    bcAffiliateClickCount(affiliateValue);
}

// Call functions on page load
window.onload = function() {
    var affiliateValue = getParameterByName('affiliate');
    if (affiliateValue) {
        // Set a persistent cookie named 'affiliate' with the affiliate value for 30 days
        bcSetCookie('affiliate', affiliateValue, 30);
    }
};

/**
 * Update affilate click meta.
 * 
 * @since 1.0
 */
function bcAffiliateClickCount(id) {
    // Check if the session token is already set
    if (sessionStorage.getItem('affiliateClickCountToken')) {
        // If session token is set, do not proceed with the function
        return;
    }

    // Make an AJAX request to fetch the rate data based on the selected service ID
    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {
            action: 'bc_update_affiliate_click_count',
            id: id, // pass affiliate id from cookie
        },
        success: function(response) {
            // Set session token to prevent multiple executions of the function
            sessionStorage.setItem('affiliateClickCountToken', 'true');
        }
    });
}