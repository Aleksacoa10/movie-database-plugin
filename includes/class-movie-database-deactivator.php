<?php
class Movie_Database_Deactivator {
    public static function deactivate() {
        flush_rewrite_rules();
    }
}

