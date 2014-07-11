<?php
/**
 * Sets up custom filters for the plugin's output, particularly filters on the [gallery] shortcode output that 
 * are custom to this plugin.
 *
 * @package   CleanerGallery
 * @author    Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2008 - 2014, Justin Tadlock
 * @link      http://themehybrid.com/plugins/cleaner-gallery
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *

/* Filter the gallery images with user options. */
add_filter( 'cleaner_gallery_image', 'cleaner_gallery_plugin_gallery_image', 10, 4 );

/* Filter the gallery captions with user options. */
add_filter( 'cleaner_gallery_caption', 'cleaner_gallery_plugin_image_caption', 10, 3 );

/* Filter the cleaner gallery default shortcode attributes. */
add_filter( 'cleaner_gallery_defaults', 'cleaner_gallery_default_args' );

/**
 * @since  0.9.0
 * @access public
 * @return array
 */
function cleaner_gallery_default_settings() {

	$settings = array(
		'size'           => 'thumbnail',
		'image_link'     => '',
		'orderby'        => 'menu_order ID',
		'order'          => 'ASC',
		'caption_remove' => false,
		'caption_title'  => false,
		'thickbox_js'    => false,
		'thickbox_css'   => false,
		'image_script'   => ''
	);

	return $settings;
}

/**
 * Function for quickly grabbing settings for the plugin without having to call get_option() 
 * every time we need a setting.
 *
 * @since  0.8.0
 * @access public
 * @param  string  $option
 * @return mixed
 */
function cleaner_gallery_get_setting( $option = '' ) {

	$settings = get_option( 'cleaner_gallery_settings', cleaner_gallery_default_settings() );

	return $settings[ $option ];
}

/**
 * @since  1.0.0
 * @access public
 * @return array
 */
function cleaner_gallery_get_supported_scripts() {

	$scripts = array(
		'colorbox'                 => __( 'Colorbox',                   'cleaner-gallery' ), 
		'fancybox'                 => __( 'FancyBox',                   'cleaner-gallery' ), 
		'fancyzoom'                => __( 'FancyZoom',                  'cleaner-gallery' ), 
		'floatbox'                 => __( 'Floatbox',                   'cleaner-gallery' ), 
		'greybox'                  => __( 'GreyBox',                    'cleaner-gallery' ), 
		'jquery_lightbox'          => __( 'jQuery Lightbox',            'cleaner-gallery' ),
		'jquery_lightbox_plugin'   => __( 'jQuery Lightbox Plugin',     'cleaner-gallery' ), 
		'jquery_lightbox_balupton' => __( 'jQuery Lightbox (Balupton)', 'cleaner-gallery' ), 
		'lightbox'                 => __( 'Lightbox',                   'cleaner-gallery' ), 
		'lightview'                => __( 'Lightview',                  'cleaner-gallery' ), 
		'lightwindow'              => __( 'LightWindow',                'cleaner-gallery' ), 
		'lytebox'                  => __( 'Lytebox',                    'cleaner-gallery' ), 
		'pretty_photo'             => __( 'prettyPhoto',                'cleaner-gallery' ), 
		'shadowbox'                => __( 'Shadowbox',                  'cleaner-gallery' ), 
		'shutter_reloaded'         => __( 'Shutter Reloaded',           'cleaner-gallery' ), 
		'slimbox'                  => __( 'Slimbox',                    'cleaner-gallery' ), 
		'thickbox'                 => __( 'Thickbox',                   'cleaner-gallery' )
	);

	return apply_filters( 'cleaner_gallery_supported_scripts', $scripts );
}

/**
 * Filters the default gallery arguments with user-selected arguments or the plugin defaults.
 *
 * @since  0.9.0
 * @access public
 * @param  array $defaults
 * @return array
 */
function cleaner_gallery_default_args( $defaults ) {

	$defaults['order']   = cleaner_gallery_get_setting( 'order' )      ? cleaner_gallery_get_setting( 'order' )      : 'ASC';
	$defaults['orderby'] = cleaner_gallery_get_setting( 'orderby' )    ? cleaner_gallery_get_setting( 'orderby' )    : 'menu_order ID';
	$defaults['size']    = cleaner_gallery_get_setting( 'size' )       ? cleaner_gallery_get_setting( 'size' )       : 'thumbnail';
	$defaults['link']    = cleaner_gallery_get_setting( 'image_link' ) ? cleaner_gallery_get_setting( 'image_link' ) : '';

	return $defaults;
}

/**
 * Filters the gallery image if it has a link and adds the appropriate attributes for the lightbox
 * scripts.
 *
 * @since  0.9.0
 * @access public
 * @param  string  $image
 * @param  int     $id
 * @param  array   $attr
 * @param  int     $instance
 * @return string
 */
