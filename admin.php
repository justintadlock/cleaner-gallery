<?php
/**
 * Administration functions for loading and displaying the settings page and saving settings 
 * are handled in this file.
 *
 * @package CleanerGallery
 */

/* Initialize the theme admin functionality. */
add_action( 'init', 'cleaner_gallery_admin_init' );

/**
 * Initializes the theme administration functions.
 *
 * @since 0.8
 */
function cleaner_gallery_admin_init() {
	add_action( 'admin_menu', 'cleaner_gallery_settings_page_init' );

	add_action( 'cleaner_gallery_update_settings_page', 'cleaner_gallery_save_settings' );
}

/**
 * Sets up the cleaner gallery settings page and loads the appropriate functions when needed.
 *
 * @since 0.8
 */
function cleaner_gallery_settings_page_init() {
	global $cleaner_gallery;

	/* Create the theme settings page. */
	$cleaner_gallery->settings_page = add_theme_page( __( 'Cleaner Gallery', 'cleaner-gallery' ), __( 'Cleaner Gallery', 'cleaner-gallery' ), 'edit_theme_options', 'cleaner-gallery', 'cleaner_gallery_settings_page' );

	/* Register the default theme settings meta boxes. */
	add_action( "load-{$cleaner_gallery->settings_page}", 'cleaner_gallery_create_settings_meta_boxes' );

	/* Make sure the settings are saved. */
	add_action( "load-{$cleaner_gallery->settings_page}", 'cleaner_gallery_load_settings_page' );

	/* Load the JavaScript and stylehsheets needed for the theme settings. */
	add_action( "load-{$cleaner_gallery->settings_page}", 'cleaner_gallery_settings_page_enqueue_script' );
	add_action( "load-{$cleaner_gallery->settings_page}", 'cleaner_gallery_settings_page_enqueue_style' );
	add_action( "admin_head-{$cleaner_gallery->settings_page}", 'cleaner_gallery_settings_page_load_scripts' );
}

/**
 * Returns an array with the default plugin settings.
 *
 * @since 0.8
 */
function cleaner_gallery_settings() {
	$settings = array(
		'image_link' => 'attachment',
		'caption_remove' => false,
		'caption_link' => true,
		'caption_title' => false,
		'size' => 'thumbnail',
		'orderby' => 'menu_order',
		'order' => 'ASC',
		'thickbox_js' => false,
		'thickbox_css' => false,
		'image_script' => false,
	);
	return apply_filters( 'cleaner_gallery_settings', $settings );
}

/**
 * Function run at load time of the settings page, which is useful for hooking save functions into.
 *
 * @since 0.8
 */
function cleaner_gallery_load_settings_page() {

	/* Get theme settings from the database. */
	$settings = get_option( 'cleaner_gallery_settings' );

	/* If no settings are available, add the default settings to the database. */
	if ( empty( $settings ) ) {
		add_option( 'cleaner_gallery_settings', cleaner_gallery_settings(), '', 'yes' );

		/* Redirect the page so that the settings are reflected on the settings page. */
		wp_redirect( admin_url( 'themes.php?page=cleaner-gallery' ) );
		exit;
	}

	/* If the form has been submitted, check the referer and execute available actions. */
	elseif ( isset( $_POST['cleaner-gallery-settings-submit'] ) ) {

		/* Make sure the form is valid. */
		check_admin_referer( 'cleaner-gallery-settings-page' );

		/* Available hook for saving settings. */
		do_action( 'cleaner_gallery_update_settings_page' );

		/* Redirect the page so that the new settings are reflected on the settings page. */
		wp_redirect( admin_url( 'themes.php?page=cleaner-gallery&updated=true' ) );
		exit;
	}
}

/**
 * Validates the plugin settings.
 *
 * @since 0.8
 */
