<?php
/**
 * Plugin Name: Cleaner Gallery
 * Plugin URI: http://justintadlock.com/archives/2008/04/13/cleaner-wordpress-gallery-plugin
 * Description: This plugin replaces the default gallery feature with a valid XHTML solution and offers support for multiple Lightbox-type image scripts.
 * Version: 0.8
 * Author: Justin Tadlock
 * Author URI: http://justintadlock.com
 *
 * Cleaner Gallery cleans up the invalid XHTML created by the default [gallery] 
 * shortcode. It was created with this purpose in mind.  But, the plugin has since
 * grown into a much larger set of functions with many options.  It integrates nicely
 * with several Lightbox-type scripts and has an options page that gives you much
 * more control over galleries across your site.
 *
 * Users should follow the Codex on using the [gallery] shortcode:
 * @link http://codex.wordpress.org/Using_the_gallery_shortcode
 *
 * Developers can learn more about the WordPress shortcode API:
 * @link http://codex.wordpress.org/Shortcode_API
 *
 * This plugin has been tested and integrates with these scripts:
 * @link http://www.huddletogether.com/projects/lightbox2
 * @link http://www.digitalia.be/software/slimbox
 * @link http://www.digitalia.be/software/slimbox2
 * @link http://jquery.com/demo/thickbox
 * @link http://dolem.com/lytebox
 * @link http://orangoo.com/labs/GreyBox
 * @link http://www.nickstakenburg.com/projects/lightview
 * @link http://www.balupton.com/sandbox/jquery_lightbox
 * @link http://leandrovieira.com/projects/jquery/lightbox
 * @link http://www.laptoptips.ca/projects/wp-shutter-reloaded
 * @link http://mjijackson.com/shadowbox/index.html
 * @link http://fancy.klade.lv
 * @link http://github.com/krewenki/jquery-lightbox/tree/master
 * @link http://www.stickmanlabs.com/lightwindow
 * @link http://www.cabel.name/2008/02/fancyzoom-10.html
 * @link http://randomous.com/floatbox/home
 * @link http://colorpowered.com/colorbox
 *
 * @copyright 2008 - 2010
 * @version 0.8
 * @author Justin Tadlock
 * @link http://justintadlock.com/archives/2008/04/13/cleaner-wordpress-gallery-plugin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package CleanerGallery
 */

/* Set up the plugin. */
add_action( 'plugins_loaded', 'cleaner_gallery_setup' );

/**
 * Sets up the Cleaner Gallery plugin and loads files at the appropriate time.
 *
 * @since 0.8
 */
function cleaner_gallery_setup() {
	/* Load translations. */
	load_plugin_textdomain( 'cleaner-gallery', false, 'cleaner-gallery/languages' );

	/* Set constant path to the Cleaner Gallery plugin directory. */
	define( 'CLEANER_GALLERY_DIR', plugin_dir_path( __FILE__ ) );

	/* Set constant path to the Cleaner Gallery plugin URL. */
	define( 'CLEANER_GALLERY_URL', plugin_dir_url( __FILE__ ) );

	if ( is_admin() )
		require_once( CLEANER_GALLERY_DIR . 'admin.php' );
	else
		require_once( CLEANER_GALLERY_DIR . 'gallery.php' );

	do_action( 'cleaner_gallery_loaded' );
}

/**
 * Function for quickly grabbing settings for the plugin without having to call get_option() 
 * every time we need a setting.
 *
 * @since 0.8
 */
function cleaner_gallery_get_setting( $option = '' ) {
	global $cleaner_gallery;

	if ( !$option )
		return false;

	if ( !isset( $cleaner_gallery->settings ) )
		$cleaner_gallery->settings = get_option( 'cleaner_gallery_settings' );

	return $cleaner_gallery->settings[$option];
}

?>