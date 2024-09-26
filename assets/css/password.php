<style>
/* Wrapper around the input and icon */
.password-field-wrapper {
  position: relative;
  display: block;
}

/* Password input field */
.bc-password-field {
  padding-right: 30px; /* Add space for the icon inside the input */
}

/* Toggle icon positioned inside the input field */
.toggle-password-visibility {
  position: absolute;
  right: 10px;           /* Distance from the right edge */
  top: 50%;              /* Center vertically */
  transform: translateY(-50%);
  cursor: pointer;       /* Cursor changes to pointer when hovering */
  font-size: 24px;       /* Adjust icon size */
  color: #888;           /* Default icon color */
  transition: color 0.3s; /* Smooth color transition on hover */
}

/* Icon hover effect */
.toggle-password-visibility:hover {
  color: #333;           /* Darker color when hovered */
}

/* Password strength indicator */
.bc-password-strength-indicator {
    padding: 5px 30px;
    margin: 10px 10px 10px 0;
    background-color: gray;
    color: white;
    border-radius: 5px;
    font-size: 16px;
}
.bc-password-strength-indicator.strength-weak {
    background-color: red;
    color: white;
}
.bc-password-strength-indicator.strength-medium {
    background-color: orange;
    color: white;
}
.bc-password-strength-indicator.strength-strong {
    background-color: green;
    color: white;
}

/* Generate Password link */
.bc-generate-password-link {
    font-size: 16px;
}
.bc-generate-password-link:hover {
    color: <?php echo bc_color( 'tertiary' ) ?>;
}

/* Copied to clipboard */
.bc-clipboard-success {
    font-size: 14px;
    color: gray;
}

</style>