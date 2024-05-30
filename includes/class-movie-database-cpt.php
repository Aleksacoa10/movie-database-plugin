<?php
class Movie_Database_CPT {

    public function __construct() {
        add_action('init', array($this, 'register_cpt_movie'));
        add_action('init', array($this, 'register_taxonomy_category'));
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_boxes'));
    }

    public function register_cpt_movie() {
        $labels = array(
            'name'                  => _x('Movies', 'Post Type General Name', 'movie-database'),
            'singular_name'         => _x('Movie', 'Post Type Singular Name', 'movie-database'),
            'menu_name'             => __('Filmovi', 'Admin Menu text', 'movie-database'), 
            'name_admin_bar'        => __('Movie', 'movie-database'),
            'archives'              => __('Movie Archives', 'movie-database'),
            'attributes'            => __('Movie Attributes', 'movie-database'),
            'parent_item_colon'     => __('Parent Movie:', 'movie-database'),
            'all_items'             => __('All Movies', 'movie-database'),
            'add_new_item'          => __('Add New Movie', 'movie-database'),
            'add_new'               => __('Add New', 'movie-database'),
            'new_item'              => __('New Movie', 'movie-database'),
            'edit_item'             => __('Edit Movie', 'movie-database'),
            'update_item'           => __('Update Movie', 'movie-database'),
            'view_item'             => __('View Movie', 'movie-database'),
            'view_items'            => __('View Movies', 'movie-database'),
            'search_items'          => __('Search Movie', 'movie-database'),
            'not_found'             => __('Not found', 'movie-database'),
            'not_found_in_trash'    => __('Not found in Trash', 'movie-database'),
            'featured_image'        => __('Featured Image', 'movie-database'),
            'set_featured_image'    => __('Set featured image', 'movie-database'),
            'remove_featured_image' => __('Remove featured image', 'movie-database'),
            'use_featured_image'    => __('Use as featured image', 'movie-database'),
            'insert_into_item'      => __('Insert into movie', 'movie-database'),
            'uploaded_to_this_item' => __('Uploaded to this movie', 'movie-database'),
            'items_list'            => __('Movies list', 'movie-database'),
            'items_list_navigation' => __('Movies list navigation', 'movie-database'),
            'filter_items_list'     => __('Filter movies list', 'movie-database'),
        );
        $args = array(
            'label'                 => __('Movie', 'movie-database'),
            'description'           => __('A custom post type for movies', 'movie-database'),
            'labels'                => $labels,
            'supports'              => array('title', 'editor', 'thumbnail'),
            'taxonomies'            => array('category'),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'menu_icon'             => 'dashicons-video-alt2',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
        );
        register_post_type('movie', $args);
    }

