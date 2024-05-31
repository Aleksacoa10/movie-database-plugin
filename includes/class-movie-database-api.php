<?php
class Movie_Database_API {

    private $api_key;

    public function __construct() {
        // Povlačenje API ključa iz postavki WordPress-a
        $this->api_key = get_option('movie_db_api_key', '42e22b1fffb5a03624bf9db933df13a1');
    }

    // Povlačenje popularnih filmova
    public function fetch_popular_movies() {
        $url = 'https://api.themoviedb.org/3/movie/popular?api_key=' . $this->api_key;
        $response = wp_remote_get($url);

        if (is_wp_error($response)) {
            return [];
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (isset($data['results'])) {
            return $data['results'];
        }

        return [];
    }

    // Povlačenje detalja o pojedinačnom filmu, uključujući kredite, video i slike
    public function fetch_movie_data($movie_id) {
        $url = 'https://api.themoviedb.org/3/movie/' . $movie_id . '?api_key=' . $this->api_key . '&append_to_response=credits,videos,images';
        $response = wp_remote_get($url);

        if (is_wp_error($response)) {
            return null;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        // Logovanje odgovora API-ja za debugging
        error_log(print_r($data, true));

        return $data;
    }
}
?>
