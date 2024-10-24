<style>
/* Testimonial Cards */
.custom-testimonial-link {
    padding: 0 !important;
    margin: 0 !important;
    color: #5A5A5A;
    text-decoration: none;
}
.custom-testimonial-link:hover {
    color: #5A5A5A;
}
.testimonial-cards {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}
@media (max-width: 768px) {
    .testimonial-cards {
        flex-direction: column;
    }
    .testimonial-card {
        width: 100%;
        max-width: none;
        flex: 1;
    }
}
@media (min-width: 768px) {
    .testimonial-card {
        max-width: 275px;
        height: 400px; /* Set a fixed height for the cards */
        display: flex;
        flex-direction: column; /* Stack content vertically */
        justify-content: space-between; /* Push content to the top and "Read more" to the bottom */
        position: relative;
    }
    .testimonial-content {
        flex-grow: 1; /* Allow content to grow and take available space */
    }
}
.testimonial-read-more {
    bottom: 0; /* Push "Read More" to the bottom */
    position: absolute;
    left: 0;
    right: 0;
    padding: 10px;
    background-color: #fff;
    text-align: center;
}
.testimonial-read-more-button:hover {
    color: <?php echo esc_attr( bc_color('tertiary') ) ?>;
}
.testimonial-card {
    border-radius: 15px;
    border: solid 1px #e7e9ec;
    background-color: #ffffff;
    padding: 25px;
    margin-bottom: 20px;
}
.testimonial-excerpt {
    font-size: 15px;
}
.testimonial-author {
    margin-bottom: 10px;
}
.testimonial-image {
    width: 100px; /* Set the width */
    height: 100px; /* Set the height */
    margin: auto;
    text-align: center;
    overflow: hidden; /* Ensure the image doesn't exceed the square boundaries */
    border-radius: 50%; /* Makes it a circle */
}
.testimonial-image img {
    width: 100px; /* Set the width */
    height: 100px; /* Set the height */
    object-fit: cover; /* Crop the image to cover the container */
}
.testimonial-card:hover {
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}
</style>