<?php
class Movie_Database {

    protected $plugin_name;
    protected $version;

    public function __construct() {
        if (defined('MOVIE_DATABASE_VERSION')) {
            $this->version = MOVIE_DATABASE_VERSION;
        } else {
            $this->version = '1.0';
        }
        $this->plugin_name = 'movie-database';

        $this->load_dependencies();
        $this->define_admin_hooks();
    }

    private function load_dependencies() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-movie-database-api.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-movie-database-cpt.php';
    }

    private function define_admin_hooks() {
        $plugin_admin = new Movie_Database_CPT();
        add_action('init', array($plugin_admin, 'register_cpt_movie'));
        add_action('init', array($plugin_admin, 'register_taxonomy_category'));
    }

    public function run() {
        load_plugin_textdomain('movie-database', false, dirname(dirname(__FILE__)) . '/languages/');
    }
/*Load*/
    public function get_movies($paged = 1, $per_page = 10) {
        $args = array(
            'post_type'      => 'movie',
            'posts_per_page' => $per_page,
            'paged'          => $paged,
        );
    
        $query = new WP_Query($args);
    
        return $query->posts;
    }    
}

