=== Movie Database ===
Contributors: Aleksa
Tags: movies, database, custom post type, TMDb
Requires at least: 5.2
Tested up to: 5.7
Requires PHP: 7.4
Stable tag: 1.0.0

== Description ==
A WordPress plugin to manage a movie database with custom post types and taxonomies. This plugin integrates with The Movie Database (TMDb) API to fetch and display movie data.

== Installation ==
1. Upload `movie-database` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Configure the plugin settings with your TMDb API key.
4. Use the Movies post type to add new movies.

== Features ==
* Custom Post Types: Manage movies with custom fields for genre, cast, trailer, and other movie details.
* TMDb API Integration: Fetch movie details from The Movie Database.
* Shortcodes: Display movies and movie details using shortcodes.
* REST API: Provides REST API endpoints for fetching movie data.
* Contact Form: Includes a contact form for user inquiries.
* Admin Settings: Manage plugin settings and fetch popular movies from TMDb.
* Multilingual Support: Translation files included for Serbian (sr_RS).

== Description of Decisions ==
* **WP Codex**: The plugin follows the WP Codex for creating custom post types and taxonomies.
* **Code Style**: The code adheres to WordPress coding standards.
* **Object-Oriented Design**: The plugin is structured using classes to encapsulate functionality.
* **Translation-ready**: The plugin is ready for translation, with a `.pot` file included.
* **WP Nonces**: Nonces are used to verify form submissions for custom fields.
* **Sanitizing, Validating, and Escaping**: All data is sanitized, validated, and escaped to ensure security.
* **Activation/Deactivation**: Hooks are used for plugin activation and deactivation to flush rewrite rules.
* **Class Structure**: A separate class `Movie_Database_CPT` is used to handle the registration of custom post types and taxonomies for better code organization and readability.
* **Custom Fields**: Custom fields for gallery, rating, release date, genre, cast, and trailer are added to the Movie custom post type.

== File Structure and Decisions ==
* **movie-database.php**: The main plugin file that initializes the plugin and registers activation/deactivation hooks.
* **uninstall.php**: Handles the uninstallation process, ensuring all plugin data is removed from the database.
* **includes/**:
  * **class-movie-database-activator.php**: Contains code to execute upon plugin activation.
  * **class-movie-database-admin.php**: Manages admin area functionalities and settings page.
  * **class-movie-database-api.php**: Handles communication with the TMDb API.
  * **class-movie-database-contact-form.php**: Provides the functionality for the contact form.
  * **class-movie-database-cpt.php**: Registers custom post types and taxonomies for movies.
  * **class-movie-database-deactivator.php**: Contains code to execute upon plugin deactivation.
  * **class-movie-database-rest-api.php**: Registers REST API endpoints for fetching movie data.
  * **class-movie-database-shortcodes.php**: Defines shortcodes for displaying movie content.
  * **class-movie-database.php**: Main class file that orchestrates the plugin's functionality.
* **css/**: Contains stylesheets for the plugin's front-end and back-end.
  * **movie-popup.css**: Styles for movie popups.
  * **movies.css**: General styles for movie listings.
  * **single-movie.css**: Styles for single movie pages.
* **js/**: Contains JavaScript files for enhanced interactivity.
  * **load-more-movies.js**: Adds 'load more' functionality for movie listings.
  * **movie-contact-form.js**: Handles contact form submissions.
  * **movie-popup.js**: Manages movie popups.
  * **single-movie.js**: Enhances single movie page interactions.
* **templates/**: Contains template files used by the plugin.
  * **admin-page.php**: Template for the admin settings page.
  * **single-movie.php**: Template for displaying individual movie details.
* **languages/**: Contains translation files for the plugin.
  * **movie-database-sr_RS.mo**: Compiled translation file for Serbian.
  * **movie-database-sr_RS.po**: Source translation file for Serbian.

== Deinstallation Instructions ==
To uninstall the Movie Database plugin:
1. Deactivate the plugin through the 'Plugins' screen in WordPress.
2. Click 'Delete' to completely remove the plugin and its data.

This will delete all custom post types, taxonomies, and metadata associated with the plugin.

== Credits ==
This plugin was developed by [Aleksa].
