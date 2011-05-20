<?php
/**
 * Handles the administration functions for the Cleaner Gallery plugin, sets up the plugin settings page, and loads 
 * any and all files dealing with the admin for the plugin.
 *
 * @package HybridHook
 */

/* Set up the administration functionality. */
add_action( 'admin_menu', 'cleaner_gallery_admin_setup' );

function cleaner_gallery_admin_setup() {
	global $cleaner_gallery;

	/* Register the plugin settings. */
	add_action( 'admin_init', 'cleaner_gallery_register_settings' );

	/* Add Cleaner Gallery settings page. */
	$cleaner_gallery->settings_page = add_theme_page( __( 'Cleaner Gallery Settings', 'cleaner-gallery' ), __( 'Cleaner Gallery', 'cleaner-gallery' ), 'edit_theme_options', 'cleaner-gallery', 'cleaner_gallery_settings_page' );

	/* Add media for the settings page. */
	add_action( "load-{$cleaner_gallery->settings_page}", 'cleaner_gallery_admin_enqueue_style' );
	add_action( "load-{$cleaner_gallery->settings_page}", 'cleaner_gallery_settings_page_media' );
	add_action( "admin_head-{$cleaner_gallery->settings_page}", 'cleaner_gallery_settings_page_scripts' );

	/* Add default settings if none are present. */
	add_action( "load-{$cleaner_gallery->settings_page}", 'cleaner_gallery_load_settings_page' );

	/* Load the meta boxes. */
	add_action( "load-{$cleaner_gallery->settings_page}", 'cleaner_gallery_load_meta_boxes' );

	/* Create a hook for adding meta boxes. */
	add_action( "load-{$cleaner_gallery->settings_page}", 'cleaner_gallery_add_meta_boxes' );
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
 * Registers the Cleaner Gallery settings.
 * @uses register_setting() to add the settings to the database.
 *
 * @since 0.2.0
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
 * Executes the 'add_meta_boxes' action hook because WordPress doesn't fire this on custom admin pages.
 *
 * @since 0.3.0
 */
function cleaner_gallery_add_meta_boxes() {
	global $cleaner_gallery;
	$plugin_data = get_plugin_data( CLEANER_GALLERY_DIR . 'cleaner-gallery.php' );
	do_action( 'add_meta_boxes', $cleaner_gallery->settings_page, $plugin_data );
}

/**
 * Loads the plugin settings page meta boxes.
 *
 * @since 0.3.0
 */
function cleaner_gallery_load_meta_boxes() {
	require_once( CLEANER_GALLERY_DIR . 'meta-boxes.php' );
}

/**
 * Function for validating the settings input from the plugin settings page.
 *
 * @since 0.9.0
 */
function cleaner_gallery_validate_settings( $input ) {
	$settings = $input;
	return $settings;
}

/**
 * Displays the HTML and meta boxes for the plugin settings page.
 *
 * @since 0.2.0
 */
function cleaner_gallery_settings_page() {
	global $cleaner_gallery; ?>

	<div class="wrap">

		<?php screen_icon(); ?>

		<h2><?php _e( 'Cleaner Gallery Plugin Settings', 'cleaner-gallery' ); ?></h2>

		<?php if ( isset( $_GET['settings-updated'] ) && 'true' == esc_attr( $_GET['settings-updated'] ) ) echo '<div class="updated"><p><strong>' . __( 'Settings saved.', 'cleaner-gallery' ) . '</strong></p></div>'; ?>

		<div id="poststuff">

			<form method="post" action="options.php">

				<?php settings_fields( 'cleaner_gallery_settings' ); ?>
				<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
				<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>

				<div class="metabox-holder">
					<div class="post-box-container column-1 normal"><?php do_meta_boxes( $cleaner_gallery->settings_page, 'normal', null ); ?></div>
					<div class="post-box-container column-2 side"><?php do_meta_boxes( $cleaner_gallery->settings_page, 'side', null ); ?></div>
				</div>

				<?php submit_button( esc_attr__( 'Update Settings', 'cleaner-gallery' ) ); ?>

			</form>

		</div><!-- #poststuff -->

	</div><!-- .wrap --><?php
}

/**
 * Loads the admin stylesheet for the plugin settings page.
 *
 * @since 0.3.0
 */
function cleaner_gallery_admin_enqueue_style() {
	wp_enqueue_style( 'cleaner-gallery-admin', trailingslashit( CLEANER_GALLERY_URI ) . 'css/admin.css', false, 0.3, 'screen' );
}

/**
 * Loads needed JavaScript files for handling the meta boxes on the settings page.
 *
 * @since 0.2.0
 */
function cleaner_gallery_settings_page_media() {
	wp_enqueue_script( 'common' );
	wp_enqueue_script( 'wp-lists' );
	wp_enqueue_script( 'postbox' );
}

/**
 * Loads JavaScript for handling the open/closed state of each meta box.
 *
 * @since 0.2.0
 * @global $cleaner_gallery The path of the settings page.
 */
function cleaner_gallery_settings_page_scripts() {
	global $cleaner_gallery; ?>
	<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready( function($) {
			$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
			postboxes.add_postbox_toggles( '<?php echo $cleaner_gallery->settings_page; ?>' );
		});
		//]]>
	</script>
<?php }

?>