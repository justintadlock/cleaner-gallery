<?php
/**
 * Plugin Name: Cleaner Gallery
 * Plugin URI:  http://themehybrid.com/plugins/cleaner-gallery
 * Description: Replaces the default <code>[gallery]</code> shortcode with valid <abbr title="Hypertext Markup Language">HTML</abbr>5 markup and moves its inline styles to a proper stylesheet. Integrates with many Lightbox-type image scripts.
 * Version:     1.1.0
 * Author:      Justin Tadlock
 * Author URI:  http://justintadlock.com
 * Text Domain: cleaner-gallery
 * Domain Path: /languages
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License as published by the Free Software Foundation; either version 2 of the License, 
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * You should have received a copy of the GNU General Public License along with this program; if not, write 
 * to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @package   CleanerGallery
 * @version   1.1.0
 * @author    Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2008 - 2014, Justin Tadlock
 * @link      http://themehybrid.com/plugins/cleaner-gallery
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Sets up the Cleaner Gallery plugin.
 *
 * @since  1.0.0
 */
final class Cleaner_Gallery_Plugin {

	/**
	 * Holds the instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    object
	 */
	private static $instance;

	/**
	 * Stores the directory path for this plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string
	 */
	private $directory_path;

	/**
	 * Stores the directory URI for this plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string
	 */
	private $directory_uri;

	/**
	 * Plugin setup.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function __construct() {

		/* Set the properties needed by the plugin. */
		add_action( 'plugins_loaded', array( $this, 'setup' ), 1 );

		/* Internationalize the text strings used. */
		add_action( 'plugins_loaded', array( $this, 'i18n' ), 2 );

		/* Load the functions files. */
		add_action( 'plugins_loaded', array( $this, 'includes' ), 3 );

		/* Load the admin files. */
		add_action( 'plugins_loaded', array( $this, 'admin' ), 4 );

		/* Check theme support for 'cleaner-gallery'. */
		add_action( 'after_setup_theme', array( $this, 'theme_support' ), 25 );

		/* Enqueue scripts and styles. */
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 15 );
	}

	/**
	 * Defines the directory path and URI for the plugin.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function setup() {
		$this->directory_path = trailingslashit( plugin_dir_path( __FILE__ ) );
		$this->directory_uri  = trailingslashit( plugin_dir_url(  __FILE__ ) );

		/* Legacy */
		define( 'CLEANER_GALLERY_DIR', $this->directory_path );
		define( 'CLEANER_GALLERY_URI', $this->directory_uri  );
	}

	/**
	 * Loads the initial files needed by the plugin.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function includes() {

		require_once( "{$this->directory_path}inc/gallery.php"         );
		require_once( "{$this->directory_path}inc/default-filters.php" );
	}

	/**
	 * Loads the translation files.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function i18n() {

		/* Load the translation of the plugin. */
		load_plugin_textdomain( 'cleaner-gallery', false, 'cleaner-gallery/languages' );
	}

	/**
	 * Loads the admin functions and files.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function admin() {

		if ( is_admin() )
			require_once( "{$this->directory_path}admin/admin.php" );
	}

	/**
	 * Checks for theme support of the 'cleaner-gallery' extension. This is used in the Hybrid 
	 * Core framework, so we want to make sure we're only loading the plugin stylesheet if the 
	 * theme is not handling styling for the gallery.
	 *
	 * @since  1.1.0
	 * @access public
	 * @return void
	 */
	public function theme_support() {

		if ( !current_theme_supports( 'cleaner-gallery' ) )
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
	}

	/**
	 * Enqueues scripts and styles on the front end.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue_scripts() {

		if ( cleaner_gallery_get_setting( 'thickbox_js' ) )
			wp_enqueue_script( 'thickbox' );

		if ( cleaner_gallery_get_setting( 'thickbox_css' ) )
			wp_enqueue_style( 'thickbox' );
	}


	/**
	 * Enqueues the Cleaner Gallery stylesheet.
	 *
	 * @since  1.1.0
	 * @access public
	 * @return void
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'cleaner-gallery', "{$this->directory_uri}css/gallery.min.css", null, '20130526' );
	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		if ( !self::$instance )
			self::$instance = new self;

		return self::$instance;
	}
}

Cleaner_Gallery_Plugin::get_instance();
