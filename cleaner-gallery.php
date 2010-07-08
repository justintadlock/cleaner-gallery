<?php
/**
 * Plugin Name: Cleaner Gallery
 * Plugin URI: http://justintadlock.com/archives/2008/04/13/cleaner-wordpress-gallery-plugin
 * Description: This plugin replaces the default gallery feature with a valid XHTML solution and offers support for multiple Lightbox-type image scripts.
 * Version: 0.7
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
 *
 *
 * @internal In 0.6, functions were renamed with the prefix 'cleaner_gallery'
 *
 * @copyright 2008 - 2009
 * @version 0.7
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

/**
 * Yes, we're localizing the plugin.  This partly makes sure non-English
 * users can use it too.  To translate into your language use the
 * en_EN.po file as as guide.  Poedit is a good tool to for translating.
 * @link http://poedit.net
 *
 * @since 0.5
 */
load_plugin_textdomain( 'cleaner_gallery', false, '/cleaner-gallery'  );

/**
 * Make sure we get the correct directory.
 * @since 0.5
 */
if ( !defined( 'WP_CONTENT_URL' ) )
	define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( !defined( 'WP_CONTENT_DIR' ) )
	define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( !defined( 'WP_PLUGIN_URL' ) )
	define('WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( !defined( 'WP_PLUGIN_DIR' ) )
	define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );

/**
 * Define constant paths to the plugin folder.
 * @since 0.5
 */
define( CLEANER_GALLERY, WP_PLUGIN_DIR . '/cleaner-gallery' );
define( CLEANER_GALLERY_URL, WP_PLUGIN_URL . '/cleaner-gallery' );

/**
 * Add the settings page to the admin menu.
 * @since 0.5
 */
add_action( 'admin_menu', 'cleaner_gallery_add_pages' );

/**
 * Load the Cleaner Gallery settings if in the WP admin.
 * @since 0.5
 */
if ( is_admin() )
	require_once( CLEANER_GALLERY . '/settings-admin.php' );

/**
 * If not in the WP admin, load the settings from the database.
 * @since 0.5
 */
if ( !is_admin() )
	$cleaner_gallery = get_option( 'cleaner_gallery_settings' );

/**
 * We're going to filter the default gallery shortcode.
 * So, we're adding our own function here.
 * @internal Prior to 0.6, we removed the post_gallery, but now filter it.
 *
 * @since 0.6
 */
add_filter( 'post_gallery', 'cleaner_gallery_shortcode', 10, 2 );

/**
 * Filters the default [gallery] arguments.
 * @since 0.7
 */
add_filter( 'cleaner_gallery_defaults', 'cleaner_gallery_default_settings' );

/**
 * Add the Cleaner Gallery stylesheet to the header for use.
 * Cleaner Gallery uses its own stylesheet because we shouldn't load these
 * things directly into the page.
 *
 * @since 0.1
 */
if ( !is_admin() )
	wp_enqueue_style( 'cleaner-gallery', CLEANER_GALLERY_URL . '/cleaner-gallery.css', false, 0.7, 'all' );

/**
 * Add the Thickbox CSS if the user chooses to use it.
 * @since 0.1
 */
if ( $cleaner_gallery['thickbox_css'] && !is_admin() )
	wp_enqueue_style( 'thickbox' );

/**
 * Add the Thickbox JavaScript if the user chooses to use it.
 * @since 0.1
 */
if ( $cleaner_gallery['thickbox_js'] && !is_admin() )
	wp_enqueue_script( 'thickbox' );

/**
 * Overwrites the original WordPress gallery shortcode.
 * This is where all the main gallery stuff is pieced together.
 * What we're doing is completely rewriting how the gallery works.
 * Most of the functionality is from the default gallery shortcode, so
 * most of the work has already been done.  Just plugging in the extras.
 *
 * The main thing we have to do is clear out the style rules and make it
 * easier to style through an external stylesheet.  The second largest 
 * issue is integrating the Lightbox-type scripts.
 *
 * In 0.6.1, added $include, $exclude, $numberposts.
 *
 * @internal Prior to 0.6, function's name was jt_gallery_shortcode()
 * @since 0.6
 */
