<?php
/**
 * Cleaner Gallery plugin settings page.  This page is added to the themes page ("Appearance") in the 
 * WordPress admin rather than as a sub-item for another section in the admin.  It deals with the 
 * appearance of the site.
 *
 * @package   CleanerGallery
 * @author    Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2008 - 2014, Justin Tadlock
 * @link      http://themehybrid.com/plugins/cleaner-gallery
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Plugin admin and settings setup. */
add_action( 'admin_menu', 'cleaner_gallery_admin_setup' );

/* Custom meta for plugin on the plugins admin screen. */
add_filter( 'plugin_row_meta', 'cleaner_gallery_plugin_row_meta', 10, 2 );

/**
 * Sets up the plugin settings page and registers the plugin settings.
 *
 * @since  0.9.0
 * @access public
 * @return void
 */
function cleaner_gallery_admin_setup() {

	/* Add the Cleaner Gallery settings page. */
	$settings = add_theme_page( 
		__( 'Cleaner Gallery', 'cleaner-gallery' ), 
		__( 'Cleaner Gallery', 'cleaner-gallery' ), 
		'edit_theme_options', 
		'cleaner-gallery', 
		'cleaner_gallery_settings_page' 
	);

	/* Register the plugin settings. */
	add_action( 'admin_init', 'cleaner_gallery_register_settings' );
}

/**
 * Registers the cleaner gallery settings with WordPress.
 *
 * @since  0.9.0
 * @access public
 * @return void
 */
function cleaner_gallery_register_settings() {
	register_setting( 'cleaner_gallery_settings', 'cleaner_gallery_settings', 'cleaner_gallery_validate_settings' );
}

/**
 * Validates/sanitizes the plugins settings after they've been submitted.
 *
 * @since  0.9.0
 * @access public
 * @return void
 */
function cleaner_gallery_validate_settings( $settings ) {

	$setttings['size']         = wp_filter_post_kses( $settings['size']         );
	$setttings['image_link']   = wp_filter_post_kses( $settings['image_link']   );
	$setttings['orderby']      = wp_filter_post_kses( $settings['orderby']      );
	$setttings['order']        = wp_filter_post_kses( $settings['order']        );
	$setttings['image_script'] = wp_filter_post_kses( $settings['image_script'] );

	$settings['caption_remove'] = isset( $settings['caption_remove'] ) ? 1 : 0;
	$settings['caption_title']  = isset( $settings['caption_title'] )  ? 1 : 0;
	$settings['thickbox_js']    = isset( $settings['thickbox_js'] )    ? 1 : 0;
	$settings['thickbox_css']   = isset( $settings['thickbox_css'] )   ? 1 : 0;

	return $settings;
}

/**
 * Displays the settings page for the plugin.
 *
 * @since  0.9.0
 * @access public
 * @return void
 */
