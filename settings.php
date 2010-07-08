<?php
/**
 * Cleaner Gallery settings page
 * This file displays all of the available settings
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package CleanerGallery
 */
?>
<div class="postbox open">

<h3><?php _e('About This Plugin','cleaner_gallery'); ?></h3>

<div class="inside">
	<table class="form-table">

	<tr>
		<th><?php _e('Plugin Description:','cleaner_gallery'); ?></th>
		<td><?php echo $plugin_data['Description']; ?></td>
	</tr>
	<tr>
		<th><?php _e('Plugin Version:','cleaner_gallery'); ?></th>
		<td><?php echo $plugin_data['Name']; ?> <?php echo $plugin_data['Version']; ?></td>
	</tr>
	<tr>
		<th><?php _e('Plugin Documentation:','cleaner_gallery'); ?></th>
		<td><?php _e('Read the <code>readme.html</code> file included with the plugin to see how this plugin works.','cleaner_gallery'); ?></td>
	</tr>
	<tr>
		<th><?php _e('Plugin Support:','cleaner_gallery'); ?></th>
		<td><a href="http://themehybrid.com/support" title="<?php _e('Get support for this plugin','cleaner_gallery'); ?>"><?php _e('Visit the support forums.','cleaner_gallery'); ?></a></td>
	</tr>

	</table>
</div>
</div>

<div class="postbox open">

<h3><?php _e('Gallery Settings','cleaner_gallery'); ?></h3>