function cleaner_gallery_save_settings() {

	/* Get the current theme settings. */
	$settings = get_option( 'cleaner_gallery_settings' );

	$settings['image_link'] = esc_html( $_POST['image_link'] );
	$settings['size'] = esc_html( $_POST['size'] );
	$settings['orderby'] = esc_html( $_POST['orderby'] );
	$settings['order'] = esc_html( $_POST['order'] );
	$settings['image_script'] = esc_html( $_POST['image_script'] );
	$settings['caption_remove'] = ( ( isset( $_POST['caption_remove'] ) ) ? true : false );
	$settings['caption_link'] = ( ( isset( $_POST['caption_link'] ) ) ? true : false );
	$settings['caption_title'] = ( ( isset( $_POST['caption_title'] ) ) ? true : false );
	$settings['thickbox_js'] = ( ( isset( $_POST['thickbox_js'] ) ) ? true : false );
	$settings['thickbox_css'] = ( ( isset( $_POST['thickbox_css'] ) ) ? true : false );

	/* Update the theme settings. */
	$updated = update_option( 'cleaner_gallery_settings', $settings );
}

/**
 * Registers the plugin meta boxes for use on the settings page.
 *
 * @since 0.8
 */
function cleaner_gallery_create_settings_meta_boxes() {
	global $cleaner_gallery;

	add_meta_box( 'cleaner-gallery-about-meta-box', __( 'About Cleaner Gallery', 'cleaner-gallery' ), 'cleaner_gallery_about_meta_box', $cleaner_gallery->settings_page, 'normal', 'high' );

	add_meta_box( 'cleaner-gallery-general-meta-box', __( 'Gallery Settings', 'cleaner-gallery' ), 'cleaner_gallery_general_meta_box', $cleaner_gallery->settings_page, 'normal', 'high' );

	add_meta_box( 'cleaner-gallery-javascript-meta-box', __( 'JavaScript Settings', 'cleaner-gallery' ), 'cleaner_gallery_javascript_meta_box', $cleaner_gallery->settings_page, 'normal', 'high' );
}

/**
 * Displays the about meta box.
 *
 * @since 0.8
 */
function cleaner_gallery_about_meta_box() {
	$plugin_data = get_plugin_data( CLEANER_GALLERY_DIR . 'cleaner-gallery.php' ); ?>

	<table class="form-table">
		<tr>
			<th><?php _e( 'Plugin:', 'cleaner-gallery' ); ?></th>
			<td><?php echo $plugin_data['Title']; ?> <?php echo $plugin_data['Version']; ?></td>
		</tr>
		<tr>
			<th><?php _e( 'Author:', 'cleaner-gallery' ); ?></th>
			<td><?php echo $plugin_data['Author']; ?></td>
		</tr>
		<tr>
			<th><?php _e( 'Description:', 'cleaner-gallery' ); ?></th>
			<td><?php echo $plugin_data['Description']; ?></td>
		</tr>
	</table><!-- .form-table --><?php
}

/**
 * Displays the gallery settings meta box.
 *
 * @since 0.8
 */
