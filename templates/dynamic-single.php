<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use BuddyClients\Includes\SinglePost;
/**
 * Template Name: Dynamic BuddyClients Single Post Template
 * Description: Dynamically generates a custom template for a single post.
 */

get_header();

$post_type = get_post_type();
$single_post = new SinglePost( $post_type );
$single_post->render();

get_footer();