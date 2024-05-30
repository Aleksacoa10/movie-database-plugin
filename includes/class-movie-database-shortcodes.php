<?php
class Movie_Database_Shortcodes {

    public function __construct() {
        add_shortcode('movie_list', array($this, 'render_movie_list'));
    }

    public function render_movie_list() {
        ob_start();
        ?>
        <div id="movies-container" class="movie-grid">
            <?php
            $movie_database = new Movie_Database();
            $movies = $movie_database->get_movies(1, 10);

            foreach ($movies as $movie) {
                ?>
                <div class="movie">
                    <a href="<?php echo get_permalink($movie->ID); ?>" data-fancybox data-type="ajax">
                        <img src="<?php echo esc_url(get_the_post_thumbnail_url($movie->ID)); ?>" alt="<?php echo esc_attr($movie->post_title); ?>">
                    </a>
                    <h2><a href="<?php echo get_permalink($movie->ID); ?>" data-fancybox data-type="ajax"><?php echo esc_html($movie->post_title); ?></a></h2>
                </div>
                <?php
            }
            ?>
        </div>
        <div id="load-more-movies-container">
            <button id="load-more-movies"><?php _e('Load More Movies', 'movie-database'); ?></button>
        </div>
        <?php
        return ob_get_clean();
    }
}

new Movie_Database_Shortcodes();
?>
