<?php
/**
 * Cleaner Gallery administration settings
 * These are the functions that allow users to select options
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package CleanerGallery
 */

/**
 * Returns an array of all the settings defaults
 * Other admin functions can grab this to use
 *
 * @since 0.5
 */
function cleaner_gallery_settings_args() {
	$settings_arr = array(
		'image_link' => __('Attachment Page', 'cleaner_gallery'),
		'caption_remove' => false,
		'caption_link' => true,
		'caption_title' => false,
		'size' => 'thumbnail',
		'orderby' => 'menu_order ID',
		'order' => 'ASC',
		'thickbox_js' => false,
		'thickbox_css' => false,
		'image_script' => false,
		'image_class' => false,
		'image_rel' => false,
	);
	return $settings_arr;
}

/**
 * Handles the main plugin settings
 *
 * @since 0.5
 */
function cleaner_gallery_theme_page() {

	/*
	* Main settings variables
	*/
	$plugin_name = __('Cleaner Gallery', 'cleaner_gallery');
	$settings_page_title = __('Cleaner Gallery Settings', 'cleaner_gallery');
	$hidden_field_name = 'cleaner_gallery_submit_hidden';
	$plugin_data = get_plugin_data(CLEANER_GALLERY . '/cleaner-gallery.php');

	/*
	* Grabs the default plugin settings
	*/
	$settings_arr = cleaner_gallery_settings_args();

	/*
	* Add a new option to the database
	*/
	add_option( 'cleaner_gallery_settings', $settings_arr );

	/*
	* Set form data IDs the same as settings keys
	* Loop through each
	*/
	$settings_keys = array_keys( $settings_arr );
	foreach ( $settings_keys as $key ) :
		$data[$key] = $key;
	endforeach;

	/*
	* Get existing options from database
	*/
	$settings = get_option( 'cleaner_gallery_settings' );

	foreach ( $settings_arr as $key => $value ) :
		$val[$key] = $settings[$key];
	endforeach;

	/*
	* If any information has been posted, we need
	* to update the options in the database
	*/
	if ( $_POST[$hidden_field_name] == 'Y' ) :

		/*
		* Loops through each option and sets it if needed
		*/
		foreach ( $settings_arr as $key => $value ) :
			$settings[$key] = $val[$key] = $_POST[$data[$key]];
		endforeach;

		/*
		* Update plugin settings
		*/
		update_option( 'cleaner_gallery_settings', $settings );

	/*
	* Output the settings page
	*/
	?>

		<div class="wrap">
			<h2><?php echo $settings_page_title; ?></h2>

		<div class="updated" style="margin: 15px 0;">
			<p><strong><?php _e('Settings saved.', 'cleaner_gallery'); ?></strong></p>
		</div>

	<?php else : ?>

		<div class="wrap">
			<h2><?php echo $settings_page_title; ?></h2>
	<?php
	endif;
?>

			<div id="poststuff">

				<form name="form0" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI'] ); ?>" style="border:none;background:transparent;">

					<?php require_once( CLEANER_GALLERY . '/settings.php' ); ?>

					<p class="submit" style="clear:both;">
						<input type="submit" name="Submit"  class="button-primary" value="<?php _e('Save Changes', 'cleaner_gallery') ?>" />
						<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y" />
					</p>

				</form>

			</div>

		</div>
<?php
}

?>