<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die;
}

// Custom post type data
$movies = get_posts( array( 'post_type' => 'movie', 'numberposts' => -1 ) );

foreach ( $movies as $movie ) {
    wp_delete_post( $movie->ID, true );
}

// Custom taxonomy data
$terms = get_terms( array( 'taxonomy' => 'category', 'hide_empty' => false ) );

foreach ( $terms as $term ) {
    wp_delete_term( $term->term_id, 'category' );
}
?>