<div class="inside">

	<table class="form-table">
	<tr>
		<th>
			<label for="<?php echo $data['image_link']; ?>"><?php _e('Image Link:','cleaner_gallery'); ?></label>
		</th>
		<td>
			<?php _e('Where or what should your gallery images link to?  Leave blank to control on a post-by-post basis.', 'cleaner_gallery'); ?>
			<br />
			<select id="<?php echo $data['image_link']; ?>" name="<?php echo $data['image_link']; ?>">
				<option <?php if ( !$val['image_link'] ) echo ' selected="selected"'; ?>></option>
				<option <?php if ( __('Attachment Page', 'cleaner_gallery' ) == $val['image_link'] ) echo ' selected="selected"'; ?>><?php _e('Attachment Page', 'cleaner_gallery'); ?></option>
				<option <?php if ( __('Thumbnail', 'cleaner_gallery' ) == $val['image_link'] ) echo ' selected="selected"'; ?>><?php _e('Thumbnail', 'cleaner_gallery'); ?></option>
				<option <?php if ( __('Medium', 'cleaner_gallery' ) == $val['image_link'] ) echo ' selected="selected"'; ?>><?php _e('Medium', 'cleaner_gallery'); ?></option>
				<option <?php if ( __('Large', 'cleaner_gallery' ) == $val['image_link'] ) echo ' selected="selected"'; ?>><?php _e('Large', 'cleaner_gallery'); ?></option>
				<option <?php if ( __('Full', 'cleaner_gallery' ) == $val['image_link'] ) echo ' selected="selected"'; ?>><?php _e('Full', 'cleaner_gallery'); ?></option>
				<option <?php if ( __('No Link', 'cleaner_gallery' ) == $val['image_link'] ) echo ' selected="selected"'; ?>><?php _e('No Link', 'cleaner_gallery'); ?></option>
			</select>
		</td>
	</tr>

	<tr>
		<th>
			<label for="<?php echo $data['caption_remove']; ?>"><?php _e('Captions:','hook'); ?></label>
		</th>
		<td>
			<input id="<?php echo $data['caption_remove']; ?>" name="<?php echo $data['caption_remove']; ?>" type="checkbox" <?php if ( $val['caption_remove'] ) echo 'checked="checked"'; ?> value="true" /> 
			<label for="<?php echo $data['caption_remove']; ?>">
				<?php _e('Do you want to remove captions from your galleries?', 'cleaner_gallery'); ?>
			</label>
			<br />
			<input id="<?php echo $data['caption_title']; ?>" name="<?php echo $data['caption_title']; ?>" type="checkbox" <?php if ( $val['caption_title'] ) echo 'checked="checked"'; ?> value="true" /> 
			<label for="<?php echo $data['caption_title']; ?>">
				<?php _e('Use the image title as a caption if there is no caption available?', 'cleaner_gallery'); ?>
			</label>
			<br />
			<input id="<?php echo $data['caption_link']; ?>" name="<?php echo $data['caption_link']; ?>" type="checkbox" <?php if ( $val['caption_link'] ) echo 'checked="checked"'; ?> value="true" /> 
			<label for="<?php echo $data['caption_link']; ?>">
				<?php _e('Link captions to the image attachment page?', 'cleaner_gallery'); ?>
			</label>
		</td>
	</tr>

	<tr>
		<th>
			<label for="<?php echo $data['size']; ?>"><?php _e('Default Image Size:','cleaner_gallery'); ?></label>
		</th>
		<td>
			<select id="<?php echo $data['size']; ?>" name="<?php echo $data['size']; ?>">
				<option <?php if ( 'thumbnail' == $val['size'] ) echo ' selected="selected"'; ?>>thumbnail</option>
				<option <?php if ( 'medium' == $val['size'] ) echo ' selected="selected"'; ?>>medium</option>
				<option <?php if ( 'large' == $val['size'] ) echo ' selected="selected"'; ?>>large</option>
				<option <?php if ( 'full' == $val['size'] ) echo ' selected="selected"'; ?>>full</option>
			</select>
		</td>
	</tr>

	<tr>
		<th>
			<label for="<?php echo $data['order']; ?>"><?php _e('Default Order:','cleaner_gallery'); ?></label>
		</th>
		<td>
			<select id="<?php echo $data['order']; ?>" name="<?php echo $data['order']; ?>">
				<option <?php if ( 'ASC' == $val['order'] ) echo ' selected="selected"'; ?>>ASC</option>
				<option <?php if ( 'DESC' == $val['order'] ) echo ' selected="selected"'; ?>>DESC</option>
			</select>
		</td>
	</tr>

	<tr>
		<th>
			<label for="<?php echo $data['orderby']; ?>"><?php _e('Default Orderby:','cleaner_gallery'); ?></label>
		</th>
		<td>
			<select id="<?php echo $data['orderby']; ?>" name="<?php echo $data['orderby']; ?>">
				<option <?php if ( 'menu_order ID' == $val['orderby'] ) echo ' selected="selected"'; ?>>menu_order ID</option>
				<option <?php if ( 'ID' == $val['orderby'] ) echo ' selected="selected"'; ?>>ID</option>
				<option <?php if ( 'post_title' == $val['orderby'] ) echo ' selected="selected"'; ?>>post_title</option>
				<option <?php if ( 'rand' == $val['orderby'] ) echo ' selected="selected"'; ?>>rand</option>
			</select>
		</td>
	</tr>

	</table>
</div>
</div>


<div class="postbox open">

<h3><?php _e('JavaScript Settings','cleaner_gallery'); ?></h3>

