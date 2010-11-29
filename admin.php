<?php
/**
 * Cleaner Gallery plugin settings page.  This page is added to the themes page ("Appearance") in the 
 * WordPress admin rather than as a sub-item for another section in the admin.  It deals with the 
 * appearance of the site.
 *
 * @package CleanerGallery
 */

/* Plugin admin and settings setup. */
add_action( 'admin_menu', 'cleaner_gallery_admin_setup' );

/**
 * Sets up the plugin settings page and registers the plugin settings.
 *
 * @since 0.9.0
 */
function cleaner_gallery_admin_setup() {

	/* Add the Cleaner Gallery settings page. */
	$settings = add_theme_page( __( 'Cleaner Gallery', 'cleaner-gallery' ), __( 'Cleaner Gallery', 'cleaner-gallery' ), 'edit_theme_options', 'cleaner-gallery', 'cleaner_gallery_settings_page' );

	/* Register the plugin settings. */
	add_action( 'admin_init', 'cleaner_gallery_register_settings' );

	/* Add default settings if none are present. */
	add_action( "load-{$settings}", 'cleaner_gallery_load_settings_page' );
}

/**
 * Adds the default Cleaner Gallery settings to the database if they have not been set.
 *
 * @since 0.9.0
 */
function cleaner_gallery_load_settings_page() {

	/* Get settings from the database. */
	$settings = get_option( 'cleaner_gallery_settings' );

	/* If no settings are available, add the default settings to the database. */
	if ( empty( $settings ) ) {
		$settings = cleaner_gallery_default_settings();
		add_option( 'cleaner_gallery_settings', $settings, '', 'yes' );

		/* Redirect the page so that the settings are reflected on the settings page. */
		wp_redirect( admin_url( 'themes.php?page=cleaner-gallery' ) );
		exit;
	}
}

/**
 * Registers the cleaner gallery settings with WordPress.
 *
 * @since 0.9.0
 */
function cleaner_gallery_register_settings() {
	register_setting( 'cleaner_gallery_settings', 'cleaner_gallery_settings', 'cleaner_gallery_validate_settings' );
}

/**
 * Returns an array of the default plugin settings.  These are only used on initial setup.
 *
 * @since 0.9.0
 */
function cleaner_gallery_default_settings() {
	return array(
		'size' => 'thumbnail',
		'image_link' => '',
		'orderby' => 'menu_order',
		'order' => 'ASC',
		'caption_remove' => false,
		'caption_title' => false,
		'caption_link' => false,
		'cleaner_gallery_css' => true,
		'thickbox_js' => false,
		'thickbox_css' => false,
		'image_script' => ''
	);
}

/**
 * Validates/sanitizes the plugins settings after they've been submitted.
 *
 * @since 0.9.0
 * @todo Validation of individual settings.
 */
function cleaner_gallery_validate_settings( $input ) {
	return $input;
}

/**
 * Displays the settings page for the plugin.
 *
 * @since 0.9.0
 */