    public function register_taxonomy_category() {
        $labels = array(
            'name'                       => _x('Categories', 'Taxonomy General Name', 'movie-database'),
            'singular_name'              => _x('Category', 'Taxonomy Singular Name', 'movie-database'),
            'menu_name'                  => __('Categories', 'movie-database'),
            'all_items'                  => __('All Categories', 'movie-database'),
            'parent_item'                => __('Parent Category', 'movie-database'),
            'parent_item_colon'          => __('Parent Category:', 'movie-database'),
            'new_item_name'              => __('New Category Name', 'movie-database'),
            'add_new_item'               => __('Add New Category', 'movie-database'),
            'edit_item'                  => __('Edit Category', 'movie-database'),
            'update_item'                => __('Update Category', 'movie-database'),
            'view_item'                  => __('View Category', 'movie-database'),
            'separate_items_with_commas' => __('Separate categories with commas', 'movie-database'),
            'add_or_remove_items'        => __('Add or remove categories', 'movie-database'),
            'choose_from_most_used'      => __('Choose from the most used', 'movie-database'),
            'popular_items'              => __('Popular Categories', 'movie-database'),
            'search_items'               => __('Search Categories', 'movie-database'),
            'not_found'                  => __('Not Found', 'movie-database'),
            'no_terms'                   => __('No categories', 'movie-database'),
            'items_list'                 => __('Categories list', 'movie-database'),
            'items_list_navigation'      => __('Categories list navigation', 'movie-database'),
        );
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
        );
        register_taxonomy('category', array('movie'), $args);
    }

    public function add_meta_boxes() {
        add_meta_box('movie_details_meta_box', __('Movie Details', 'movie-database'), array($this, 'display_meta_boxes'), 'movie', 'normal', 'high');
    }

    public function display_meta_boxes($post) {
        $rating = get_post_meta($post->ID, 'rating', true);
        $release_date = get_post_meta($post->ID, 'release_date', true);
        $cast = get_post_meta($post->ID, 'cast', true);
        $trailer = get_post_meta($post->ID, 'trailer', true);
        $gallery = get_post_meta($post->ID, 'gallery', true);
        $genre = get_post_meta($post->ID, 'genre', true);

        wp_nonce_field('movie_details_nonce', 'movie_details_nonce_field');
        ?>
        <p>
            <label for="gallery"><?php _e('Gallery', 'movie-database'); ?></label>
            <textarea id="gallery" name="gallery"><?php echo esc_textarea($gallery); ?></textarea>
        </p>
        <p>
            <label for="rating"><?php _e('Rating', 'movie-database'); ?></label>
            <input type="text" id="rating" name="rating" value="<?php echo esc_attr($rating); ?>" />
        </p>
        <p>
            <label for="release_date"><?php _e('Release Date', 'movie-database'); ?></label>
            <input type="date" id="release_date" name="release_date" value="<?php echo esc_attr($release_date); ?>" />
        </p>
        <p>
            <label for="genre"><?php _e('Genre', 'movie-database'); ?></label>
            <input type="text" id="genre" name="genre" value="<?php echo esc_attr($genre); ?>" />
        </p>
        <p>
            <label for="cast"><?php _e('Cast', 'movie-database'); ?></label>
            <textarea id="cast" name="cast"><?php echo esc_textarea($cast); ?></textarea>
        </p>
        <p>
            <label for="trailer"><?php _e('Trailer', 'movie-database'); ?></label>
            <input type="url" id="trailer" name="trailer" value="<?php echo esc_attr($trailer); ?>" />
        </p>
        <?php
    }

    public function save_meta_boxes($post_id) {
        if (!isset($_POST['movie_details_nonce_field']) || !wp_verify_nonce($_POST['movie_details_nonce_field'], 'movie_details_nonce')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (isset($_POST['rating'])) {
            update_post_meta($post_id, 'rating', sanitize_text_field($_POST['rating']));
        }

        if (isset($_POST['release_date'])) {
            update_post_meta($post_id, 'release_date', sanitize_text_field($_POST['release_date']));
        }

        if (isset($_POST['cast'])) {
            update_post_meta($post_id, 'cast', sanitize_textarea_field($_POST['cast']));
        }

        if (isset($_POST['trailer'])) {
            update_post_meta($post_id, 'trailer', esc_url_raw($_POST['trailer']));
        }

        if (isset($_POST['gallery'])) {
            update_post_meta($post_id, 'gallery', sanitize_textarea_field($_POST['gallery']));
        }

        if (isset($_POST['genre'])) {
            update_post_meta($post_id, 'genre', sanitize_text_field($_POST['genre']));
        }

        $this->update_movie_meta_from_api($post_id);
    }

    public function update_movie_meta_from_api($post_id) {
        if (get_post_type($post_id) !== 'movie') {
            return;
        }

        $api = new Movie_Database_API(get_option('movie_database_api_token'));
        $data = $api->fetch_movie_data($post_id);

        if ($data) {
            if (isset($data['genres'])) {
                $genres = array_column($data['genres'], 'name');
                update_post_meta($post_id, 'genre', implode(', ', $genres));
            }

            if (isset($data['credits']['cast'])) {
                $cast_members = array_map(function($cast) {
                    return $cast['name'];
                }, $data['credits']['cast']);
                update_post_meta($post_id, 'cast', implode(', ', $cast_members));
            }

            if (isset($data['videos']['results'])) {
                $trailers = array_filter($data['videos']['results'], function($video) {
                    return $video['type'] === 'Trailer';
                });
                if (!empty($trailers)) {
                    $trailer_url = 'https://www.youtube.com/watch?v=' . reset($trailers)['key'];
                    update_post_meta($post_id, 'trailer', esc_url_raw($trailer_url));
                }
            }

            if (isset($data['vote_average'])) {
                update_post_meta($post_id, 'rating', sanitize_text_field($data['vote_average']));
            }

            if (isset($data['release_date'])) {
                update_post_meta($post_id, 'release_date', sanitize_text_field($data['release_date']));
            }

            if (isset($data['images']['backdrops'])) {
                $gallery_urls = array_map(function($image) {
                    return 'https://image.tmdb.org/t/p/original' . $image['file_path'];
                }, $data['images']['backdrops']);
                update_post_meta($post_id, 'gallery', implode(', ', $gallery_urls));
            }
        }
    }
}
?>
