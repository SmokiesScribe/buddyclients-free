/**
 * Validates email address.
 * 
 * @param {email} The email address string to check.
 * @returns bool
 */
function buddycIsValidEmailAddress(email) {
    // Regular expression for email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}