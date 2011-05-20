<?php
/**
 * Plugin Name: Cleaner Gallery
 * Plugin URI: http://justintadlock.com/archives/2008/04/13/cleaner-wordpress-gallery-plugin
 * Description: This plugin replaces the default gallery feature with a valid XHTML solution and offers support for multiple Lightbox-type image scripts.
 * Version: 0.9.2
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
 * @version 0.9.2
 * @author Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2008 - 2011, Justin Tadlock
 * @link http://justintadlock.com/archives/2008/04/13/cleaner-wordpress-gallery-plugin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Set up the plugin. */
add_action( 'plugins_loaded', 'cleaner_gallery_setup' );

/**
 * Sets up the Cleaner Gallery plugin and loads files at the appropriate time.
 *
 * @since 0.8.0
 */
function cleaner_gallery_setup() {

	/* Set constant path to the Cleaner Gallery plugin directory. */
	define( 'CLEANER_GALLERY_DIR', plugin_dir_path( __FILE__ ) );

	/* Set constant path to the Cleaner Gallery plugin URL. */
	define( 'CLEANER_GALLERY_URL', plugin_dir_url( __FILE__ ) );

	if ( is_admin() ) {

		/* Load translations. */
		load_plugin_textdomain( 'cleaner-gallery', false, 'cleaner-gallery/languages' );

		/* Load the plugin's admin file. */
		require_once( CLEANER_GALLERY_DIR . 'admin.php' );
	}

	else {
		/* Load the gallery shortcode functionality. */
		require_once( CLEANER_GALLERY_DIR . 'gallery.php' );

		/* Filter the gallery images with user options. */
		add_filter( 'cleaner_gallery_image', 'cleaner_gallery_plugin_gallery_image', 10, 4 );

		/* Filter the gallery captions with user options. */
		add_filter( 'cleaner_gallery_caption', 'cleaner_gallery_plugin_image_caption', 10, 3 );

		/* Load any scripts needed. */
		add_action( 'template_redirect', 'cleaner_gallery_enqueue_script' );

		/* Load any stylesheets needed. */
		add_action( 'template_redirect', 'cleaner_gallery_enqueue_style' );

		/* Filter the cleaner gallery default shortcode attributes. */
		add_filter( 'cleaner_gallery_defaults', 'cleaner_gallery_default_args' );
	}
}

/**
 * Function for quickly grabbing settings for the plugin without having to call get_option() 
 * every time we need a setting.
 *
 * @since 0.8.0
 */
function cleaner_gallery_get_setting( $option = '' ) {
	global $cleaner_gallery;

	if ( !$option )
		return false;

	if ( !isset( $cleaner_gallery->settings ) )
		$cleaner_gallery->settings = get_option( 'cleaner_gallery_settings' );

	if ( !is_array( $cleaner_gallery->settings ) || empty( $cleaner_gallery->settings[$option] ) )
		return false;

	return $cleaner_gallery->settings[$option];
}

/**
 * Modifies the gallery captions according to user-selected settings.
 *
 * @since 0.9.0
 */
function cleaner_gallery_plugin_image_caption( $caption, $id, $attr ) {

	/* If the caption should be removed, return empty string. */
	if ( cleaner_gallery_get_setting( 'caption_remove' ) )
		return '';

	/* If the caption is empty and the user is using the title as a caption, get the image title. */
	if ( empty( $caption ) && cleaner_gallery_get_setting( 'caption_title' ) ) {
		$post = get_post( $id );
		$caption = wptexturize( esc_html( $post->post_title ) );
	}

	/* If there's a caption and it should be linked, link to the attachment page. */
	if ( !empty( $caption ) && cleaner_gallery_get_setting( 'caption_link' ) )
		$caption = wp_get_attachment_link( $id, false, true, false, $caption );

	/* Return the caption. */
	return $caption;
}

/**
 * Modifies gallery images based on user-selected settings.
 *
 * @since 0.9.0
 */
function cleaner_gallery_plugin_gallery_image( $image, $id, $attr, $instance ) {

	/* If the image should link to nothing, remove the image link. */
	if ( 'none' == $attr['link'] ) {
		$image = preg_replace( '/<a.*?>(.*?)<\/a>/', '$1', $image );
	}

	/* If the image should link to the 'file' (full-size image), add in extra link attributes. */
	elseif ( 'file' == $attr['link'] ) {
		$attributes = cleaner_gallery_link_attributes( $instance );

		if ( !empty( $attributes ) )
			$image = str_replace( '<a href=', "<a{$attributes} href=", $image );
	}

	/* If the image should link to an intermediate-sized image, change the link attributes. */
	elseif ( in_array( $attr['link'], get_intermediate_image_sizes() ) ) {

		$post = get_post( $id );
		$image_src = wp_get_attachment_image_src( $id, $attr['link'] );

		$attributes = cleaner_gallery_link_attributes( $instance );
		$attributes .= " href='{$image_src[0]}'";
		$attributes .= " title='" . esc_attr( $post->post_title ) . "'";

		$image = preg_replace( '/<a.*?>(.*?)<\/a>/', "<a{$attributes}>$1</a>", $image );
	}

	/* Return the formatted image. */
	return $image;
}