function cleaner_gallery_general_meta_box() {

	foreach ( get_intermediate_image_sizes() as $size )
		$image_sizes[$size] = $size;

	$image_link = array( '' => '', 'none' => __( 'None', 'cleaner-gallery' ), 'attachment' => __( 'Attachment Page', 'cleaner_gallery' ) );
	$image_link = array_merge( $image_link, $image_sizes );
	$image_link['full'] = 'full'; ?>

	<table class="form-table">
		<tr>
			<th><?php _e( 'Captions:', 'cleaner-gallery' ); ?></th>
			<td>
				<input id="caption_remove" name="caption_remove" type="checkbox" <?php checked( cleaner_gallery_get_setting( 'caption_remove' ), true ); ?> value="true" /> 
				<label for="caption_remove"><?php _e( 'Do you want to remove captions from your galleries?', 'cleaner-gallery' ); ?></label>
				<br />
				<input id="caption_title" name="caption_title" type="checkbox" <?php checked( cleaner_gallery_get_setting( 'caption_title' ), true ); ?> value="true" /> 
				<label for="caption_title"><?php _e( 'Use the image title as a caption if there is no caption available?', 'cleaner-gallery' ); ?></label>
				<br />
				<input id="caption_link" name="caption_link" type="checkbox" <?php checked( cleaner_gallery_get_setting( 'caption_link' ), true ); ?> value="true" /> 
				<label for="caption_link"><?php _e( 'Link captions to the image attachment page?', 'cleaner-gallery' ); ?></label>
			</td>
		</tr>

		<tr>
			<th><?php _e( 'Default Image Link:', 'cleaner-gallery' ); ?></th>
			<td>
				<?php _e( 'Where or what should your gallery images link to?  Leave blank for the WordPress default.', 'cleaner_gallery' ); ?>
				<br />
				<select id="image_link" name="image_link">
					<?php foreach ( $image_link as $option => $option_name ) { ?>
						<option <?php selected( $option, cleaner_gallery_get_setting( 'image_link' ) ); ?> value="<?php echo $option; ?>"><?php echo $option_name; ?></option>
					<?php } ?>
				</select>
			</td>
		</tr>

		<tr>
			<th><?php _e( 'Default Image Size:', 'cleaner-gallery' ); ?></th>
			<td>
				<select name="size" id="size">
					<?php foreach ( get_intermediate_image_sizes() as $size ) { ?>
						<option value="<?php echo $size; ?>" <?php selected( $size, cleaner_gallery_get_setting( 'size' ) ); ?>><?php echo $size; ?></option>
					<?php } ?>
				</select>
			</td>
		</tr>

		<tr>
			<th><?php _e( 'Default Order:', 'cleaner-gallery' ); ?></th>
			<td>
				<select name="order" id="order">
					<?php foreach ( array( 'ASC', 'DESC' ) as $order ) { ?>
						<option value="<?php echo $order; ?>" <?php selected( $order, cleaner_gallery_get_setting( 'order' ) ); ?>><?php echo $order; ?></option>
					<?php } ?>
				</select>
			</td>
		</tr>

		<tr>
			<th><?php _e( 'Default Orderby:', 'cleaner-gallery' ); ?></th>
			<td>
				<select name="orderby" id="orderby">
					<?php $orderby_options = array( 'comment_count' => __( 'Comment Count', 'cleaner-gallery' ), 'date' => __( 'Date', 'cleaner-gallery' ), 'ID' => __( 'ID', 'cleaner-gallery' ), 'menu_order' => __( 'Menu Order', 'cleaner-gallery' ), 'none' => __( 'None', 'cleaner-gallery' ), 'rand' => __( 'Random', 'cleaner-gallery' ), 'title' => __( 'Title', 'cleaner-gallery' ) ); ?>
					<?php foreach ( $orderby_options as $option => $option_name ) { ?>
						<option value="<?php echo $option; ?>" <?php selected( $option, cleaner_gallery_get_setting( 'orderby' ) ); ?>><?php echo $option_name; ?></option>
					<?php } ?>
				</select>
			</td>
		</tr>
	</table><!-- .form-table --><?php
}

/**
 * Displays the JavaScript meta box.
 *
 * @since 0.8
 */
function cleaner_gallery_javascript_meta_box() {
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
		'shadowbox' => __( 'Shadowbox', 'cleaner-gallery' ), 
		'shutter_reloaded' => __( 'Shutter Reloaded', 'cleaner-gallery' ), 
		'slimbox' => __( 'Slimbox', 'cleaner-gallery' ), 
		'thickbox' => __( 'Thickbox', 'cleaner-gallery' )
	); ?>
	<table class="form-table">
		<tr>
			<th><?php _e( 'About:', 'cleaner-gallery' ); ?></th>
			<td>
				<?php _e( 'If you are using a Lightbox-type image script, this plugin will work along with it.  There are several supported scripts to choose from.', 'cleaner_gallery' ); ?>
			</td>
		</tr>
		<tr>
			<th><?php _e( 'Image Script:', 'cleaner-gallery' ); ?></th>
			<td>
				<select name="image_script" id="image_script">
					<?php foreach ( $scripts as $option => $option_name ) { ?>
						<option value="<?php echo $option; ?>" <?php selected( $option, cleaner_gallery_get_setting( 'image_script' ) ); ?>><?php echo $option_name; ?></option>
					<?php } ?>
				</select>
			</td>
		</tr>
		<tr>
			<th><?php _e( 'Thickbox:', 'cleaner-gallery' ); ?></th>
			<td>
				<input id="thickbox_js" name="thickbox_js" type="checkbox" <?php checked( cleaner_gallery_get_setting( 'thickbox_js' ), true ); ?> value="true" /> 
				<label for="thickbox_js"><?php _e( 'Auto-load Thickbox JavaScript (included with WordPress)?', 'cleaner-gallery' ); ?></label>
				<br />
				<input id="thickbox_css" name="thickbox_css" type="checkbox" <?php checked( cleaner_gallery_get_setting( 'thickbox_css' ), true ); ?> value="true" /> 
				<label for="thickbox_css"><?php _e( 'Auto-load Thickbox CSS (included with WordPress)?', 'cleaner-gallery' ); ?></label>
			</td>
		</tr>
	</table><?php
}

