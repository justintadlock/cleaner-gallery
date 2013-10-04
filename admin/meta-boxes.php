<?php
/**
 * This file holds all the meta boxes and the function to create the meta boxes for the Cleaner Gallery settings
 * page in the admin.
 *
 * @package HybridHook
 */

/* Add the meta boxes for the settings page on the 'add_meta_boxes' hook. */
add_action( 'add_meta_boxes', 'cleaner_gallery_create_meta_boxes' );

/**
 * Adds all the meta boxes to the Cleaner Gallery settings page in the WP admin.
 *
 * @since 0.3.0
 */
function cleaner_gallery_create_meta_boxes() {
	global $cleaner_gallery;

	/* Add the 'About' meta box. */
	add_meta_box( 'cleaner-gallery-about', __( 'About', 'cleaner-gallery' ), 'cleaner_gallery_meta_box_display_about', $cleaner_gallery->settings_page, 'side', 'default' );

	/* Add the 'Donate' meta box. */
	add_meta_box( 'cleaner-gallery-donate', __( 'Like this plugin?', 'cleaner-gallery' ), 'cleaner_gallery_meta_box_display_donate', $cleaner_gallery->settings_page, 'side', 'high' );

	/* Add the 'Support' meta box. */
	add_meta_box( 'cleaner-gallery-support', __( 'Support', 'cleaner-gallery' ), 'cleaner_gallery_meta_box_display_support', $cleaner_gallery->settings_page, 'side', 'low' );

	add_meta_box( 'cleaner-gallery-default', __( 'Default Gallery Settings', 'cleaner-gallery' ), 'cleaner_gallery_meta_box_display_default', $cleaner_gallery->settings_page, 'normal', 'high' );

	add_meta_box( 'cleaner-gallery-caption', __( 'Caption Settings', 'cleaner-gallery' ), 'cleaner_gallery_meta_box_display_caption', $cleaner_gallery->settings_page, 'normal', 'default' );

	add_meta_box( 'cleaner-gallery-script', __( 'Scripts and Styles', 'cleaner-gallery' ), 'cleaner_gallery_meta_box_display_script', $cleaner_gallery->settings_page, 'normal', 'low' );

}

/**
 * Displays the about plugin meta box.
 *
 * @since 0.3.0
 */
function cleaner_gallery_meta_box_display_about( $object, $box ) {

	$plugin_data = get_plugin_data( CLEANER_GALLERY_DIR . 'cleaner-gallery.php' ); ?>

	<p>
		<strong><?php _e( 'Version:', 'cleaner-gallery' ); ?></strong> <?php echo $plugin_data['Version']; ?>
	</p>
	<p>
		<strong><?php _e( 'Description:', 'cleaner-gallery' ); ?></strong>
	</p>
	<p>
		<?php echo $plugin_data['Description']; ?>
	</p>
<?php }

/**
 * Displays the donation meta box.
 *
 * @since 0.3.0
 */
function cleaner_gallery_meta_box_display_donate( $object, $box ) { ?>

	<p><?php _e( "Here's how you can give back:", 'cleaner-gallery' ); ?></p>

	<ul>
		<li><a href="http://wordpress.org/extend/plugins/cleaner-gallery" title="<?php _e( 'Cleaner Gallery on the WordPress plugin repository', 'cleaner-gallery' ); ?>"><?php _e( 'Give the plugin a good rating.', 'cleaner-gallery' ); ?></a></li>
		<li><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=3687060" title="<?php _e( 'Donate via PayPal', 'cleaner-gallery' ); ?>"><?php _e( 'Donate a few dollars.', 'cleaner-gallery' ); ?></a></li>
		<li><a href="http://amzn.com/w/31ZQROTXPR9IS" title="<?php _e( "Justin Tadlock's Amazon Wish List", 'cleaner-gallery' ); ?>"><?php _e( 'Get me something from my wish list.', 'cleaner-gallery' ); ?></a></li>
	</ul>
<?php
}

/**
 * Displays the support meta box.
 *
 * @since 0.3.0
 */
function cleaner_gallery_meta_box_display_support( $object, $box ) { ?>
	<p>
		<?php printf( __( 'Support for this plugin is provided via the support forums at %1$s. If you need any help using it, please ask your support questiosn there.', 'cleaner-gallery' ), '<a href="http://themehybrid.com/support" title="' . __( 'Theme Hybrid Support Forums', 'cleaner-gallery' ) . '">' . __( 'Theme Hybrid', 'cleaner-gallery' ) . '</a>' ); ?>
	</p>
<?php }