/**
 * Filters the default gallery arguments with user-selected arguments or the plugin defaults.
 *
 * @since 0.9.0
 * @param array $defaults
 * @return array $defaults
 */
function cleaner_gallery_default_args( $defaults ) {

	$defaults['order'] = ( ( cleaner_gallery_get_setting( 'order' ) ) ? cleaner_gallery_get_setting( 'order' ) : 'ASC' );

	$defaults['orderby'] = ( ( cleaner_gallery_get_setting( 'orderby' ) ) ? cleaner_gallery_get_setting( 'orderby' ) : 'menu_order ID' );

	$defaults['size'] = ( ( cleaner_gallery_get_setting( 'size' ) ) ? cleaner_gallery_get_setting( 'size' ) : 'thumbnail' );

	$defaults['link'] = ( ( cleaner_gallery_get_setting( 'image_link' ) ) ? cleaner_gallery_get_setting( 'image_link' ) : '' );

	return $defaults;
}

/**
 * Returns the link class and rel attributes based on what the user selected in the plugin
 * settings.  This is important for handling Lightbox-type image scripts.
 *
 * @since 0.7.0
 * @param int $id Post ID.
 * @return string $attributes
 */
function cleaner_gallery_link_attributes( $id = 0 ) {

	$class = '';
	$rel = '';
	$script = cleaner_gallery_get_setting( 'image_script' );

	switch ( $script ) {

		case 'lightbox' :
		case 'slimbox' :
		case 'jquery_lightbox_plugin' :
		case 'jquery_lightbox_balupton' :
			$class = 'lightbox';
			$rel = "lightbox[cleaner-gallery-{$id}]";
			break;

		case 'colorbox' :
			$class = "colorbox colorbox-{$id}";
			$rel = "colorbox-{$id}";
			break;

		case 'jquery_lightbox' :
			$class = 'lightbox';
			$rel = "cleaner-gallery-{$id}";
			break;

		case 'lightwindow' :
			$class = 'lightwindow';
			$rel = "lightwindow[cleaner-gallery-{$id}]";
			break;

		case 'floatbox' :
			$class = 'floatbox';
			$rel = "floatbox.cleaner-gallery-{$id}";
			break;

		case 'shutter_reloaded' :
			$class = "shutterset_cleaner-gallery-{$id}";
			$rel = "lightbox[cleaner-gallery-{$id}]";
			break;

		case 'fancybox' :
			$class = 'fancybox';
			$rel = "fancybox-{$id}";
			break;

		case 'greybox' :
			$class = 'greybox';
			$rel = "gb_imageset[cleaner-gallery-{$id}]";
			break;

		case 'lightview' :
			$class = 'lightview';
			$rel = "gallery[cleaner-gallery-{$id}]";
			break;

		case 'lytebox' :
			$class = 'lytebox';
			$rel = "lytebox[cleaner-gallery-{$id}]";
			break;

		case 'thickbox' :
			$class = 'thickbox';
			$rel = "clean-gallery-{$id}";
			break;

		case 'shadowbox' :
			$class = 'shadowbox';
			$rel = "shadowbox[cleaner-gallery-{$id}]";
			break;

		case 'pretty_photo' :
			$class = 'prettyPhoto';
			$rel = "prettyPhoto[{$id}]";
			break;

		case 'fancyzoom' :
		default :
			$class = '';
			$rel = '';
			break;
	}

	$class = apply_filters( 'cleaner_gallery_image_link_class', $class );
	$rel = apply_filters( 'cleaner_gallery_image_link_rel', $rel );

	if ( !empty( $class ) )
		$class = " class='{$class}'";

	if ( !empty( $rel ) )
		$rel = " rel='{$rel}'";

	return $class . $rel;
}

/**
 * Load the cleaner gallery stylesheet and the Thickbox stylesheet if needed.
 *
 * @since 0.8.0
 */
function cleaner_gallery_enqueue_style() {
	if ( cleaner_gallery_get_setting( 'thickbox_css' ) )
		wp_enqueue_style( 'thickbox' );

	if ( cleaner_gallery_get_setting( 'cleaner_gallery_css' ) )
		wp_enqueue_style( 'cleaner-gallery', CLEANER_GALLERY_URL . 'gallery.css', false, 0.9, 'all' );
}

/**
 * Load the Thickbox JavaScript if needed.
 *
 * @since 0.8.0
 */
function cleaner_gallery_enqueue_script() {
	if ( cleaner_gallery_get_setting( 'thickbox_js' ) )
		wp_enqueue_script( 'thickbox' );
}

/**
 * @since 0.7.0
 * @deprecated 0.9.0
 */
function cleaner_gallery_id( $id = 0 ) {
	return $id;
}

?>