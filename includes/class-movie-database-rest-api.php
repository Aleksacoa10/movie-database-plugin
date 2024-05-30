<?php
class Movie_Database_REST_API {

    public function __construct() {
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    public function register_routes() {
        register_rest_route('movie-database/v1', '/movies', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_movies'),
        ));
    }

    public function get_movies(WP_REST_Request $request) {
        $page = $request->get_param('page');
        $per_page = $request->get_param('per_page');

        $args = array(
            'post_type' => 'movie',
            'posts_per_page' => $per_page,
            'paged' => $page,
        );
        $query = new WP_Query($args);
        $movies = array();

        foreach ($query->posts as $post) {
            $movies[] = array(
                'ID' => $post->ID,
                'title' => $post->post_title,
                'excerpt' => wp_trim_words($post->post_content, 20),
                'permalink' => get_permalink($post->ID),
                'thumbnail' => get_the_post_thumbnail_url($post->ID),
            );
        }

        return rest_ensure_response($movies);
    }
}

new Movie_Database_REST_API();
?>
