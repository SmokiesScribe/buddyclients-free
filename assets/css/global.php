<style>
/* Subnav */
.bc-subnav {
    margin-right: 20px;
}
/* Copy Paste Field */
.bb-icon-copy {
    font-size: 22px;
}
.bb-icon-copy:hover {
    color: gray;
    cursor: pointer;
}
/* Archive */
.archive-title-container {
    margin-top: 30px;
}
/* Single post */
.bc-single-post {
    margin-top: 10px;
}
/* Signature field */
#signature-clear-button {
    max-width: 150px;
    padding: 5px;
    background-color: #fafbfd;
    color: <?php echo esc_attr( bc_color('tertiary') ) ?>;
    border-color: <?php echo esc_attr( bc_color('tertiary') ) ?>;
    font-size: 12px;
}

/* Description Checkbox */
.bc_description_checkbox + label {
    color: gray;
    font-size: 15px;
}

/* No Margin */
.margin-free {
    margin: 0 !important;
}
/* Hide Group Admin Menu Items */
#group-settings-groups-li,
#manage-members-groups-li,
#delete-group-groups-li {
    display: none;
}

/* On-Screen Notification Edge Color */
.bb-onscreen-notification-enable .bb-onscreen-notification .notification-list .read-item.recent-item:before {
    background: <?php echo esc_attr( bc_color('accent') ) ?>;
}

/* Forms */
.bc-form .description {
    color: #8F9091;
    font-size: 14px;
    margin-bottom: 10px;
}

.bc-form-container {
    display: flex;
    justify-content: center;
    align-items: center;
    max-width: 650px;
    margin: auto;
}

.bc-form {
    width: 100%;
}

.bc-form legend {
    color: <?php echo esc_attr( bc_color('primary') ) ?>;
    font-size: 18px;
}

.bc-form fieldset {
    border: none;
    padding: 0;
    width: 100%;
    margin: 0;
}

.no-team-available {
    font-size: 14px;
    color: gray;
}

/* Hide default checkbox */
.bc-form input[type="checkbox"] {
    opacity: 0;
    display: none;
}

.bc-form .form-group {
    padding: 15px 0;
}

.bc-form textarea,
.bc-form .form-group input,
.bc-form .form-group select {
    width: 100%;
}

.bc-form.bc-table-form .form-group {
    padding: 3px;
}


/* User profile */
.bc-form-user-profile {
    display: flex;
    align-items: center;
    border-bottom: solid 1px #e7e9ec;
    padding: 20px;
    max-width: 500px;
}

.bc-form-user-avatar {
    margin-right: 10px;
    margin-left: 20px;
}

.bc-form-user-name {
    font-weight: bold;
    color: <?php echo esc_attr( bc_color('primary') ) ?>;
}

/* File upload */
.manuscript-upload {
    max-width: 600px;
}

/* Global */
.display-none {
    display: none;
}
.max-650 {
    max-width: 650px;
    margin: auto;
}

/* Block Quote */
.wp-block-quote {
    background-color: #ffffff !important;
    border: solid 1px #e7e9ec;
}
.wp-block-quote:before {
    background-color: <?php echo esc_attr( bc_color('accent') ) ?> !important;
}

/* ALERT BAR */
.custom-alert-bar {
    background-color: <?php echo esc_attr( bc_color('primary') ) ?>;
    color: #ffffff;
    text-align: center;
    padding: 20px;
    position: fixed !important;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 9999;
}

.custom-alert-bar a {
    color: #ffffff;
    text-decoration: underline;
}

.custom-alert-bar a:hover {
    color: #e7e9ec;
}
.alert-message-icon {
    font-size: 20px;
}
</style>