function cleaner_gallery_shortcode( $output, $attr ) {
	global $post, $cleaner_gallery;

	/* Orderby. */
	if ( isset( $attr['orderby'] ) ) :
		$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
		if ( !$attr['orderby'] )
			unset( $attr['orderby'] );
	endif;

	/* Default gallery settings. */
	$defaults = array(
		'order' => 'ASC',
		'orderby' => 'menu_order ID',
		'id' => $post->ID,
		'itemtag' => 'dl',
		'icontag' => 'dt',
		'captiontag' => 'dd',
		'columns' => 3,
		'size' => 'thumbnail',
		'include' => '',
		'exclude' => '',
		'numberposts' => -1,
	);

	/* Apply filters to the default arguments. */
	$defaults = apply_filters( 'cleaner_gallery_defaults', $defaults );

	/* Merge the defaults with user input. Make sure $id is an integer. */
	extract( shortcode_atts( $defaults, $attr ) );
	$id = intval( $id );

	/* Arguments for get_children(). */
	$children = array(
		'post_parent' => $id,
		'post_status' => 'inherit',
		'post_type' => 'attachment',
		'post_mime_type' => 'image',
		'order' => $order,
		'orderby' => $orderby,
		'exclude' => $exclude,
		'include' => $include,
		'numberposts' => $numberposts,
	);

	/* Get image attachments. If none, return. */
	$attachments = get_children( $children );

	if ( empty( $attachments ) )
		return '';

	/* If is feed, leave the default WP settings. We're only worried about on-site presentation. */
	if ( is_feed() ) {
		$output = "\n";
		foreach ( $attachments as $id => $attachment )
			$output .= wp_get_attachment_link( $id, $size, true ) . "\n";
		return $output;
	}

	/* Set up some important variables. */
	$itemtag = tag_escape( $itemtag );
	$captiontag = tag_escape( $captiontag );
	$columns = intval( $columns );
	$itemwidth = $columns > 0 ? floor( 100 / $columns ) : 100;
	$i = 0;

	/*
	* Remove the style output in the middle of the freakin' page.
	* This needs to be added to the header.
	* The width applied through CSS but limits it a bit.
	*/

	/* Make sure posts with multiple galleries have different IDs. */
	$gallery_id = cleaner_gallery_id( $id );

	/* Class and rel attributes. */
	$attributes = cleaner_gallery_link_attributes( $gallery_id );

	/* Open the gallery <div>. */
	$output = "\t\t\t" . '<div id="gallery-' . $gallery_id . '" class="gallery gallery-' . $id . '">';

	/* Loop through each attachment. */
	foreach ( $attachments as $id => $attachment ) :

		/* Get the caption and title. */
		$caption = wp_specialchars( $attachment->post_excerpt, 1 );
		$title = wp_specialchars( $attachment->post_title, 1 );
		if ( !$caption && $cleaner_gallery['caption_title'] )
			$caption = $title;
		if ( $cleaner_gallery['caption_remove'] )
			$caption = false;

		/* Open each gallery row. */
		if ( $columns > 0 && $i % $columns == 0 )
			$output .= "\n\t\t\t\t<div class='gallery-row clear'>";

		/* Open each gallery item. */
		$output .= "\n\t\t\t\t\t<{$itemtag} class='gallery-item col-$columns'>";

		/* Open the element to wrap the image. */
		$output .= "\n\t\t\t\t\t\t<{$icontag} class='gallery-icon'>";

		/* If no setting for 'image_link', use WP default. */
		if ( !$cleaner_gallery['image_link'] ) :

			/* If user links to file. */
			if ( isset( $attr['link'] ) && 'file' == $attr['link'] ) :

				$output .= '<a href="' .  wp_get_attachment_url( $id ) . '" title="' . $title . '"' . $attributes . '>';

				$img = wp_get_attachment_image_src( $id, $size );
				$output .= '<img src="' . $img[0] . '" alt="' . $title . '" title="' . $title . '" />';

				$output .= '</a>';

			/* Link to attachment page. */
			else :
				$output .= wp_get_attachment_link( $id, $size, true, false );

			endif;

		/* If 'image_link' is set to attachment page. */
		elseif ( $cleaner_gallery['image_link'] == __('Attachment Page', 'cleaner_gallery') ) :
			$output .= wp_get_attachment_link( $id, $size, true, false );

		/* If user wants to link to neither the image file nor attachment. */
		elseif ( $cleaner_gallery['image_link'] == __('No Link', 'cleaner_gallery') ) :
			$img = wp_get_attachment_image_src( $id, $size );
			$output .= '<img src="' . $img[0] . '" alt="' . $title . '" title="' . $title . '" />';

		/* If 'image_link' is set to full, large, medium, or thumbnail. */
		else :
			if ( $cleaner_gallery['image_link'] == __('Full', 'cleaner_gallery') ) :
				$link = wp_get_attachment_image_src( $id, 'full' );

			elseif ( $cleaner_gallery['image_link'] == __('Large', 'cleaner_gallery') ) :
				$link = wp_get_attachment_image_src( $id, 'large' );

			elseif ( $cleaner_gallery['image_link'] == __('Medium', 'cleaner_gallery') ) :
				$link = wp_get_attachment_image_src( $id, 'medium' );

			elseif ( $cleaner_gallery['image_link'] == __('Thumbnail', 'cleaner_gallery') ) :
				$link = wp_get_attachment_image_src( $id, 'thumbnail' );

			endif;

			/* Output the link. */
			$output .= '<a href="' .  $link[0] . '" title="' . $title . '"' . $attributes . '>';

			$img = wp_get_attachment_image_src( $id, $size );
			$output .= '<img src="' . $img[0] . '" alt="' . $title . '" title="' . $title . '" />';

			$output .= '</a>';

		endif;

		/* Close the image wrapper. */
		$output .= "</{$icontag}>";

		/* If image caption is set. */
		if ( $captiontag && $caption ) :
			$output .= "\n\t\t\t\t\t\t<$captiontag class='gallery-caption'>";
			if ( $cleaner_gallery['caption_link'] ) :
				$output .= '<a href="' . get_attachment_link( $id ) . '" title="' . $title . '">' . $caption . '</a>';
			else :
				$output .= $caption;
			endif;
			$output .= "</$captiontag>";
		endif;

		/* Close individual gallery item. */
		$output .= "\n\t\t\t\t\t</{$itemtag}>";

		/* Close gallery row. */
		if ( $columns > 0 && ++$i % $columns == 0 )
			$output .= "\n\t\t\t\t</div>";

	endforeach;

	/* Close gallery and return it. */
	if ( $columns > 0 && $i % $columns !== 0 )
		$output .= "\n\t\t\t</div>";

	$output .= "\n\t\t\t</div>\n";

	/* Return out very nice, valid XHTML gallery. */
	return $output;
}

