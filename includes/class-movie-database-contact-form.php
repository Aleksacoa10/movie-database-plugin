<?php
class Movie_Database_Contact_Form {

    public function __construct() {
        add_shortcode('movie_contact_form', array($this, 'render_contact_form'));
        add_action('wp_ajax_nopriv_movie_contact_form', array($this, 'handle_form_submission'));
        add_action('wp_ajax_movie_contact_form', array($this, 'handle_form_submission'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    public function enqueue_scripts() {
        wp_enqueue_script('movie-contact-form-script', plugin_dir_url(__FILE__) . 'js/movie-contact-form.js', array('jquery'), null, true);
        wp_localize_script('movie-contact-form-script', 'movieContactForm', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('movie_contact_form_nonce')
        ));
    }

    public function render_contact_form($atts = [], $content = null) {
        $atts = shortcode_atts(
            array(
                'movie_title' => '',
            ),
            $atts,
            'movie_contact_form'
        );

        ob_start();
        ?>
        <form id="movie-contact-form">
            <label for="name">Ime i Prezime:</label>
            <input type="text" id="name" name="name" required><br>

            <label for="email">Email adresa:</label>
            <input type="email" id="email" name="email" required><br>

            <label for="movie">Naziv filma:</label>
            <input type="text" id="movie" name="movie" value="<?php echo esc_attr($atts['movie_title']); ?>" required readonly><br>

            <label for="opinion">Mišljenje:</label>
            <textarea id="opinion" name="opinion" required></textarea><br>

            <button type="submit">Pošalji</button>
            <p id="form-message"></p>
        </form>
        <?php
        return ob_get_clean();
    }

    public function handle_form_submission() {
        check_ajax_referer('movie_contact_form_nonce', 'security');

        // Validacija podataka
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $movie = sanitize_text_field($_POST['movie']);
        $opinion = sanitize_textarea_field($_POST['opinion']);

        if (empty($name) || empty($email) || empty($movie) || empty($opinion)) {
            wp_send_json_error('Sva polja su obavezna.');
        }

        if (!is_email($email)) {
            wp_send_json_error('Unesite validnu email adresu.');
        }

        // Podaci za slanje email-a
        $to = get_option('admin_email'); 
        $subject = 'Novi kontakt sajt Movie Database';
        $message = "Ime i Prezime: $name\nEmail adresa: $email\nNaziv filma: $movie\nMišljenje: $opinion";
        $headers = array('Content-Type: text/plain; charset=UTF-8');

        // Slanje email-a
        $sent = wp_mail($to, $subject, $message, $headers);

        if ($sent) {
            wp_send_json_success('Poruka je uspešno poslata!');
        } else {
            wp_send_json_error('Došlo je do greške pri slanju poruke. Pokušajte ponovo.');
        }
    }
}
new Movie_Database_Contact_Form();
?>
