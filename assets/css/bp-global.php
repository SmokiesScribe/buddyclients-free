<?php
// Exit if BB Theme is enabled or we're in the admin area
if ( bc_buddyboss_theme() || is_admin() ) {
    return;
}
?>

<style>
/* Form */
.bc-form {
    margin: auto;
    max-width: 800px;
}

/* Style checkboxes */
.bc-form input[type="checkbox"] {
    -webkit-appearance: none; /* Remove default checkbox appearance */
    -moz-appearance: none;
    appearance: none;
    display: inline-block;
    opacity: 1 !important;
    width: 16px !important; /* Adjust size as needed */
    height: 16px;
    border: 1px solid #d7d9dd; /* Border color */
    border-radius: 4px; /* Rounded corners */
    background-color: #fff; /* Background color */
    cursor: pointer; /* Change cursor on hover */
    margin-right: 5px; /* Add some space between checkbox and label */
    vertical-align: middle; /* Align checkbox vertically */
}

/* Style checked state of checkboxes */
.bc-form input[type="checkbox"]:checked {
    background-color: <?php echo esc_attr( bc_color('accent') ) ?>; /* Change background color when checked */
}

/* Style the checkmark inside the checkbox */
.bc-form input[type="checkbox"]::before {
    content: "\2713"; /* Unicode checkmark character */
    font-size: 16px; /* Adjust size as needed */
    color: #fff; /* Checkmark color */
    display: inline-block;
    width: 16px !important;
    height: 16px;
    text-align: left;
    line-height: 0; /* Align checkmark vertically */
    vertical-align: middle;
    position: relative; /* Set position to relative */
    left: -5px; /* Adjust left position */
    visibility: hidden; /* Hide checkmark by default */
}

/* Show checkmark when checkbox is checked */
.bc-form input[type="checkbox"]:checked::before {
    visibility: visible; /* Show checkmark */
}

/* Style the checkbox label */
.bc-form label {
    font-size: 16px; /* Adjust size as needed */
    line-height: 16px; /* Align label vertically with checkbox */
    vertical-align: middle; /* Align label vertically */
}

/* Adjust checkbox wrap spacing */
.bc-form .bp-checkbox-wrap {
    margin-bottom: 5px; /* Add some space between each checkbox */
}

/* Style inputs */
.bc-form select,
.bc-form input,
.bc-input {
    padding: 10px;
    border-radius: 5px;
    border: solid 1px #d7d9dd;
}

.bc-form input[type="submit"] {
    background-color: <?php echo esc_attr( bc_color('primary') ) ?>;
    color: #fff;
    font-size: 16px;
    padding: 12px;
    border-radius: 8px;
}

.bc-form input[type="submit"]:hover {
    background-color: <?php echo esc_attr( bc_color('tertiary') ) ?>;
    cursor: pointer;
}

/* Style the entire table */
#line-items-table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
    border: solid 1px #d7d9dd;
}

/* Style the table header */
#line-items-table th {
    padding: 10px; /* Padding for content */
    border-bottom: solid 1px #d7d9dd; /* Only bottom border */
}

/* Style the table body */
#line-items-table td {
    padding: 10px; /* Padding for content */
    border-bottom: solid 1px #d7d9dd; /* Only bottom border */
}

/* Upload button */
.dz-button {
    padding: 10px 20px;
    background-color: <?php echo esc_attr( bc_color('tertiary') ) ?>;
    color: #fff;
    border: none;
    border-radius: 5px;
}

.dz-button:hover {
    background-color: <?php echo esc_attr( bc_color('accent') ) ?>;
    cursor: pointer;
}

/* Avatar */
.blue-pen-form-user-avatar img {
    border-radius: 100%;
}

/* Buttons */
.bc-button {
    background-color: <?php echo esc_attr( bc_color('primary') ) ?>;
    padding: 15px;
    border-radius: 5px;
    color: #fff;
    font-size: 16px;
    border: none;
    margin: 10px 0;
}

.bc-button:hover {
    background-color: <?php echo esc_attr( bc_color('tertiary') ) ?>;
    cursor: pointer;
}

    
</style>