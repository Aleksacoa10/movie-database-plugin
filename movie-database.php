<?php
/*
Plugin Name: Movie Database
Description: A plugin to manage movies with custom fields.
Version: 2.0
Author: Aleksa
Text Domain: movie-database
Domain Path: /languages
*/

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Load text domain for translations
function movie_database_load_textdomain() {
    load_plugin_textdomain('movie-database', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'movie_database_load_textdomain');

// Define plugin version
define('MOVIE_DATABASE_VERSION', '2.0');

// Includes the main class files
require_once plugin_dir_path(__FILE__) . 'includes/class-movie-database.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-movie-database-api.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-movie-database-cpt.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-movie-database-activator.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-movie-database-deactivator.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-movie-database-contact-form.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-movie-database-admin.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-movie-database-shortcodes.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-movie-database-rest-api.php';  

// Function to flush rewrite rules
function movie_database_flush_rewrite_rules() {
    $cpt = new Movie_Database_CPT();
    $cpt->register_cpt_movie();
    $cpt->register_taxonomy_category();
    flush_rewrite_rules();
}

// Activation and deactivation hooks
register_activation_hook(__FILE__, 'movie_database_activation');
register_deactivation_hook(__FILE__, 'movie_database_deactivation');

function movie_database_activation() {
    Movie_Database_Activator::activate();

    if (!wp_next_scheduled('movie_database_daily_event')) {
        wp_schedule_event(time(), 'daily', 'movie_database_daily_event');
    }

    // Flush rewrite rules
    movie_database_flush_rewrite_rules();
}

function movie_database_deactivation() {
    Movie_Database_Deactivator::deactivate();
    wp_clear_scheduled_hook('movie_database_daily_event');

    // Flush rewrite rules
    flush_rewrite_rules();
}

add_action('movie_database_daily_event', 'fetch_popular_movies');

// Function to fetch popular movies from the API
function fetch_popular_movies() {
    $api_token = get_option('movie_database_api_token');
    if (!$api_token) {
        error_log('Movie Database: API token is missing.');
        return;
    }
    $api = new Movie_Database_API($api_token);
    $movies = $api->fetch_popular_movies();

    if (empty($movies)) {
        error_log('Movie Database: No movies fetched from the API.');
        return;
    }

    foreach ($movies as $movie) {
        create_movie_post($movie);
    }
}

function create_movie_post($movie) {
    $existing_movie = get_page_by_title($movie['title'], OBJECT, 'movie');
    if ($existing_movie) {
        return;
    }

    $post_id = wp_insert_post(array(
        'post_title'    => sanitize_text_field($movie['title']),
        'post_content'  => sanitize_textarea_field($movie['overview']),
        'post_status'   => 'publish',
        'post_type'     => 'movie',
    ));

    if (!empty($movie['poster_path'])) {
        $image_url = 'https://image.tmdb.org/t/p/w500' . $movie['poster_path'];
        $image_id = upload_image_from_url($image_url);
        set_post_thumbnail($post_id, $image_id);
    }

    update_post_meta($post_id, 'rating', sanitize_text_field($movie['vote_average']));
    update_post_meta($post_id, 'release_date', sanitize_text_field($movie['release_date']));

    $api = new Movie_Database_API(get_option('movie_database_api_token'));
    $movie_details = $api->fetch_movie_data($movie['id']);

    if ($movie_details) {
        if (isset($movie_details['videos']['results'])) {
            $trailers = array_filter($movie_details['videos']['results'], function($video) {
                return $video['type'] === 'Trailer';
            });
            if (!empty($trailers)) {
                $trailer_url = 'https://www.youtube.com/watch?v=' . reset($trailers)['key'];
                update_post_meta($post_id, 'trailer', esc_url_raw($trailer_url));
            }
        }

        if (isset($movie_details['credits']['cast'])) {
            $cast_members = array_map(function($cast) {
                return $cast['name'];
            }, $movie_details['credits']['cast']);
            update_post_meta($post_id, 'cast', implode(', ', $cast_members));
        }

        if (isset($movie_details['genres'])) {
            $genre_ids = array_map(function($genre) {
                return $genre['id'];
            }, $movie_details['genres']);

            $genre_names = array_map(function($genre) {
                return $genre['name'];
            }, $movie_details['genres']);

            wp_set_post_terms($post_id, $genre_ids, 'category');

            update_post_meta($post_id, 'genre', implode(', ', $genre_names));
        }

        if (isset($movie_details['images']['backdrops'])) {
            $gallery_urls = array_map(function($image) {
                return 'https://image.tmdb.org/t/p/original' . $image['file_path'];
            }, $movie_details['images']['backdrops']);
            update_post_meta($post_id, 'gallery', implode(', ', $gallery_urls));
        }
    }
}

function upload_image_from_url($image_url) {
    $image_name = basename($image_url);
    $upload_file = wp_upload_bits($image_name, null, file_get_contents($image_url));

    if (!$upload_file['error']) {
        $wp_filetype = wp_check_filetype($image_name, null);
        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title'     => sanitize_file_name($image_name),
            'post_content'   => '',
            'post_status'    => 'inherit',
        );

        $attachment_id = wp_insert_attachment($attachment, $upload_file['file']);
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload_file['file']);
        wp_update_attachment_metadata($attachment_id, $attachment_data);

        return $attachment_id;
    }

    return false;
}

