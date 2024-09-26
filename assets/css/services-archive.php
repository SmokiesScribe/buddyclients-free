<style>
/* Title */
.archive-title-container {
    width: 100%;
}
/* Rates Cards */
.rate-cards {
    list-style: none !important;
    padding: 0 !important;
    margin: 0 !important;
    display: flex !important;
    flex-wrap: wrap !important;
    justify-content: left;
}

/* Single Service Container */
.rate-post {
    margin: 10px;
    background-color: #fff;
    border-radius: 15px;
    padding: 20px;
    border: solid 1px #e7e9ec;
    align-items: center;
    text-align: center;
    flex-grow: 1; /* Allow items to grow equally */
    flex-basis: 300px; /* initial width */
    box-sizing: border-box; /* Include padding and border in the width calculation */
}

@media only screen and (min-width: 767px) {
    .rate-post {
        width: calc(33.33% - 20px); /* Set width for 3 items per row */
    }
}

@media only screen and (min-width: 767px) {
    .rate-post {
        max-width: calc(33.33% - 20px); /* Maximum width for 3 items per row */
    }
}


.service-section-title {
    margin-top: 40px;
    font-size: 24px;
}
.service-type-label {
    background-color: <?php echo bc_color('primary') ?>;
    color: #ffffff;
    padding: 2px 10px;
    border-radius: 15px;
    font-size: 12px;
    display: inline-block;
}

.custom-rate-link {
    padding: 0 !important;
    margin: 0 !important;
    color: #5A5A5A;
    text-decoration: none;
}
.custom-rate-link:hover {
    color: #5A5A5A;
}

@media (min-width: 768px) {
    .rate-content {
        flex-grow: 1; /* Allow content to grow and take available space */
    }
}
.rate-read-more {
    bottom: 0; /* Push "Read More" to the bottom */
    position: absolute;
    left: 0;
    right: 0;
    padding: 10px;
    background-color: #fff;
    text-align: center;
}
.rate-read-more-button:hover {
    color: <?php echo bc_color('tertiary') ?>;
}

.rate-excerpt {
    font-size: 15px;
}
.rate-title {
    margin-bottom: 10px;
}
.rate-image {
    width: 100px; /* Set the width */
    height: 100px; /* Set the height */
    margin: auto;
    text-align: center;
    overflow: hidden; /* Ensure the image doesn't exceed the square boundaries */
    margin-bottom: 20px;
    border-radius: 50%; /* Makes it a circle */
}
.rate-post:hover {
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}


/* Rates Table */

/* Style for the table */
.blue-pen-rates-table {
    background-color: white;
    max-width: 800px;
    margin: 25px auto;
}

/* Style for table headers */
.blue-pen-rates-table th {
    background-color: #fafbfd;
}

/* Style the section headers */
.blue-pen-rates-section {
    background-color: #fff;
    font-weight: bold;
    border: solid 1px #e7e9ec;
    color: <?php echo bc_color('primary') ?>;
}

.blue-pen-rates-table .blue-pen-rates-section td {
    background-color: #fafbfd;
    border-bottom: solid 1px #e7e9ec;
    border-radius: 10px 10px 0 0;
    text-align: center;
}

.blue-pen-rates-table .blue-pen-rates-section td h3 {
    margin: 10px;
}

.blue-pen-rates-table .rate-type-header {
    border-bottom: solid 1px #e7e9ec;
}

/* Style for DataTables search input */
.blue-pen-rates-table-container .dataTables_filter input {
    width: 100%;
    margin-bottom: 10px;
}

.blue-pen-rates-table td {
    width: 30%;
}
.blue-pen-rates-table-wrapper {
    margin: auto;
}
</style>