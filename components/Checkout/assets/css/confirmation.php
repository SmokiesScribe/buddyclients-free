<style>
/* Container */
.bc-confirmation-container {
    max-width: 650px;
    background-color: #fff;
    margin: auto;
    padding: 30px; /* Add padding inside the container */
    border-radius: 10px;
    border: solid 1px #E7E9EC;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Optional: adds a subtle shadow */
    text-align: center; /* Center-aligns content */
}

/* Header */
.bc-confirmation-container h1 {
    font-size: 24px;
    margin-bottom: 15px; /* Space below the header */
}

/* Content */
.bc-confirmation-container div {
    margin-bottom: 20px; /* Space below the content */
    text-align: center; /* Center-aligns content */
}

/* Button */
.bc-confirmation-button {
    color: #fff;
    background-color: <?php echo esc_attr( bc_color( 'primary' ) ) ?>;
    padding: 15px 20px;
    border-radius: 5px;
    font-size: 15px;
    text-decoration: none; /* Remove underline from link */
    display: inline-block; /* Ensure button is inline-block */
    transition: background-color 0.3s ease; /* Smooth transition on hover */
    margin: 0 auto; /* Center-align the button */
}

.bc-confirmation-button:hover {
    color: #fff;
    background-color: <?php echo esc_attr( bc_color( 'tertiary' ) ) ?>;
    cursor: pointer;
}

</style>