function cleaner_gallery_settings_page() {

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

	<div class="wrap">

		<?php screen_icon(); ?>

		<h2><?php _e( 'Cleaner Gallery Settings', 'cleaner-gallery' ); ?></h2>

		<?php if ( isset( $_GET['updated'] ) && 'true' == esc_attr( $_GET['updated'] ) ) echo '<div id="setting-error-settings_updated" class="updated settings-error"><p><strong>' . __( 'Settings saved.', 'cleaner-gallery' ) . '</strong></p></div>'; ?>

		<form action="<?php echo admin_url( 'options.php' ); ?>" method="post">

			<?php settings_fields( 'cleaner_gallery_settings' ); ?>

			<table class="form-table">

				<tr>
					<th><?php _e( 'Default gallery settings', 'cleaner-gallery' ); ?></th>
					<td>
					<?php
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

						<?php printf( __( 'Display %s size images by default.', 'cleaner-gallery' ), $size_field ); ?>
						<br />
						<?php printf( __( 'Images should link to %s by default.', 'cleaner-gallery' ), $image_link_field ); ?>
						<br />
						<?php printf( __( 'Galleries should be ordered by %s by default.', 'cleaner-gallery' ), $orderby_field ); ?>
						<br />
						<?php printf( __( 'Display gallery images in %s order by default.', 'cleaner-gallery' ), $order_field ); ?>
						<br />
						<small><em><?php _e( '(These settings may be overriden for individual galleries.)', 'cleaner-gallery' ); ?></em></small>
					</td>
				</tr>

				<tr>
					<th><?php _e( 'Image caption settings', 'cleaner-gallery' ); ?></th>
					<td>
						<input id="caption_remove" name="cleaner_gallery_settings[caption_remove]" type="checkbox" <?php checked( ( cleaner_gallery_get_setting( 'caption_remove' ) ? true : false ), true ); ?> value="true" /> 
						<label for="caption_remove"><?php _e( 'Completely remove image captions (overrules other caption settings).', 'cleaner-gallery' ); ?></label>
						<br />
						<input id="caption_title" name="cleaner_gallery_settings[caption_title]" type="checkbox" <?php checked( ( cleaner_gallery_get_setting( 'caption_title' ) ? true : false ), true ); ?> value="true" /> 
						<label for="caption_title"><?php _e( 'Use the image title as a caption if there is no caption available.', 'cleaner-gallery' ); ?></label>
						<br />
						<input id="caption_link" name="cleaner_gallery_settings[caption_link]" type="checkbox" <?php checked( ( cleaner_gallery_get_setting( 'caption_link' ) ? true : false ), true ); ?> value="true" /> 
						<label for="caption_link"><?php _e( 'Link captions to the image attachment page.', 'cleaner-gallery' ); ?></label>
					</td>
				</tr>

				<tr>
					<th><?php _e( 'Script and style settings', 'cleaner-gallery' ); ?></th>
					<td>
						<input id="cleaner_gallery_css" name="cleaner_gallery_settings[cleaner_gallery_css]" type="checkbox" <?php checked( ( cleaner_gallery_get_setting( 'cleaner_gallery_css' ) ? true : false ), true ); ?> value="true" />
						<label for="cleaner_gallery_css"><?php _e( 'Load the Cleaner Gallery stylesheet (used to format galleries).', 'cleaner-gallery' ); ?></label>
						<br />
						<input id="thickbox_js" name="cleaner_gallery_settings[thickbox_js]" type="checkbox" <?php checked( ( cleaner_gallery_get_setting( 'thickbox_js' ) ? true : false ), true ); ?> value="true" /> 
						<label for="thickbox_js"><?php _e( 'Load the Thickbox JavaScript (included with WordPress).', 'cleaner-gallery' ); ?></label>
						<br />
						<input id="thickbox_css" name="cleaner_gallery_settings[thickbox_css]" type="checkbox" <?php checked( ( cleaner_gallery_get_setting( 'thickbox_css' ) ? true : false ), true ); ?> value="true" /> 
						<label for="thickbox_css"><?php _e( 'Load the Thickbox stylesheet (included with WordPress).', 'cleaner-gallery' ); ?></label>
					</td>
				</tr>

				<tr>
					<th><?php _e( 'External image script', 'cleaner-gallery' ); ?></th>
					<td>
						<?php _e( 'Select an image script to use with your galleries.', 'cleaner-gallery' ); ?>
						<br />
						<select name="cleaner_gallery_settings[image_script]" id="image_script">
							<?php foreach ( $scripts as $option => $option_name ) { ?>
								<option value="<?php echo $option; ?>" <?php selected( $option, cleaner_gallery_get_setting( 'image_script' ) ); ?>><?php echo $option_name; ?></option>
							<?php } ?>
						</select>
						<br />
						<span class="description"><?php _e( 'The use, installation, and configuration of third-party image scripts are not supported by the Cleaner Gallery plugin developer. Please contact the image script developer for help using your preferred script.', 'cleaner-gallery' ); ?></span>
					</td>
				</tr>
			</table>

			<p class="submit" style="clear: both;">
				<input type="submit" name="Submit"  class="button-primary" value="<?php esc_attr_e( 'Update Settings', 'cleaner-gallery' ); ?>" />
			</p><!-- .submit -->
		</form>
	</div>
<?php }

?>