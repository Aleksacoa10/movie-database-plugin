<?php
class Movie_Database_Admin {

    public function __construct() {
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function register_settings() {
        register_setting('movie_database_settings_group', 'movie_database_api_token');
    }

    public function settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Admin Movie Database Settings', 'movie-database'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('movie_database_settings_group');
                do_settings_sections('movie_database_settings_group');
                ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php _e('API Token', 'movie-database'); ?></th>
                        <td><input type="text" name="movie_database_api_token" value="<?php echo esc_attr(get_option('movie_database_api_token')); ?>" /></td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>

            <h2><?php _e('Shortcode for Movie List', 'movie-database'); ?></h2>
            <p><?php _e('Use the following shortcode to display the contact form:', 'movie-database'); ?></p>
            <p><code id="movie-contact-form-shortcode">[movie_list]</code> <button type="button" onclick="copyShortcode()"><?php _e('Copy', 'movie-database'); ?></button></p>
        </div>
        <script>
        function copyShortcode() {
            var copyText = document.getElementById("movie-contact-form-shortcode");
            var range = document.createRange();
            range.selectNode(copyText);
            window.getSelection().removeAllRanges(); 
            window.getSelection().addRange(range); 
            document.execCommand("copy");
            window.getSelection().removeAllRanges();
            alert("Shortcode copied: " + copyText.textContent);
        }
        </script>
        <?php
    }
}
new Movie_Database_Admin();
