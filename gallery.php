<?php
/**
 * The functions file for completely taking over the WordPress [gallery] shortcode and other
 * functions used for the plugin.
 *
 * @package CleanerGallery
 */

/* Load any scripts needed. */
add_action( 'template_redirect', 'cleaner_gallery_enqueue_script' );

/* Load any stylesheets needed. */
add_action( 'template_redirect', 'cleaner_gallery_enqueue_style' );

/* Filter the post_gallery [gallery] shortcode. */
add_filter( 'post_gallery', 'cleaner_gallery_shortcode', 10, 2 );

/* Filter the cleaner gallery default shortcode attributes. */
add_filter( 'cleaner_gallery_defaults', 'cleaner_gallery_default_settings' );

/**
 * Overwrites the original WordPress gallery shortcode.  This is where all the main gallery 
 * stuff is pieced together.  What we're doing is completely rewriting how the gallery works.
 * Most of the functionality is from the default gallery shortcode, so most of the work has 
 * already been done.  Just plugging in the extras.
 *
 * @since 0.6
 * @param string $output The formatted gallery.
 * @param array $attr Arguments for displaying the gallery.
 * @return string $output
 */
function cleaner_gallery_shortcode( $output, $attr ) {
	global $post;

	/* Orderby. */
	if ( isset( $attr['orderby'] ) ) {
		$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
		if ( !$attr['orderby'] )
			unset( $attr['orderby'] );
	}

	/* Default gallery settings. */
	$defaults = array(
		'order' => 'ASC',
		'orderby' => 'menu_order ID',
		'id' => $post->ID,
		'link' => '',
		'itemtag' => 'dl',
		'icontag' => 'dt',
		'captiontag' => 'dd',
		'columns' => 3,
		'size' => 'thumbnail',
		'include' => '',
		'exclude' => '',
		'numberposts' => -1,
		'offset' => ''
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
		'offset' => $offset,
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
	$output = "\t\t\t<div id='gallery-{$gallery_id}' class='gallery gallery-{$id}'>";

	/* Loop through each attachment. */
	foreach ( $attachments as $id => $attachment ) {

		/* Get the caption and title. */
		$caption = esc_html( $attachment->post_excerpt );
		$title = esc_attr( $attachment->post_title );

		if ( empty( $caption ) && cleaner_gallery_get_setting( 'caption_title' ) )
			$caption = $title;

		if ( cleaner_gallery_get_setting( 'caption_remove' ) )
			$caption = false;

		/* Open each gallery row. */
		if ( $columns > 0 && $i % $columns == 0 )
			$output .= "\n\t\t\t\t<div class='gallery-row clear'>";

		/* Open each gallery item. */
		$output .= "\n\t\t\t\t\t<{$itemtag} class='gallery-item col-{$columns}'>";

		/* Open the element to wrap the image. */
		$output .= "\n\t\t\t\t\t\t<{$icontag} class='gallery-icon'>";

		/* If user links to file. */
		if ( 'file' == $link ) {

			$output .= '<a href="' .  wp_get_attachment_url( $id ) . '" title="' . $title . '"' . $attributes . '>';

			$img = wp_get_attachment_image_src( $id, $size );
			$output .= '<img src="' . $img[0] . '" alt="' . $title . '" title="' . $title . '" />';

			$output .= '</a>';
		}

		/* Link to attachment page. */
		elseif ( empty( $link ) || 'attachment' == $link ) {
			$output .= wp_get_attachment_link( $id, $size, true, false );
		}

		/* If user wants to link to neither the image file nor attachment. */
		elseif ( 'none' == $link ) {
			$img = wp_get_attachment_image_src( $id, $size );
			$output .= '<img src="' . $img[0] . '" alt="' . $title . '" title="' . $title . '" />';
		}

		/* If 'image_link' is set to full, large, medium, or thumbnail. */
		elseif ( 'full' == $link || in_array( $link, get_intermediate_image_sizes() ) ) {
			$img_src = wp_get_attachment_image_src( $id, $link );

			/* Output the link. */
			$output .= '<a href="' .  $img_src[0] . '" title="' . $title . '"' . $attributes . '>';

			$img = wp_get_attachment_image_src( $id, $size );
			$output .= '<img src="' . $img[0] . '" alt="' . $title . '" title="' . $title . '" />';

			$output .= '</a>';
		}

		/* Close the image wrapper. */
		$output .= "</{$icontag}>";

		/* If image caption is set. */
		if ( $captiontag && $caption ) {
			$output .= "\n\t\t\t\t\t\t<{$captiontag} class='gallery-caption'>";

			if ( cleaner_gallery_get_setting( 'caption_link' ) )
				$output .= '<a href="' . get_attachment_link( $id ) . '" title="' . $title . '">' . $caption . '</a>';

			else
				$output .= $caption;

			$output .= "</{$captiontag}>";
		}

		/* Close individual gallery item. */
		$output .= "\n\t\t\t\t\t</{$itemtag}>";

		/* Close gallery row. */
		if ( $columns > 0 && ++$i % $columns == 0 )
			$output .= "\n\t\t\t\t</div>";

	}

	/* Close gallery and return it. */
	if ( $columns > 0 && $i % $columns !== 0 )
		$output .= "\n\t\t\t</div>";

	$output .= "\n\t\t\t</div>\n";

	/* Return out very nice, valid XHTML gallery. */
	return $output;
}

/**
 * Makes sure we can have multiple galleries in one post without the same IDs to keep 
 * the XHTML valid.
 *
 * @since 0.7
 * @param int $id
 * @return int $id
 */
function cleaner_gallery_id( $id = 0 ) {
	global $cleaner_gallery;

	if ( isset( $cleaner_gallery->gallery_id ) && $cleaner_gallery->gallery_id == $id ) {
		++$cleaner_gallery->gallery_num;
		$id = "{$id}-{$cleaner_gallery->gallery_num}";
	}
	else {
		$cleaner_gallery->gallery_num = 0;
		$cleaner_gallery->gallery_id = $id;
	}

	return $id;
}

/**
 * Filters the default gallery arguments with user-selected arguments or the plugin defaults.
 *
 * @since 0.7
 * @param array $defaults
 * @return array $defaults
 */
function cleaner_gallery_default_settings( $defaults ) {

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
 * @since 0.7
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

		case 'fancyzoom' :
		default :
			$class = '';
			$rel = '';
			break;
	}

	$class = apply_filters( 'cleaner_gallery_image_link_class', $class );
	$rel = apply_filters( 'cleaner_gallery_image_link_rel', $rel );

	if ( !empty( $class ) )
		$class = ' class="' . $class . '"';

	if ( !empty( $rel ) )
		$rel = ' rel="' . $rel . '"';

	return $class . $rel;
}

/**
 * Load the cleaner gallery stylesheet and the Thickbox stylesheet if needed.
 *
 * @since 0.8
 */
function cleaner_gallery_enqueue_style() {
	if ( cleaner_gallery_get_setting( 'thickbox_css' ) )
		wp_enqueue_style( 'thickbox' );

	wp_enqueue_style( 'cleaner-gallery', CLEANER_GALLERY_URL . 'cleaner-gallery.css', false, 0.8, 'all' );
}

/**
 * Load the Thickbox JavaScript if needed.
 *
 * @since 0.8
 */
function cleaner_gallery_enqueue_script() {
	if ( cleaner_gallery_get_setting( 'thickbox_js' ) )
		wp_enqueue_script( 'thickbox' );
}

?>