function cleaner_gallery_settings_page() {

	/* Set up some default empty variables. */
	$size_field       = '';
	$image_link_field = '';
	$orderby_field    = '';
	$order_field      = '';

	/* Get the available image sizes. */
	foreach ( get_intermediate_image_sizes() as $size )
		$image_sizes[ $size ] = $size;

	$image_sizes = array_merge(
		$image_sizes,
		array( 
			'thumbnail' => __( 'Thumbnail', 'cleaner-gallery' ), 
			'medium'    => __( 'Medium',    'cleaner-gallery' ), 
			'large'     => __( 'Large',     'cleaner-gallery' ), 
			'full'      => __( 'Full',      'cleaner-gallery' )
		)
	);

	/* WP filter. */
	$image_sizes = apply_filters( 'image_size_names_choose', $image_sizes );

	/* Set up an array items that gallery items can link to. */
	$image_link = array_merge( 
		array( 
			'none' => __( 'No image or page', 'cleaner-gallery' ), 
			''     => __( 'Attachment Page',  'cleaner-gallery' ) 
		), 
		$image_sizes 
	);

	/* Set up an array of orderby options. */
	$orderby_options = array( 
		'comment_count' => __( 'Comment Count', 'cleaner-gallery' ), 
		'date'          => __( 'Date',          'cleaner-gallery' ), 
		'ID'            => __( 'ID',            'cleaner-gallery' ), 
		'menu_order ID' => __( 'Menu Order',    'cleaner-gallery' ), 
		'none'          => __( 'None',          'cleaner-gallery' ), 
		'rand'          => __( 'Random',        'cleaner-gallery' ), 
		'title'         => __( 'Title',         'cleaner-gallery' ) 
	);

	/* Set up an array of ordering options. */
	$order_options = array( 
		'ASC'  => __( 'Ascending',  'cleaner-gallery' ), 
		'DESC' => __( 'Descending', 'cleaner_gallery' ) 
	);

	/* Set up an array of supported Lightbox-type scripts the plugin supports. */
	$scripts = cleaner_gallery_get_supported_scripts();

	/* === Set up form fields for use inline with text. === */

	/* Set up the image size select element. */
	foreach ( $image_sizes as $size => $label )
		$size_field .= '<option value="' . esc_attr( $size ) . '" ' . selected( $size, cleaner_gallery_get_setting( 'size' ), false ) . '>' . esc_html( $label ) . '</option>';
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
						<?php printf( __( 'Display %s size images by default.', 'cleaner-gallery' ), $size_field ); ?>
						<br />
						<?php printf( __( 'Images should link to %s by default.', 'cleaner-gallery' ), $image_link_field ); ?>
						<br />
						<?php printf( __( 'Galleries should be ordered by %s by default.', 'cleaner-gallery' ), $orderby_field ); ?>
						<br />
						<?php printf( __( 'Display gallery images in %s order by default.', 'cleaner-gallery' ), $order_field ); ?>
						<br />
						<p class="description">
							<?php _e( '(These settings may be overriden for individual galleries.)', 'cleaner-gallery' ); ?>
						</p>
					</td>
				</tr>

				<tr>
					<th><?php _e( 'Image caption settings', 'cleaner-gallery' ); ?></th>
					<td>
						<input id="caption_remove" name="cleaner_gallery_settings[caption_remove]" type="checkbox" <?php checked( cleaner_gallery_get_setting( 'caption_remove' ) ? true : false, true ); ?> value="true" /> 
						<label for="caption_remove"><?php _e( 'Completely remove image captions (overrules other caption settings).', 'cleaner-gallery' ); ?></label>
						<br />
						<input id="caption_title" name="cleaner_gallery_settings[caption_title]" type="checkbox" <?php checked( cleaner_gallery_get_setting( 'caption_title' ) ? true : false, true ); ?> value="true" /> 
						<label for="caption_title"><?php _e( 'Use the image title as a caption if there is no caption available.', 'cleaner-gallery' ); ?></label>
					</td>
				</tr>

				<tr>
					<th><?php _e( 'Script and style settings', 'cleaner-gallery' ); ?></th>
					<td>
						<input id="thickbox_js" name="cleaner_gallery_settings[thickbox_js]" type="checkbox" <?php checked( cleaner_gallery_get_setting( 'thickbox_js' ) ? true : false, true ); ?> value="true" /> 
						<label for="thickbox_js"><?php _e( 'Load the Thickbox JavaScript (included with WordPress).', 'cleaner-gallery' ); ?></label>
						<br />
						<input id="thickbox_css" name="cleaner_gallery_settings[thickbox_css]" type="checkbox" <?php checked( cleaner_gallery_get_setting( 'thickbox_css' ) ? true : false, true ); ?> value="true" /> 
						<label for="thickbox_css"><?php _e( 'Load the Thickbox stylesheet (included with WordPress).', 'cleaner-gallery' ); ?></label>
					</td>
				</tr>

				<tr>
					<th><?php _e( 'External image script', 'cleaner-gallery' ); ?></th>
					<td>
						<select name="cleaner_gallery_settings[image_script]" id="image_script">
							<option value=""></option>
							<?php foreach ( $scripts as $option => $option_name ) { ?>
								<option value="<?php echo $option; ?>" <?php selected( $option, cleaner_gallery_get_setting( 'image_script' ) ); ?>><?php echo $option_name; ?></option>
							<?php } ?>
						</select>
						<br />
						<p class="description">
							<?php _e( 'The use, installation, and configuration of third-party image scripts are not supported by the Cleaner Gallery plugin developer. Please contact the image script developer for help using your preferred script.', 'cleaner-gallery' ); ?>
						</p>
					</td>
				</tr>

				<tr>
					<th><?php _e( 'Like this plugin?', 'cleaner-gallery' ); ?></th>
					<td>
						<p><?php _e( "Here's how you can give back:", 'cleaner-gallery' ); ?></p>
						<ul>
							<li><a href="http://wordpress.org/support/view/plugin-reviews/cleaner-gallery#postform"><?php _e( 'Give the plugin a good rating.', 'cleaner-gallery' ); ?></a></li>
							<li><a href="http://themehybrid.com/donate"><?php _e( 'Donate to the project.', 'cleaner-gallery' ); ?></a></li>
						</ul>
					</td>
				</tr>
			</table>

			<?php submit_button( esc_attr__( 'Update Settings', 'cleaner-gallery' ), 'button-primary', 'submit' ); ?>

		</form>
	</div>
<?php }

/**
 * Adds support, rating, and donation links to the plugin row meta on the plugins admin screen.
 *
 * @since  1.0.0
 * @access public
 * @param  array  $meta
 * @param  string $file
 * @return array
 */
function cleaner_gallery_plugin_row_meta( $meta, $file ) {

	if ( preg_match( '/cleaner-gallery\.php/i', $file ) ) {
		$meta[] = '<a href="http://themehybrid.com/support">' . __( 'Plugin support', 'cleaner-gallery' ) . '</a>';
		$meta[] = '<a href="http://wordpress.org/support/view/plugin-reviews/cleaner-gallery#postform">' . __( 'Rate plugin', 'cleaner-gallery' ) . '</a>';
		$meta[] = '<a href="http://themehybrid.com/donate">' . __( 'Donate', 'cleaner-gallery' ) . '</a>';
	}

	return $meta;
}

/**
 * @since      0.9.0
 * @deprecated 1.0.0
 */
function cleaner_gallery_load_settings_page() {
	_deprecated_function( __FUNCTION__, '1.0.0', '' );
}
