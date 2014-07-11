=== Cleaner Gallery ===

Contributors: greenshady
Donate link: http://themehybrid.com/donate
Tags: gallery, lightbox, slimbox, images, jquery, fancybox, shadowbox, thickbox, html5
Requires at least: 3.9
Stable tag: 1.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A cleaner WordPress [gallery] that integrates with multiple Lightbox-type scripts.

== Description ==

This plugin was written to take care of the invalid HTML that WordPress produces when using the `[gallery]` shortcode.

It does a bit more than that though.  It will integrate with many Lightbox-type scripts and allow you to do much cooler things with your galleries.  Plus, it has a couple of extra options that you can play around with.

### Features

* Uses HTML5 `<fig>` and `<figcaption>` elements.
* Integrates with [Schema.org microdata](http://schema.org).
* Uses the `aria-describedby` attribute to make images + captions more accessible to users with disabilities.
* Validates the invalid code that WordPress spits out.
* Several options on how you want your gallery images.
* Allows multiple galleries in a single post.
* Ability to set the number of images shown in each gallery.
* Ability to exclude or include any images from your gallery.
* Doesn't load any extra CSS or JavaScript unless you choose to do so.

### Integrates with 18 different Lightbox-type scripts

1. [Lightbox 2](http://www.huddletogether.com/projects/lightbox2/)
2. [Slimbox](http://www.digitalia.be/software/slimbox)
3. [Slimbox 2](http://www.digitalia.be/software/slimbox2)
4. [Thickbox](http://jquery.com/demo/thickbox/)
5. [Lytebox](http://dolem.com/lytebox/)
6. [Greybox](http://orangoo.com/labs/GreyBox/)
7. [Lightview](http://www.nickstakenburg.com/projects/lightview/)
8. [jQuery Lightbox Plugin](http://www.balupton.com/sandbox/jquery_lightbox/) (balupton edition)
9. [jQuery Lightbox Plugin](http://leandrovieira.com/projects/jquery/lightbox/)
10. [Shutter Reloaded](http://www.laptoptips.ca/projects/wp-shutter-reloaded/)
11. [Shadowbox](http://mjijackson.com/shadowbox/index.html)
12. [FancyBox](http://fancy.klade.lv)
13. [jQuery Lightbox](http://github.com/krewenki/jquery-lightbox/tree/master)
14. [LightWindow](http://www.stickmanlabs.com/lightwindow)
15. [FancyZoom](http://www.cabel.name/2008/02/fancyzoom-10.html)
16. [Floatbox](http://randomous.com/floatbox/home)
17. [Colorbox](http://colorpowered.com/colorbox)
18. [prettyPhoto](http://www.no-margin-for-errors.com/projects/prettyphoto-jquery-lightbox-clone)

### Professional Support

If you need professional plugin support from me, the plugin author, you can access the support forums at [Theme Hybrid](http://themehybrid.com/support), which is a professional WordPress help/support site where I handle support for all my plugins and themes for a community of 40,000+ users (and growing).

### Plugin Development

If you're a theme author, plugin author, or just a code hobbyist, you can follow the development of this plugin on it's [GitHub repository](https://github.com/justintadlock/cleaner-gallery). 

### Donations

Yes, I do accept donations.  If you want to buy me a beer or whatever, you can do so from my [donations page](http://themehybrid.com/donate).  I appreciate all donations, no matter the size.  Further development of this plugin is not contingent on donations, but they are always a nice incentive.

== Installation ==

1. Uzip the `cleaner-gallery.zip` folder.
2. Upload the `cleaner-gallery` folder to your `/wp-content/plugins` directory.
3. In your WordPress dashboard, head over to the *Plugins* section.
4. Activate *Cleaner Gallery*.

== Upgrade Notice ==

If you're upgrading from a version prior to version 1.0.0 of this plugin, please check your Cleaner Gallery plugin settings page to make sure everything is in order. A few settings changed in the update.

== Frequently Asked Questions ==

### Why was this plugin created?

The purpose of the Cleaner Gallery plugin is to get rid of the invalid HTML output by the default WordPress `[gallery]` shortcode.  Since I was having so much fun with it, I decided to make it compatible with several Lightbox-type image scripts.

It has eventually grown into an extremely robust and flexible image script.

### Why do I have one column of images?

If you expect multiple columns of images but only have one column, it's most likely that your theme is doing something incorrectly.

Also, the Cleaner Gallery plugin stylesheet doesn't load if your theme supports `cleaner-gallery`.  If that's the case, it's your theme's responsibility to style the gallery output.

### My gallery columns aren't working. What to do?

It's impossible for me to say for sure without looking at your site.  More than likely, your theme is overwriting the gallery styles.  You'd need to stop by my [support forums](http://themehybrid.com/support) for support for your specific case.

### Which Lightbox-type scripts will the plugin work with?

The plugin has been tested and works with many different Lightbox-type image scripts.  They are all listed in the plugin description.

### Can you help me set up an image script?

I don't officially support any third-party code whatsoever.  However, I'm more than willing to **attempt** helping you set up your preferred image script via my [support forums](http://themehybrid.com/support).

### Will you add support for X image script?

There's a good chance that I will.  It really depends on how feasible it is.  Some scripts would require re-coding how the plugin works, which is something I probably won't do.  However, most scripts don't require this.

If you'd like for me to add a new image script, please provide me with as much information as you can.  Particularly, a link to the page where I can download the script and view a demo of it.  That will go a long way towards getting support for the script included in this plugin.

### How do I set it up?

Technically, you don't have to set up anything at all.  It should work right out of the box.

However, you do have several configuration options that you can set from the "Appearance > Cleaner Gallery" screen in the admin.

### How do I integrate support for this plugin in a theme?

If you're building a WordPress theme, it's extremely simple to add support for this plugin.  Simply add the following line to your theme's setup function:

	add_theme_support( 'cleaner-gallery' );

Then, copy the plugins `css/gallery.css` file into your theme's stylesheet.  You are free to make any customizations you wish.  It's much more flexible than the default WordPress gallery, so you have a lot of freedom with design choices.

### How do I customize the styles for galleries?

Customizing your galleries can be very simple. Here's some CSS you can add to your theme's `style.css` and modify.

	/* Style rules for the entire gallery */
	.gallery {}
	
		/* Style rules for each item (image and caption) in the gallery */
		.gallery-item {}
		
			/* Style rules for images within the gallery */
			.gallery-icon {}
			
			/* Style rules for captions within the gallery */
			.gallery-caption {}

### Can I paginate galleries?

While the plugin has no way of paginating its own galleries, it's very much possible to make this happen using the WordPress `<!--nextpage-->` tag.

Let's suppose we have 12 images we want to show. The first six will be on the first page while the following six will be on the second page. You'd use this code in the post editor:

	[gallery numberposts="6"]
	
	<!--nextpage-->
	
	[gallery numberposts="6" offset="6"]

== Screenshots ==

A demo can be found on the [Cleaner Gallery page](http://justintadlock.com/archives/2008/04/13/cleaner-wordpress-gallery-plugin) or my [gallery archives](http://justintadlock.com/archives/type/gallery).

1. Gallery from the [Socially Awkward theme](http://wordpress.org/themes/socially-awkward).
2. Gallery from the [Chun theme](http://wordpress.org/themes/chun).
3. Gallery from the [Picturesque theme](http://wordpress.org/themes/picturesque).
4. Cleaner Gallery settings page.

== Changelog ==

### Version 1.1.0 ###

* Introduce the `Cleaner_Gallery` class, which allows for better code reuse and consolidates all the gallery functionality.
* Add support for Schema.org microdata.
* Added the `.gallery-columns-x` class.
* Added the `.gallery-size-x` class.
* Added `aria-describedby` attribute for images with captions for better accessibility.

### Version 1.0.0

* License change from GPL v2-only to GPL v2+.
* Rewrote large chunks of the plugin from the ground up.
* Galleries are now HTML5+, using the `<fig>` and `<figcaption>` tags.
* Dropped the caption link setting because captions in WordPress can now have links within them. Removing the setting was the best way to avoid conflicts.
* Dropped the Cleaner Gallery stylesheet setting. It caused too much confusion for new users.  If you're savvy enough to add styles to your theme, you can handle disabling the stylesheet via the `add_theme_support( 'cleaner-gallery' )` method.
* Much cleaner settings page that now uses the standard Settings API screen design.
* Upgraded to support all newer WordPress gallery arguments that have been added up to version 3.7.

### Version 0.9.1

* Remove the default feature of overwriting the columns if gallery has too few images since users have asked for this.

### Version 0.9

* Important! Users sould re-save their settings after updating to version 0.9.
* Completely overhauled the plugin settings page to use the WordPress settings API.
* Added the option of loading the Cleaner Gallery stylesheet.
* Made the gallery shortcode script modular, allowing it to be ported to other projects.
* Added support for the [prettyPhoto](http://www.no-margin-for-errors.com/projects/prettyphoto-jquery-lightbox-clone) script.
* Plugin now only officially supports WordPress 3.0+.

### Version 0.8

* Important! Users sould re-save their settings after updating to version 0.8.
* Completely recoded the plugin from the ground up for a much needed code overhaul.
* Added support for the [Colorbox](http://colorpowered.com/colorbox) script.
* Added the `cleaner_gallery_image_link_class` filter hook.
* Added the `cleaner_gallery_image_link_rel` filter hook.
* Removed the link class and rel options in the admin for the more robust filter hooks.
* Changed the Image Link option to the Default Image Link option, which allows users to always override this setting on a per-post basis.
* Recognizes custom image sizes created using the `add_image_size()` WordPress function.
* Split the plugin into new files so that specific parts of the code are only loaded when needed.
* Changed the settings page to fully support the screen options and meta box functionality of WordPress.
* Moved translations to the `/languages` folder.
* Added the `offset` argument so that users could more easily make paginated galleries.

### Version 0.7

* Recoded much of the plugin to be more efficient.
* Added the `cleaner_gallery_id()` function to make sure multiple galleries in a single post have different IDs but the same class.
* Added `cleaner_gallery_defaults` filter hook.
* Added `cleaner_gallery_default_settings()` function to better handle user settings.
* Moved Lightbox-type image scripts to an external function from the main script (`cleaner_gallery_link_attributes()`).
* Deprecated CSS functions in favor of using `wp_enqueue_style()`.'
* Dropped support for WordPress 2.5.
* Added support for [jQuery Lightbox](http://github.com/krewenki/jquery-lightbox/tree/master).
* Added support for [LightWindow](http://www.stickmanlabs.com/lightwindow).
* Added support for [FancyZoom](http://www.cabel.name/2008/02/fancyzoom-10.html).
* Added support for [Floatbox](http://randomous.com/floatbox/home).

### Version 0.6.1

* Added `numberposts` parameter.
* Added `exclude` parameter.
* Added `include` parameter.

### Version 0.6

* Cleaned up the code to be more efficient and understandable.
* New options added to the settings page, which include *Default Image Size*, *Default Order*, and *Default Orderby*.
* Added support for [Slimbox 2](http://www.digitalia.be/software/slimbox2).
* Fixed the `[gallery columns="0"]` error.
* Fully documented nearly every function and line of code within the PHP files.

### Version 0.5

* Added in an options panel.
* Support added for Fancybox.