function cleaner_gallery_meta_box_display_default( $object, $box ) {
	/* Set up some default empty variables. */
	$size_field = '';
	$image_link_field = '';
	$orderby_field = '';
	$order_field = '';

	/* Get the available image sizes. */
	foreach ( get_intermediate_image_sizes() as $size )
		$image_sizes[$size] = $size;

	/* Set up an array items that gallery items can link to. */
	$image_link = array_merge( array( 'none' => '', '' => __( 'Attachment Page' ), 'file' => 'full' ), $image_sizes );

	/* Set up an array of orderby options. */
	$orderby_options = array( 'comment_count' => __( 'Comment Count', 'cleaner-gallery' ), 'date' => __( 'Date', 'cleaner-gallery' ), 'ID' => __( 'ID', 'cleaner-gallery' ), 'menu_order' => __( 'Menu Order', 'cleaner-gallery' ), 'none' => __( 'None', 'cleaner-gallery' ), 'rand' => __( 'Random', 'cleaner-gallery' ), 'title' => __( 'Title', 'cleaner-gallery' ) );

	/* Set up an array of ordering options. */
	$order_options = array( 'ASC' => __( 'Ascending', 'cleaner-gallery' ), 'DESC' => __( 'Descending', 'cleaner_gallery' ) );

						/* Set up the image size select element. */
						foreach ( get_intermediate_image_sizes() as $size )
							$size_field .= '<option value="' . esc_attr( $size ) . '" ' . selected( $size, cleaner_gallery_get_setting( 'size' ), false ) . '>' . esc_html( $size ) . '</option>';
						$size_field = '<select name="cleaner_gallery_settings[size]" id="size">' . $size_field . '</select>';

						/* Set up the image link select element. */
						foreach ( $image_link as $option => $option_name )
							$image_link_field .= '<option value="' . esc_attr( $option ) . '" ' . selected( $option, cleaner_gallery_get_setting( 'image_link' ), false ) . '>' . esc_html( $option_name ) . '</option>';
						$image_link_field = '<select id="image_link" name="cleaner_gallery_settings[image_link]">' . $image_link_field . '</select>';

						/* Set up the orderby select element. */
						foreach ( $orderby_options as $option => $option_name )
							$orderby_field .= '<option value="' . esc_attr( $option ) . '" ' . selected( $option, cleaner_gallery_get_setting( 'orderby' ), false ) . '>' . esc_html( $option_name ) . '</option>';
						$orderby_field = '<select name="cleaner_gallery_settings[orderby]" id="orderby">' . $orderby_field . '</select>';

						/* Set up the order select element. */
						foreach ( $order_options as $option => $option_name )
							$order_field .= '<option value="' . esc_attr( $option ) . '" ' . selected( $option, cleaner_gallery_get_setting( 'order' ), false ) . '>' . esc_html( $option_name ) . '</option>';
						$order_field = '<select name="cleaner_gallery_settings[order]" id="order">' . $order_field . '</select>';
					?>

	<p>
		<?php printf( __( 'Display %s size images by default.', 'cleaner-gallery' ), $size_field ); ?>
	</p>
	<p>
		<?php printf( __( 'Images should link to %s by default.', 'cleaner-gallery' ), $image_link_field ); ?>
	</p>
	<p>
		<?php printf( __( 'Galleries should be ordered by %s by default.', 'cleaner-gallery' ), $orderby_field ); ?>
	</p>
	<p>
		<?php printf( __( 'Display gallery images in %s order by default.', 'cleaner-gallery' ), $order_field ); ?>
		<br />
		<span class="description"><?php _e( '(These settings may be overriden for individual galleries.)', 'cleaner-gallery' ); ?></span>
	</p>
<?php }