/**
 * Makes sure we can have multiple galleries in one post
 * without the same IDs to keep the XHTML valid.
 *
 * @since 0.7
 * @param int $id
 * @return int $id
 */
function cleaner_gallery_id( $id = 0 ) {
	global $cleaner_gallery_id, $cleaner_gallery_num;

	if ( $cleaner_gallery_id == $id ) :
		++$cleaner_gallery_num;
		$id = $id . '-' . $cleaner_gallery_num;
	else :
		$cleaner_gallery_num = 0;
		$cleaner_gallery_id = $id;
	endif;

	return $id;
}

/**
 * Gets the Cleaner Gallery settings defaults and overwrites
 * the default arguments for the gallery.
 *
 * @since 0.7
 * @param array $defaults
 * @return array $defaults
 */
function cleaner_gallery_default_settings( $defaults ) {
	global $cleaner_gallery;

	/* Check if user has set any defaults. */
	if ( $cleaner_gallery['order'] )
		$defaults['order'] = $cleaner_gallery['order'];
	else
		$defaults['order'] = 'ASC';

	if ( $cleaner_gallery['orderby'] )
		$defaults['orderby'] = $cleaner_gallery['orderby'];
	else
		$defaults['orderby'] = 'menu_order ID';

	if ( $cleaner_gallery['size'] )
		$defaults['size'] = $cleaner_gallery['size'];
	else
		$defaults['size'] = 'thumbnail';

	return $defaults;
}

/**
 * Returns the link class and rel attributes.
 * This is important for handling Lightbox-type image scripts.
 *
 * @since 0.7
 * @param int $id Post ID.
 * @return string $attributes
 */
