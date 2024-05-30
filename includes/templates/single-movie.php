<?php
get_header();
if (have_posts()) :
    while (have_posts()) : the_post(); ?>
        <div class="movie-single">
            <div class="movie-image">
                <?php the_post_thumbnail('full'); ?>
            </div>
            <div class="movie-details">
                <h1 class="movie-title"><?php the_title(); ?></h1>
                <div class="movie-genre">
                    <strong><?php _e('Genres:', 'movie-database'); ?></strong>
                    <?php
                    $genres = get_post_meta($post->ID, 'genre', true);
                    if ($genres) {
                        echo esc_html($genres);
                    } else {
                        $terms = get_the_terms($post->ID, 'category');
                        if ($terms && !is_wp_error($terms)) {
                            $genre_list = array();
                            foreach ($terms as $term) {
                                $genre_list[] = $term->name;
                            }
                            echo implode(', ', $genre_list);
                        }
                    }
                    ?>
                </div>
                <div class="movie-description">
                    <?php
                    remove_filter('the_content', 'movie_database_display_custom_fields');
                    the_content();
                    add_filter('the_content', 'movie_database_display_custom_fields');
                    ?>
                </div>
                <?php if ($rating = get_post_meta($post->ID, 'rating', true)): ?>
                    <div class="movie-rating">
                        <strong><?php _e('Rating:', 'movie-database'); ?></strong>
                        <?php echo esc_html($rating); ?>
                    </div>
                <?php endif; ?>
                <?php if ($release_date = get_post_meta($post->ID, 'release_date', true)): ?>
                    <div class="movie-release-date">
                        <strong><?php _e('Release Date:', 'movie-database'); ?></strong>
                        <?php echo esc_html($release_date); ?>
                    </div>
                <?php endif; ?>
                <?php if ($cast = get_post_meta($post->ID, 'cast', true)): ?>
                    <div class="movie-cast">
                        <strong><?php _e('Cast:', 'movie-database'); ?></strong>
                        <?php echo esc_html($cast); ?>
                    </div>
                <?php endif; ?>
                <?php if ($gallery = get_post_meta($post->ID, 'gallery', true)): ?>
                    <div class="movie-gallery">
                        <strong><?php _e('Gallery:', 'movie-database'); ?></strong>
                        <div class="gallery-scroller">
                            <?php
                            $gallery_array = explode(', ', $gallery);
                            foreach (array_unique($gallery_array) as $image) {
                                echo '<a href="' . esc_url(trim($image)) . '" data-fancybox="gallery"><img src="' . esc_url(trim($image)) . '" alt="" /></a>';
                            }
                            ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($trailer = get_post_meta($post->ID, 'trailer', true)): ?>
                    <div class="movie-trailer">
                        <strong><?php _e('Trailer:', 'movie-database'); ?></strong>
                        <?php
                        $embed_url = '';
                        if (strpos($trailer, 'youtube.com') !== false || strpos($trailer, 'youtu.be') !== false) {
                            preg_match('/(youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $trailer, $matches);
                            if (isset($matches[2])) {
                                $embed_url = 'https://www.youtube.com/embed/' . $matches[2];
                            }
                        } elseif (strpos($trailer, 'vimeo.com') !== false) {
                            preg_match('/vimeo\.com\/([0-9]+)/', $trailer, $matches);
                            if (isset($matches[1])) {
                                $embed_url = 'https://player.vimeo.com/video/' . $matches[1];
                            }
                        }

                        if ($embed_url) {
                            echo '<iframe width="560" height="315" src="' . esc_url($embed_url) . '" frameborder="0" allowfullscreen></iframe>';
                        } else {
                            echo '<p>' . __('Invalid trailer URL.', 'movie-database') . '</p>';
                        }
                        ?>
                    </div>
                <?php endif; ?>
                <!-- Contact form -->
                <?php echo do_shortcode('[movie_contact_form movie_title="' . get_the_title() . '"]'); ?>
            </div>
        </div>
    <?php endwhile;
endif;
get_footer();
?>