function cleaner_gallery_plugin_gallery_image( $image, $id, $attr, $instance ) {

	/* If the image should link to the 'file' (full-size image), add in extra link attributes. */
	if ( 'file' == $attr['link'] ) {

		$attributes = cleaner_gallery_link_attributes( $instance );

		if ( !empty( $attributes ) )
			$image = str_replace( '<a href=', "<a{$attributes} href=", $image );
	}

	/* If the image should link to an intermediate-sized image, change the link attributes. */
	else if ( in_array( $attr['link'], get_intermediate_image_sizes() ) ) {

		$post      = get_post( $id );
		$image_src = wp_get_attachment_image_src( $id, $attr['link'] );

		$attributes  = cleaner_gallery_link_attributes( $instance );
		$attributes .= " href='{$image_src[0]}'";
		$attributes .= " title='" . esc_attr( $post->post_title ) . "'";

		$image = preg_replace( '/<a.*?>(.*?)<\/a>/', "<a{$attributes}>$1</a>", $image );
	}

	/* Return the formatted image. */
	return $image;
}

/**
 * Modifies the gallery captions according to user-selected settings.
 *
 * @since  0.9.0
 * @access public
 * @param  string  $caption
 * @param  int     $id
 * @param  array   $attr
 * @return string
 */
function cleaner_gallery_plugin_image_caption( $caption, $id, $attr ) {

	/* If the caption should be removed, return empty string. */
	if ( cleaner_gallery_get_setting( 'caption_remove' ) )
		$caption = '';

	/* If the caption is empty and the user is using the title as a caption, get the image title. */
	else if ( empty( $caption ) && cleaner_gallery_get_setting( 'caption_title' ) )
		$caption = wptexturize( get_the_title( $id ) );

	/* Return the caption. */
	return $caption;
}

/**
 * Returns the link class and rel attributes based on what the user selected in the plugin
 * settings.  This is important for handling Lightbox-type image scripts.
 *
 * @since  0.7.0
 * @access public
 * @param  int    $id
 * @return string
 */
function cleaner_gallery_link_attributes( $id = 0 ) {

	$script = cleaner_gallery_get_setting( 'image_script' );

	if ( !array_key_exists( $script, cleaner_gallery_get_supported_scripts() ) )
		return '';

	switch ( $script ) {

		case 'lightbox' :
		case 'slimbox' :
		case 'jquery_lightbox_plugin' :
		case 'jquery_lightbox_balupton' :

			$class = 'lightbox';
			$rel   = "lightbox[cleaner-gallery-{$id}]";
			break;

		case 'colorbox' :

			$class = "colorbox colorbox-{$id}";
			$rel   = "colorbox-{$id}";
			break;

		case 'jquery_lightbox' :

			$class = 'lightbox';
			$rel   = "cleaner-gallery-{$id}";
			break;

		case 'lightwindow' :

			$class = 'lightwindow';
			$rel   = "lightwindow[cleaner-gallery-{$id}]";
			break;

		case 'floatbox' :

			$class = 'floatbox';
			$rel   = "floatbox.cleaner-gallery-{$id}";
			break;

		case 'shutter_reloaded' :

			$class = "shutterset_cleaner-gallery-{$id}";
			$rel   = "lightbox[cleaner-gallery-{$id}]";
			break;

		case 'fancybox' :

			$class = 'fancybox';
			$rel   = "fancybox-{$id}";
			break;

		case 'greybox' :

			$class = 'greybox';
			$rel   = "gb_imageset[cleaner-gallery-{$id}]";
			break;

		case 'lightview' :

			$class = 'lightview';
			$rel   = "gallery[cleaner-gallery-{$id}]";
			break;

		case 'lytebox' :

			$class = 'lytebox';
			$rel   = "lytebox[cleaner-gallery-{$id}]";
			break;

		case 'thickbox' :

			$class = 'thickbox';
			$rel   = "clean-gallery-{$id}";
			break;

		case 'shadowbox' :

			$class = 'shadowbox';
			$rel   = "shadowbox[cleaner-gallery-{$id}]";
			break;

		case 'pretty_photo' :

			$class = 'prettyPhoto';
			$rel   = "prettyPhoto[{$id}]";
			break;

		case 'fancyzoom' :
		default :

			$class = '';
			$rel   = '';
			break;
	}

	$class = apply_filters( 'cleaner_gallery_image_link_class', $class );
	$rel   = apply_filters( 'cleaner_gallery_image_link_rel',   $rel   );

	$class = !empty( $class ) ? " class='{$class}'" : '';
	$rel   = !empty( $rel )   ? " rel='{$rel}'"     : '';

	return $class . $rel;
}

/* === DEPRECATED === */

/**
 * @since      0.8.0
 * @deprecated 1.0.0
 */
function cleaner_gallery_enqueue_style() {
	_deprecated_function( __FUNCTION__, '1.0.0', '' );
}

/**
 * @since      0.8.0
 * @deprecated 1.0.0
 */
function cleaner_gallery_enqueue_script() {
	_deprecated_function( __FUNCTION__, '1.0.0', '' );
}

/**
 * @since      0.8.0
 * @deprecated 1.0.0
 */
function cleaner_gallery_setup() {
	_deprecated_function( __FUNCTION__, '1.0.0', '' );
}

/**
 * @since      0.7.0
 * @deprecated 0.9.0
 */
function cleaner_gallery_id( $id = 0 ) {
	_deprecated_function( __FUNCTION__, '0.9.0', '' );
}