function cleaner_gallery_link_attributes( $id = 0 ) {
	global $cleaner_gallery;

	$attributes = false;

	/* Lightbox 2, Slimbox, jQuery Lightbox Plugin, jQuery Lightbox Plugin (Balupton). */
	if ( $cleaner_gallery['image_script'] == 'Lightbox 2' || $cleaner_gallery['image_script'] == 'Slimbox' || $cleaner_gallery['image_script'] == 'jQuery Lightbox Plugin' || $cleaner_gallery['image_script'] == 'jQuery Lightbox Plugin (Balupton)' ) :
		$class = "lightbox";
		$rel = "lightbox[cleaner-gallery-$id]";

	/* Slimbox 2. */
	elseif ( $cleaner_gallery['image_script'] == 'Slimbox 2' ) :
		$class = "lightbox";
		$rel = "lightbox-$id";

	/* jQuery Lightbox. */
	elseif ( $cleaner_gallery['image_script'] == 'jQuery Lightbox' ) :
		$class = "lightbox";
		$rel = "cleaner-gallery-$id";

	/* Lightwindow. */
	elseif ( $cleaner_gallery['image_script'] == 'LightWindow' ) :
		$class = "lightwindow";
		$rel = "lightwindow[cleaner-gallery-$id]";

	/* Floatbox. */
	elseif ( $cleaner_gallery['image_script'] == 'Floatbox' ) :
		$class = "floatbox";
		$rel = "floatbox.cleaner-gallery-$id";

	/* Shutter Reloaded. */
	elseif ( $cleaner_gallery['image_script'] == 'Shutter Reloaded' ) :
		$rel = "lightbox[cleaner-gallery-$post->ID]";
		$class = "shutterset_cleaner-gallery-$id";

	/* Fancybox. */
	elseif ( $cleaner_gallery['image_script'] == 'FancyBox' ) :
		//$class = "fancybox";
		$rel = "fancybox-$id";

	/* FancyZoom. */
	elseif ( $cleaner_gallery['image_script'] == 'FancyBox' ) :
		$class = false;
		$rel = false;

	/* GreyBox. */
	elseif ( $cleaner_gallery['image_script'] == 'GreyBox' ) :
		$rel = "gb_imageset[cleaner-gallery-$id]";
		$class = "greybox";

	/* Lightview. */
	elseif ( $cleaner_gallery['image_script'] == 'Lightview' ) :
		$rel = "gallery[cleaner-gallery-$id]";
		$class = "lightview";

	/* Lytebox. */
	elseif ( $cleaner_gallery['image_script'] == 'Lytebox' ) :
		$rel = "lytebox[cleaner-gallery-$id]";
		$class = "lytebox";

	/* Thickbox. */
	elseif ( $cleaner_gallery['image_script'] == 'Thickbox' ) :
		$rel = "clean-gallery-$id";
		$class = "thickbox";

	/* Shadowbox. */
	elseif ( $cleaner_gallery['image_script'] == 'Shadowbox' ) :
		$rel = "shadowbox[cleaner-gallery-$id]";
		$class = "shadowbox";

	/* Custom class and rel. */
	elseif ( $cleaner_gallery['image_class'] || $cleaner_gallery['image_rel'] ) :
		$class = str_ireplace( 'id', $id, $cleaner_gallery['image_class'] );
		$rel = str_ireplace( 'id', $id, $cleaner_gallery['image_rel'] );

	else :
		$class = false;
		$rel = false;

	endif;

	/* If a class and rel have been set, wrap them appropriately. */
	if ( $class )
		$attributes .= ' class="' . $class . '"';
	if ( $rel )
		$attributes .= ' rel="' . $rel . '"';

	return $attributes;
}

/**
 * Function to add the settings page
 *
 * @since 0.5
 */
function cleaner_gallery_add_pages() {
	add_theme_page( __('Cleaner Gallery Settings', 'cleaner_gallery' ), __('Cleaner Gallery', 'cleaner_gallery'), 10, 'cleaner-gallery-settings.php', cleaner_gallery_theme_page );
}

/**
 * Function for outputting gallery CSS to the site
 * Themes must use the wp_head() hook for this to work
 *
 * @internal In 0.6, function changed to cleaner_gallery_css() from jt_gallery_css()
 *
 * @since 0.6
 * @deprecated 0.7
 */
function cleaner_gallery_css () {
	return false;
}

/**
 * Function for outputting the Thickbox CSS
 *
 * @since unknown
 * @deprecated 0.7
 */
function thickbox_css() {
	return false;
}

/**
 * Old Cleaner Gallery shortcode function
 *
 * @deprecated 0.6
 * @since 0.1
 */
function jt_gallery_shortcode( $deprecated = '', $deprecated_2 = '' ) {
	return false;
}

/**
 * Old Cleaner Gallery CSS function
 *
 * @deprecated 0.6
 * @since 0.1
 */
function jt_gallery_css() {
	return false;
}

?>