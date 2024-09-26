<style>
    /* Single Brief Post */
    .bc-single-brief {
        margin-top: 10px;
    }
    
    .bc-single-brief-breadcrumbs {
        margin-bottom: 30px;
    }
    
    @media (min-width: 768px) {
        .bc-single-brief-content {
            max-width: 650px;
            margin: auto;
        }
    }
    
    .single-brief-field {
        margin: auto;
        background-color: #ffffff;
        border: solid 1px #e7e9ec;
        margin-bottom: 20px;
        padding: 30px;
        border-radius: 15px;
    }
    
    /* Single Brief */
    .project-services-title, .project-brief-title {
        margin-bottom: 40px; /* Adjust as needed */
    }
    .project-brief-container textarea {
        border-radius: 10px;
    }
    .brief-form-section-header {
        border-bottom: solid 1px #e7e9ec;
        border-top: solid 1px #e7e9ec;
        padding: 15px 0;
        margin: 25px 0;
    }
    .bc-brief-links {
        margin: 30px auto;
    }
    .bc-back-to-briefs-link:hover {
        color: <?php echo bc_color('tertiary') ?>;
    }
    .show-hide-brief-btn {
        color: <?php echo bc_color('primary') ?>;
        border: solid 1.5px <?php echo bc_color('primary') ?>;
        padding: 10px;
        border-radius: 5px;
        display: inline-block;
        text-decoration: none;
    }
    .show-hide-brief-btn:hover {
        color: <?php echo bc_color('tertiary') ?>;
        border-color: <?php echo bc_color('tertiary') ?>;
    }
</style>