function cleaner_gallery_meta_box_display_caption( $object, $box ) { ?>

	<p>
		<input id="caption_remove" name="cleaner_gallery_settings[caption_remove]" type="checkbox" <?php checked( ( cleaner_gallery_get_setting( 'caption_remove' ) ? true : false ), true ); ?> value="true" /> 
		<label for="caption_remove"><?php _e( 'Completely remove image captions (overrules other caption settings).', 'cleaner-gallery' ); ?></label>
	</p>
	<p>
		<input id="caption_title" name="cleaner_gallery_settings[caption_title]" type="checkbox" <?php checked( ( cleaner_gallery_get_setting( 'caption_title' ) ? true : false ), true ); ?> value="true" /> 
		<label for="caption_title"><?php _e( 'Use the image title as a caption if there is no caption available.', 'cleaner-gallery' ); ?></label>
	</p>
	<p>
		<input id="caption_link" name="cleaner_gallery_settings[caption_link]" type="checkbox" <?php checked( ( cleaner_gallery_get_setting( 'caption_link' ) ? true : false ), true ); ?> value="true" /> 
		<label for="caption_link"><?php _e( 'Link captions to the image attachment page.', 'cleaner-gallery' ); ?></label>
	</p>
<?php }

function cleaner_gallery_meta_box_display_script( $object, $box ) {
	/* Set up an array of supported Lightbox-type scripts the plugin supports. */
	$scripts = array( 
		'' => '', 
		'colorbox' => __( 'Colorbox', 'cleaner-gallery' ), 
		'fancybox' => __( 'FancyBox', 'cleaner-gallery' ), 
		'fancyzoom' => __( 'FancyZoom', 'cleaner-gallery' ), 
		'floatbox' => __( 'Floatbox', 'cleaner-gallery' ), 
		'greybox' => __( 'GreyBox', 'cleaner-gallery' ), 
		'jquery_lightbox' => __( 'jQuery Lightbox', 'cleaner-gallery' ),
		'jquery_lightbox_plugin' => __( 'jQuery Lightbox Plugin', 'cleaner-gallery' ), 
		'jquery_lightbox_balupton' => __( 'jQuery Lightbox (Balupton)', 'cleaner-gallery' ), 
		'lightbox' => __( 'Lightbox', 'cleaner-gallery' ), 
		'lightview' => __( 'Lightview', 'cleaner-gallery' ), 
		'lightwindow' => __( 'LightWindow', 'cleaner-gallery' ), 
		'lytebox' => __( 'Lytebox', 'cleaner-gallery' ), 
		'pretty_photo' => __( 'prettyPhoto', 'cleaner-gallery' ), 
		'shadowbox' => __( 'Shadowbox', 'cleaner-gallery' ), 
		'shutter_reloaded' => __( 'Shutter Reloaded', 'cleaner-gallery' ), 
		'slimbox' => __( 'Slimbox', 'cleaner-gallery' ), 
		'thickbox' => __( 'Thickbox', 'cleaner-gallery' )
	); ?>

	<p>
		<input id="cleaner_gallery_css" name="cleaner_gallery_settings[cleaner_gallery_css]" type="checkbox" <?php checked( ( cleaner_gallery_get_setting( 'cleaner_gallery_css' ) ? true : false ), true ); ?> value="true" />
		<label for="cleaner_gallery_css"><?php _e( 'Load the Cleaner Gallery stylesheet (used to format galleries).', 'cleaner-gallery' ); ?></label>
	</p>
	<p>
		<input id="thickbox_js" name="cleaner_gallery_settings[thickbox_js]" type="checkbox" <?php checked( ( cleaner_gallery_get_setting( 'thickbox_js' ) ? true : false ), true ); ?> value="true" /> 
		<label for="thickbox_js"><?php _e( 'Load the Thickbox JavaScript (included with WordPress).', 'cleaner-gallery' ); ?></label>
	</p>
	<p>
		<input id="thickbox_css" name="cleaner_gallery_settings[thickbox_css]" type="checkbox" <?php checked( ( cleaner_gallery_get_setting( 'thickbox_css' ) ? true : false ), true ); ?> value="true" /> 
		<label for="thickbox_css"><?php _e( 'Load the Thickbox stylesheet (included with WordPress).', 'cleaner-gallery' ); ?></label>
	</p>
	<p>
		<?php _e( 'Select an image script to use with your galleries.', 'cleaner-gallery' ); ?>
		<select name="cleaner_gallery_settings[image_script]" id="image_script">
			<?php foreach ( $scripts as $option => $option_name ) { ?>
				<option value="<?php echo $option; ?>" <?php selected( $option, cleaner_gallery_get_setting( 'image_script' ) ); ?>><?php echo $option_name; ?></option>
			<?php } ?>
		</select>
		<br />
		<span class="description"><?php _e( 'The use, installation, and configuration of third-party image scripts are not supported by the Cleaner Gallery plugin developer. Please contact the image script developer for help using your preferred script.', 'cleaner-gallery' ); ?></span>
	</p>
<?php }

?>