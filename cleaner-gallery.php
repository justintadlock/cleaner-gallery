<?php
/**
 * Plugin Name: Cleaner Gallery
 * Plugin URI: http://justintadlock.com/archives/2008/04/13/cleaner-wordpress-gallery-plugin
 * Description: This plugin replaces the default gallery feature with a valid XHTML solution and offers support for multiple Lightbox-type image scripts.
 * Version: 1.0.0-alpha-1
 * Author: Justin Tadlock
 * Author URI: http://justintadlock.com
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume 
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package CleanerGallery
 * @version 1.0.0
 * @author Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2008 - 2010, Justin Tadlock
 * @link http://justintadlock.com/archives/2008/04/13/cleaner-wordpress-gallery-plugin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
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

		/* @todo - drop this */
		global $cleaner_gallery;
		$cleaner_gallery = new stdClass;
		/* ===================== */

		/* Set the properties needed by the plugin. */
		add_action( 'plugins_loaded', array( $this, 'setup' ), 1 );

		/* Internationalize the text strings used. */
		add_action( 'plugins_loaded', array( $this, 'i18n' ), 2 );

		/* Load the functions files. */
		add_action( 'plugins_loaded', array( $this, 'includes' ), 3 );

		/* Load the admin files. */
		add_action( 'plugins_loaded', array( $this, 'admin' ), 4 );

		/* Enqueue scripts and styles. */
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 15 );

		/* Register admin scripts and styles. */
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_register_scripts' ), 5 );
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
		load_plugin_textdomain( 'custom-background-extended', false, 'custom-background-extended/languages' );
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
			require_once( "{$this->directory_path}admin/settings.php" );
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

		if ( !current_theme_supports( 'cleaner-gallery' ) )
			wp_enqueue_style( 'cleaner-gallery', "{$this->directory_uri}css/gallery.css", null, '20130526' );
	}

	/**
	 * Registers scripts and styles for use in the WordPress admin (does not load theme).
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function admin_register_scripts() {
		wp_register_style( 'cleaner-gallery-admin', "{$this->directory_uri}css/admin.css", null, '20130526' );
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

?>