/**
 * Displays a settings saved message.
 *
 * @since 0.8
 */
function cleaner_gallery_settings_update_message() { ?>
	<p class="updated fade below-h2" style="padding: 5px 10px;">
		<strong><?php _e( 'Settings saved.', 'cleaner-gallery' ); ?></strong>
	</p><?php
}

/**
 * Outputs the HTML and calls the meta boxes for the settings page.
 *
 * @since 0.8
 */
function cleaner_gallery_settings_page() {
	global $cleaner_gallery;

	$plugin_data = get_plugin_data( CLEANER_GALLERY_DIR . 'cleaner-gallery.php' ); ?>

	<div class="wrap">

		<h2><?php _e( 'Cleaner Gallery Settings', 'cleaner-gallery' ); ?></h2>

		<?php if ( isset( $_GET['updated'] ) && 'true' == esc_attr( $_GET['updated'] ) ) cleaner_gallery_settings_update_message(); ?>

		<div id="poststuff">

			<form method="post" action="<?php admin_url( 'themes.php?page=cleaner-gallery' ); ?>">

				<?php wp_nonce_field( 'cleaner-gallery-settings-page' ); ?>
				<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
				<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>

				<div class="metabox-holder">
					<div class="post-box-container column-1 normal"><?php do_meta_boxes( $cleaner_gallery->settings_page, 'normal', $plugin_data ); ?></div>
					<div class="post-box-container column-2 advanced"><?php do_meta_boxes( $cleaner_gallery->settings_page, 'advanced', $plugin_data ); ?></div>
					<div class="post-box-container column-3 side"><?php do_meta_boxes( $cleaner_gallery->settings_page, 'side', $plugin_data ); ?></div>
				</div>

				<p class="submit" style="clear: both;">
					<input type="submit" name="Submit"  class="button-primary" value="<?php _e( 'Update Settings', 'cleaner-gallery' ); ?>" />
					<input type="hidden" name="cleaner-gallery-settings-submit" value="true" />
				</p><!-- .submit -->

			</form>

		</div><!-- #poststuff -->

	</div><!-- .wrap --><?php
}

/**
 * Loads the scripts needed for the settings page.
 *
 * @since 0.8
 */
function cleaner_gallery_settings_page_enqueue_script() {
	wp_enqueue_script( 'common' );
	wp_enqueue_script( 'wp-lists' );
	wp_enqueue_script( 'postbox' );
}

/**
 * Loads the stylesheets needed for the settings page.
 *
 * @since 0.8
 */
function cleaner_gallery_settings_page_enqueue_style() {
	wp_enqueue_style( 'cleaner-gallery-admin', CLEANER_GALLERY_URL . 'admin.css', false, 0.7, 'screen' );
}

/**
 * Loads the metabox toggle JavaScript in the settings page head.
 *
 * @since 0.8
 */
function cleaner_gallery_settings_page_load_scripts() {
	global $cleaner_gallery; ?>
	<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready( function($) {
			$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
			postboxes.add_postbox_toggles( '<?php echo $cleaner_gallery->settings_page; ?>' );
		});
		//]]>
	</script><?php
}

?>