<div class="inside">
	<table class="form-table">
	<tr>
		<th><?php _e('About', 'cleaner_gallery'); ?></th>
		<td>
			<?php _e('If you are using a Lightbox-type image script, this plugin will work along with it.  There are several supported scripts at this time.  There\'s also the option of manually inputting the <code>class</code> and/or <code>rel</code> tags if you\'re using an unsupported script.  If you have suggestions for others, please let me know.', 'cleaner_gallery'); ?>
		</td>
	</tr>

	<tr>
		<th>
			<label for="<?php echo $data['image_script']; ?>"><?php _e('Image Script:','cleaner_gallery'); ?></label>
		</th>
		<td>
			<select id="<?php echo $data['image_script']; ?>" name="<?php echo $data['image_script']; ?>">
				<option <?php if ( !$val['image_script'] ) echo ' selected="selected"'; ?>></option>
				<option <?php if ( 'FancyBox' == $val['image_script'] ) echo ' selected="selected"'; ?>>FancyBox</option>
				<option <?php if ( 'FancyZoom' == $val['image_script'] ) echo ' selected="selected"'; ?>>FancyZoom</option>
				<option <?php if ( 'Floatbox' == $val['image_script'] ) echo ' selected="selected"'; ?>>Floatbox</option>
				<option <?php if ( 'GreyBox' == $val['image_script'] ) echo ' selected="selected"'; ?>>GreyBox</option>
				<option <?php if ( 'jQuery Lightbox' == $val['image_script'] ) echo ' selected="selected"'; ?>>jQuery Lightbox</option>
				<option <?php if ( 'jQuery Lightbox Plugin' == $val['image_script'] ) echo ' selected="selected"'; ?>>jQuery Lightbox Plugin</option>
				<option <?php if ( 'jQuery Lightbox Plugin (Balupton)' == $val['image_script'] ) echo ' selected="selected"'; ?>>jQuery Lightbox Plugin (Balupton)</option>
				<option <?php if ( 'Lightbox 2' == $val['image_script'] ) echo ' selected="selected"'; ?>>Lightbox 2</option>
				<option <?php if ( 'Lightview' == $val['image_script'] ) echo ' selected="selected"'; ?>>Lightview</option>
				<option <?php if ( 'LightWindow' == $val['image_script'] ) echo ' selected="selected"'; ?>>LightWindow</option>
				<option <?php if ( 'Lytebox' == $val['image_script'] ) echo ' selected="selected"'; ?>>Lytebox</option>
				<option <?php if ( 'Shadowbox' == $val['image_script'] ) echo ' selected="selected"'; ?>>Shadowbox</option>
				<option <?php if ( 'Shutter Reloaded' == $val['image_script'] ) echo ' selected="selected"'; ?>>Shutter Reloaded</option>
				<option <?php if ( 'Slimbox' == $val['image_script'] ) echo ' selected="selected"'; ?>>Slimbox</option>
				<option <?php if ( 'Slimbox 2' == $val['image_script'] ) echo ' selected="selected"'; ?>>Slimbox 2</option>
				<option <?php if ( 'Thickbox' == $val['image_script'] ) echo ' selected="selected"'; ?>>Thickbox</option>
			</select> 
			<?php _e('Choose the image script you\'ve installed from the list.', 'cleaner_gallery'); ?>
		</td>
	</tr>

	<tr>
		<th>
			<label for="<?php echo $data['thickbox_js']; ?>"><?php _e('Thickbox:','hook'); ?></label>
		</th>
		<td>
			<input id="<?php echo $data['thickbox_js']; ?>" name="<?php echo $data['thickbox_js']; ?>" type="checkbox" <?php if ( $val['thickbox_js'] ) echo 'checked="checked"'; ?> value="true" /> 
			<label for="<?php echo $data['thickbox_js']; ?>">
				<?php _e('Auto-load Thickbox JavaScript (included with WordPress)?', 'cleaner_gallery'); ?>
			</label>
			<br />
			<input id="<?php echo $data['thickbox_css']; ?>" name="<?php echo $data['thickbox_css']; ?>" type="checkbox" <?php if ( $val['thickbox_css'] ) echo 'checked="checked"'; ?> value="true" /> 
			<label for="<?php echo $data['thickbox_css']; ?>">
				<?php _e('Auto-load Thickbox CSS (included with WordPress)?', 'cleaner_gallery'); ?>
			</label>
		</td>
	</tr>

	<tr>
		<th>
			<label for="<?php echo $data['image_class']; ?>"><?php _e('Alternate Script:','js_logic'); ?></label> 
		</th>
		<td>
			<input id="<?php echo $data['image_class']; ?>" name="<?php echo $data['image_class']; ?>" value="<?php echo stripslashes($val['image_class']); ?>" size="20" /> 
			<?php _e('Input a custom class attribute.', 'cleaner_gallery'); ?> <code>&lt;a class="custom"&gt;</code>
			<br />
			<input id="<?php echo $data['image_rel']; ?>" name="<?php echo $data['image_rel']; ?>" value="<?php echo stripslashes($val['image_rel']); ?>" size="20" /> 
			<?php _e('Input a custom rel attribute.', 'cleaner_gallery'); ?> <code>&lt;a rel="custom"&gt;</code>
		</td>
	</tr>

	</table>
</div>
</div>