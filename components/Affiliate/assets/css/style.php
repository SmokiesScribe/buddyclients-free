<style>
/* Settings nav icon */
#affiliates::before {
    content: "\eeed";
}

/* Copy Affiliate Link */
.copy-affiliate-link-container {
    margin-bottom: 40px;
}
.copy-to-clipboard-icon {
    color: <?php echo bc_color('primary') ?>;
    font-size: 24px;
    cursor: pointer;
}
.copy-to-clipboard-icon:hover {
    color: <?php echo bc_color('tertiary') ?>;
}
#copyText {
    min-width: 400px;
}
#feedback {
    margin-top: 10px;
    color: <?php echo bc_color('tertiary') ?>;
}

/* Commission page link */
.commission-page-link {
    margin-bottom: 40px;
}
.commission-page-link a:hover {
    color: <?php echo bc_color('tertiary') ?>;
}

/* Form */
.affiliate-form-success-message {
    color: #ffffff;
    background-color: <?php echo bc_color('tertiary') ?>;
    padding: 10px;
    border-radius: 5px;
}
.affiliate-form-success-message-icon {
    font-size: 24px;
    margin: 0 5px;
}
</style>