// Enqueue the script for the popup
function enqueue_movie_popup_scripts() {
    wp_enqueue_style('fancybox-css', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css');
    wp_enqueue_script('fancybox-js', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js', array('jquery'), null, true);
    wp_enqueue_script('movie-popup-js', get_template_directory_uri() . '/includes/js/movie-popup.js', array('jquery', 'fancybox-js'), null, true);

    // Custom styles for movie popup
    wp_enqueue_style('movie-popup-css', get_template_directory_uri() . '/includes/css/movie-popup.css');

    // Enqueue contact form script
    wp_enqueue_script('movie-contact-form-script', plugin_dir_url(__FILE__) . 'includes/js/movie-contact-form.js', array('jquery'), null, true);
    wp_localize_script('movie-contact-form-script', 'movieContactForm', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('movie_contact_form_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_movie_popup_scripts');

// Enqueue the script for the contact form
function movie_database_enqueue_scripts() {
    wp_enqueue_script('movie-contact-form', plugin_dir_url(__FILE__) . 'includes/js/movie-contact-form.js', array('jquery'), MOVIE_DATABASE_VERSION, true);
    wp_localize_script('movie-contact-form', 'movieContactForm', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('movie_contact_form_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'movie_database_enqueue_scripts');

// Enqueue the script for the load more functionality
function movie_database_enqueue_load_more_scripts() {
    wp_enqueue_script('load-more-movies', plugin_dir_url(__FILE__) . 'includes/js/load-more-movies.js', array('jquery'), MOVIE_DATABASE_VERSION, true);
    wp_localize_script('load-more-movies', 'movieLoadMore', array(
        'api_url' => rest_url('movie-database/v1/movies'),
    ));
}
add_action('wp_enqueue_scripts', 'movie_database_enqueue_load_more_scripts');

// Enqueue the CSS for the movies
function movie_database_enqueue_styles() {
    wp_enqueue_style('movies-css', plugin_dir_url(__FILE__) . 'includes/css/movies.css');
    if (is_singular('movie')) {
        wp_enqueue_style('movie-database-styles', plugins_url('includes/css/movie-database.css', __FILE__));
    }
}
add_action('wp_enqueue_scripts', 'movie_database_enqueue_styles');

// Enqueue the necessary scripts and styles for the single movie page
function movie_database_enqueue_single_movie_scripts() {
    if (is_singular('movie')) {
        // Fancybox CSS
        wp_enqueue_style('fancybox-css', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css');
        // Fancybox JS
        wp_enqueue_script('fancybox-js', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js', array('jquery'), null, true);
        // Custom CSS
        wp_enqueue_style('movie-database-single-movie', plugins_url('includes/css/single-movie.css', __FILE__));
        // Custom JS
        wp_enqueue_script('movie-database-single-movie-js', plugins_url('includes/js/single-movie.js', __FILE__), array('jquery'), null, true);
    }
}
add_action('wp_enqueue_scripts', 'movie_database_enqueue_single_movie_scripts');

// Add admin menu
function movie_database_admin_menu() {
    add_menu_page(
        'Movie Database Settings',
        'Movie Database',
        'manage_options',
        'movie-database',
        'movie_database_settings_page',
        'dashicons-admin-generic',
        90
    );
}
add_action('admin_menu', 'movie_database_admin_menu');

// Admin page callback
function movie_database_settings_page() {
    require_once plugin_dir_path(__FILE__) . 'includes/templates/admin-page.php';
}

// Load custom templates
function load_custom_templates($template) {
    if (is_singular('movie')) {
        $template = plugin_dir_path(__FILE__) . 'includes/templates/single-movie.php';
    }
    return $template;
}
add_filter('template_include', 'load_custom_templates');

// Initialize the plugin
function run_movie_database() {
    $plugin = new Movie_Database();
    $plugin->run();

    $cpt = new Movie_Database_CPT();
    $contact_form = new Movie_Database_Contact_Form();
}
run_movie_database();

// Remove plugin update notification
add_filter('site_transient_update_plugins', 'remove_plugin_update_notification');

function remove_plugin_update_notification($value) {
    if (isset($value) && is_object($value)) {
        unset($value->response[plugin_basename(__FILE__)]);
    }
    return $value;
}

// Filter post content to show custom fields for 'movie' post type
function movie_database_display_custom_fields($content) {
    if (is_singular('movie') && in_the_loop() && is_main_query()) {
        $post_id = get_the_ID();
        $rating = get_post_meta($post_id, 'rating', true);
        $release_date = get_post_meta($post_id, 'release_date', true);
        $cast = get_post_meta($post_id, 'cast', true);
        $trailer = get_post_meta($post_id, 'trailer', true);
        $gallery = get_post_meta($post_id, 'gallery', true);
        $genres = wp_get_post_terms($post_id, 'category');

        $custom_content = '';

        if ($rating) {
            $custom_content .= '<p><strong>' . __('Rating:', 'movie-database') . '</strong> ' . esc_html($rating) . '</p>';
        }

        if ($release_date) {
            $custom_content .= '<p><strong>' . __('Release Date:', 'movie-database') . '</strong> ' . esc_html($release_date) . '</p>';
        }

        if ($cast) {
            $custom_content .= '<p><strong>' . __('Cast:', 'movie-database') . '</strong> ' . esc_html($cast) . '</p>';
        }

        if ($trailer) {
            $custom_content .= '<p><strong>' . __('Trailer:', 'movie-database') . '</strong> <a href="' . esc_url($trailer) . '" target="_blank">' . __('Watch Trailer', 'movie-database') . '</a></p>';
        }

        if ($gallery && is_array($gallery)) {
            $custom_content .= '<div><strong>' . __('Gallery:', 'movie-database') . '</strong>';
            foreach ($gallery as $image) {
                $custom_content .= '<img src="' . esc_url(trim($image)) . '" alt="" style="max-width:100%;height:auto;margin-bottom:10px;">';
            }
            $custom_content .= '</div>';
        }

        if (!empty($genres)) {
            $custom_content .= '<p><strong>' . __('Genres:', 'movie-database') . '</strong> ';
            $genre_names = array();
            foreach ($genres as $genre) {
                $genre_names[] = esc_html($genre->name);
            }
            $custom_content .= implode(', ', $genre_names) . '</p>';
        }

        $content .= $custom_content;
    }
    return $content;
}
add_filter('the_content', 'movie_database_display_custom_fields');
?>
