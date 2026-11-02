<?php

/* -----------------------------------------------------------------------------
 * Helper Function
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vw_get_option' ) ) {
	/**
	 * Proxy Function 
	 */
	function vw_get_option( $opt_name, $default = null ) {
		global $Redux_Options;
		return $Redux_Options->get( $opt_name, $default );
	}
}

/* -----------------------------------------------------------------------------
 * Load Custom Fields
 * -------------------------------------------------------------------------- */
require_once 'custom-fields/field_font_upload/field_upload.php';
require_once 'custom-fields/field_typography.php';
require_once 'custom-fields/field_section_info.php';
require_once 'custom-fields/field_sidebar_select.php';
require_once 'custom-fields/field_color_scheme.php';

/* -----------------------------------------------------------------------------
 * Initial Redux Framework
 * -------------------------------------------------------------------------- */

/*
 *
 * Set the text domain for the theme or plugin.
 *
 */
define('Redux_TEXT_DOMAIN', 'redux-opts');

/*
 *
 * Require the framework class before doing anything else, so we can use the defined URLs and directories.
 * If you are running on Windows you may have URL problems which can be fixed by defining the framework url first.
 *
 */

if(!class_exists('Redux_Options')) {
	//define('Redux_OPTIONS_URL', site_url('path the options folder'));
	require_once(dirname(__FILE__) . '/options/defaults.php');
}


/*
 *
 * Most of your editing will be done in this section.
 *
 * Here you can override default values, uncomment args and change their values.
 * No $args are required, but they can be over ridden if needed.
 *
 */
function vw_setup_framework_options() {
	$args = array();

	$args['std_show'] = true; // If true, it shows the std value

	// Set the class for the dev mode tab icon.
	// This is ignored unless $args['icon_type'] = 'iconfont'
	// Default: null
	$args['dev_mode_icon_class'] = 'icon-large';

	// Setup custom links in the footer for share icons
	$args['share_icons']['twitter'] = array(
		'link' => 'http://twitter.com/ghost1227',
		'title' => __('Follow me on Twitter', Redux_TEXT_DOMAIN),
		'img' => Redux_OPTIONS_URL . 'img/social/Twitter.png'
	);
	$args['share_icons']['linked_in'] = array(
		'link' => 'http://www.linkedin.com/profile/view?id=52559281',
		'title' => __('Find me on LinkedIn', Redux_TEXT_DOMAIN),
		'img' => Redux_OPTIONS_URL . 'img/social/LinkedIn.png'
	);

	// Set the class for the import/export tab icon.
	// This is ignored unless $args['icon_type'] = 'iconfont'
	// Default: null
	$args['import_icon_class'] = 'icon-large';

	// Set a custom option name. Don't forget to replace spaces with underscores!
	$args['opt_name'] = 'vw_presso_options';

	// Set a custom title for the options page.
	// Default: Options
	$args['menu_title'] = __('Theme Options', Redux_TEXT_DOMAIN);

	// Set a custom page title for the options page.
	// Default: Options
	$args['page_title'] = __('Theme Options', Redux_TEXT_DOMAIN);

	// Set a custom page slug for options page (wp-admin/themes.php?page=***).
	// Default: redux_options
	$args['page_slug'] = 'redux_options';

	// Set the menu type. Set to "menu" for a top level menu, or "submenu" to add below an existing item.
	// Default: menu
	$args['page_type'] = 'submenu';

	// Set the parent menu.
	// Default: themes.php
	// A list of available parent menus is available at http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
	// $args['page_parent'] = 'options-general.php';
	$args['page_parent'] = 'themes.php';

	// Set the icon type. Set to "iconfont" for Font Awesome, or "image" for traditional.
	// Redux no longer ships with standard icons!
	// Default: iconfont
	//$args['icon_type'] = 'image';
	$args['dev_mode_icon_type'] = 'iconfont';
	$args['import_icon_type'] = 'iconfont';

	$args['intro_text'] = __('<p><i>Theme Version: '.VW_THEME_VERSION.' on Wordpress '.get_bloginfo('version').' and PHP '.phpversion().'</i></p>', Redux_TEXT_DOMAIN );

	$sections = array();

	$sections[] = array(
		'icon_type' => 'iconfont',
		'icon' => 'globe',
		'icon_class' => 'icon-large',
		'title' => __('General', Redux_TEXT_DOMAIN),
		'desc' => __('<p class="description">These are the general options.</p>', Redux_TEXT_DOMAIN),
		'fields' => array(
			array(
				'id' => 'site_layout',
				'type' => 'select',
				'title' => __('Site Layout', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('Choose the site layout.', Redux_TEXT_DOMAIN),
				'std' => 'full-large',
				'options' => array(
					'full-medium' => __('Full-Page (970px Wide)', Redux_TEXT_DOMAIN),
					'full-large' => __('Full-Page (1200px Wide)', Redux_TEXT_DOMAIN),
					'boxed' => __('Boxed', Redux_TEXT_DOMAIN),
				),
			),
			array(
				'id' => 'site_enable_post_box_effects',
				'type' => 'checkbox',
				'title' => __('Enable Fly-in Effects for Post', Redux_TEXT_DOMAIN), 
				'switch' => true,
				'std' => '1' // 1 = checked | 0 = unchecked
			),
			array(
				'id' => 'site_enable_rtl',
				'type' => 'checkbox',
				'title' => __('Enable RTL Supports', Redux_TEXT_DOMAIN), 
				'switch' => true,
				'std' => '0' // 1 = checked | 0 = unchecked
			),
			array(
				'id' => 'site_enable_meta_description',
				'type' => 'checkbox',
				'title' => __('Enable Meta Description', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('You can disable the meta description tag when using the SEO plugin like Yoast', Redux_TEXT_DOMAIN),
				'switch' => true,
				'std' => '1' // 1 = checked | 0 = unchecked
			),
			array(
				'id' => 'site_enable_open_graph',
				'type' => 'checkbox',
				'title' => __('Enable Facebook Open Graph Supports', Redux_TEXT_DOMAIN), 
				'switch' => true,
				'std' => '0' // 1 = checked | 0 = unchecked
			),
			array(
				'id' => 'tracking_code',
				'type' => 'textarea',
				'title' => __('Tracking Code', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('Enter your Google Analytics Code or other tracking code. The code must including <strong>&lt;script&gt;</strong> tag.', Redux_TEXT_DOMAIN),
			),
		)
	);

	$sections[] = array(
		'icon_type' => 'iconfont',
		'icon' => 'pencil',
		'icon_class' => 'icon-large',
		'title' => __('Blog', Redux_TEXT_DOMAIN),
		'desc' => __('<p class="description">These are the options for the blog.</p>', Redux_TEXT_DOMAIN),
		'fields' => array(
			array(
				'id' => 'blog_excerpt_length',
				'type' => 'text',
				'title' => __('Blog Excerpt Length', Redux_TEXT_DOMAIN),
				'sub_desc' => __('Used for blog page, archives & search results.', Redux_TEXT_DOMAIN),
				'validate' => 'numeric',
				'std' => '25'
			),
			array(
				'id' => 'blog_layout',
				'type' => 'select',
				'title' => __('Blog Layout', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('Choose the layout for blog page.', Redux_TEXT_DOMAIN),
				'std' => 'large-thumbnail',
				'options' => array(
					'large-thumbnail' => __('Large Thumbnail', Redux_TEXT_DOMAIN),
					'classic' => __('Classic', Redux_TEXT_DOMAIN),
				),
			),
			array(
				'id' => 'blog_sidebar',
				'type' => 'sidebar_select',
				'title' => __('Default Sidebar for Blog', Redux_TEXT_DOMAIN),
				'sub_desc' => __('Used for blog page, archives & search results.', Redux_TEXT_DOMAIN),
				'std' => 'blog-sidebar'
			),
			array(
				'id' => 'archive_enable_post_slider',
				'type' => 'checkbox',
				'title' => __('Enable Posts Slider on Archive Page', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('All posts marked as featured will be shown in post slider.', Redux_TEXT_DOMAIN),
				'switch' => true,
				'std' => '1' // 1 = checked | 0 = unchecked
			),
			array(
				'id' => 'blog_show_featured_image_single_post',
				'type' => 'checkbox',
				'title' => __('Show Featured Image', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('Show/hide the featured image for standard post type on single-post page.', Redux_TEXT_DOMAIN),
				'switch' => true,
				'std' => '1' // 1 = checked | 0 = unchecked
			),
			array(
				'id' => 'blog_show_video_player_for_thumbnail',
				'type' => 'checkbox',
				'title' => __('Show Video Player on Featured Image', Redux_TEXT_DOMAIN), 
				'switch' => true,
				'std' => '1' // 1 = checked | 0 = unchecked
			),

			array(
				'id' => 'info_demo',
				'type' => 'section_info',
				'desc' => __('Blog Post', Redux_TEXT_DOMAIN)
			),
			array(
				'id' => 'blog_show_posts_at_top',
				'type' => 'select',
				'title' => __('Show Posts At The Top', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('Choose the way to show the posts at the top of single post page.', Redux_TEXT_DOMAIN),
				'std' => 'latest',
				'options' => array(
					'hidden' => __('Not Shown', Redux_TEXT_DOMAIN),
					'latest' => __('Show Latest Posts', Redux_TEXT_DOMAIN),
					'random' => __('Show Random Posts', Redux_TEXT_DOMAIN),
				),
			),
			array(
				'id' => 'blog_enable_author_info',
				'type' => 'checkbox',
				'title' => __('Enable Author Info', Redux_TEXT_DOMAIN), 
				'switch' => true,
				'std' => '1' // 1 = checked | 0 = unchecked
			),
			array(
				'id' => 'blog_enable_related_post',
				'type' => 'checkbox',
				'title' => __('Enable Related Post', Redux_TEXT_DOMAIN), 
				'switch' => true,
				'std' => '1' // 1 = checked | 0 = unchecked
			),
			

			array(
				'id' => 'info_demo',
				'type' => 'section_info',
				'desc' => __('Custom Wordpress Gallery', Redux_TEXT_DOMAIN)
			),

			array(
				'id' => 'blog_enable_custom_gallery',
				'type' => 'checkbox',
				'title' => __('Enable Custom Wordpress Gallery', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('Turn it off if you need to use the Jetpack Carousel or other gallery plugins.', Redux_TEXT_DOMAIN),
				'switch' => true,
				'std' => '1' // 1 = checked | 0 = unchecked
			),

			array(
				'id' => 'blog_custom_gallery_layout',
				'type' => 'text',
				'title' => __('Gallery Layout', Redux_TEXT_DOMAIN),
				'sub_desc' => __('A numbers representing the number of columns for each row. Example, "213" is the 1st row has 2 images, 2nd row has 1 image, 3rd row has 3 images.', Redux_TEXT_DOMAIN),
				'validate' => 'numeric',
				'std' => '213'
			),

			array(
				'id' => 'info_demo',
				'type' => 'section_info',
				'desc' => __('Share-Box', Redux_TEXT_DOMAIN)
			),
			array(
				'id' => 'blog_enable_sharebox',
				'type' => 'checkbox',
				'title' => __('Enable Share-Box', Redux_TEXT_DOMAIN), 
				'switch' => true,
				'std' => '1' // 1 = checked | 0 = unchecked
			),
			array(
				'id' => 'sharebox_delicious',
				'type' => 'checkbox',
				'title' => __('Enable Share To Delicious', Redux_TEXT_DOMAIN), 
				'switch' => true,
				'std' => '0' // 1 = checked | 0 = unchecked
			),
			array(
				'id' => 'sharebox_digg',
				'type' => 'checkbox',
				'title' => __('Enable Share To Digg', Redux_TEXT_DOMAIN), 
				'switch' => true,
				'std' => '0' // 1 = checked | 0 = unchecked
			),
			array(
				'id' => 'sharebox_email',
				'type' => 'checkbox',
				'title' => __('Enable Share To Email', Redux_TEXT_DOMAIN), 
				'switch' => true,
				'std' => '0' // 1 = checked | 0 = unchecked
			),
			array(
				'id' => 'sharebox_facebook',
				'type' => 'checkbox',
				'title' => __('Enable Share To Facebook', Redux_TEXT_DOMAIN), 
				'switch' => true,
				'std' => '1' // 1 = checked | 0 = unchecked
			),
			array(
				'id' => 'sharebox_googleplus',
				'type' => 'checkbox',
				'title' => __('Enable Share To Google+', Redux_TEXT_DOMAIN), 
				'switch' => true,
				'std' => '1' // 1 = checked | 0 = unchecked
			),
			array(
				'id' => 'sharebox_linkedin',
				'type' => 'checkbox',
				'title' => __('Enable Share To LinkedIn', Redux_TEXT_DOMAIN), 
				'switch' => true,
				'std' => '0' // 1 = checked | 0 = unchecked
			),
			array(
				'id' => 'sharebox_pinterest',
				'type' => 'checkbox',
				'title' => __('Enable Share To Pinterest', Redux_TEXT_DOMAIN), 
				'switch' => true,
				'std' => '1' // 1 = checked | 0 = unchecked
			),
			array(
				'id' => 'sharebox_reddit',
				'type' => 'checkbox',
				'title' => __('Enable Share To Reddit', Redux_TEXT_DOMAIN), 
				'switch' => true,
				'std' => '0' // 1 = checked | 0 = unchecked
			),
			array(
				'id' => 'sharebox_tumblr',
				'type' => 'checkbox',
				'title' => __('Enable Share To Tumblr', Redux_TEXT_DOMAIN), 
				'switch' => true,
				'std' => '0' // 1 = checked | 0 = unchecked
			),
			array(
				'id' => 'sharebox_twitter',
				'type' => 'checkbox',
				'title' => __('Enable Share To Twitter', Redux_TEXT_DOMAIN), 
				'switch' => true,
				'std' => '1' // 1 = checked | 0 = unchecked
			),
		)
	);

	$sections[] = array(
		'icon_type' => 'iconfont',
		'icon' => 'group',
		'icon_class' => 'icon-large',
		'title' => __('Social Media', Redux_TEXT_DOMAIN),
		'desc' => __('<p class="description">These are options for setting up the site’s social media.</p>', Redux_TEXT_DOMAIN),
		'fields' => array(
			array(
				'id' => 'social_delicious',
				'type' => 'text',
				'title' => __('Delicious URL', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('The URL to your account page', Redux_TEXT_DOMAIN),
			),
			array(
				'id' => 'social_digg',
				'type' => 'text',
				'title' => __('Digg URL', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('The URL to your account page', Redux_TEXT_DOMAIN),
			),
			array(
				'id' => 'social_dribbble',
				'type' => 'text',
				'title' => __('Dribbble URL', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('The URL to your account page', Redux_TEXT_DOMAIN),
			),
			array(
				'id' => 'social_facebook',
				'type' => 'text',
				'title' => __('Facebook URL', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('The URL to your account page', Redux_TEXT_DOMAIN),
				'std' => 'https://facebook.com',
			),
			array(
				'id' => 'social_flickr',
				'type' => 'text',
				'title' => __('Flickr URL', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('The URL to your account page', Redux_TEXT_DOMAIN),
			),
			array(
				'id' => 'social_forrst',
				'type' => 'text',
				'title' => __('Forrst URL', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('The URL to your account page', Redux_TEXT_DOMAIN),
			),
			array(
				'id' => 'social_github',
				'type' => 'text',
				'title' => __('Github URL', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('The URL to your account page', Redux_TEXT_DOMAIN),
			),
			array(
				'id' => 'social_googleplus',
				'type' => 'text',
				'title' => __('Google+ URL', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('The URL to your account page', Redux_TEXT_DOMAIN),
			),
			array(
				'id' => 'social_instagram',
				'type' => 'text',
				'title' => __('Instagram URL', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('The URL to your account page', Redux_TEXT_DOMAIN),
			),
			array(
				'id' => 'social_linkedin',
				'type' => 'text',
				'title' => __('Linkedin URL', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('The URL to your account page', Redux_TEXT_DOMAIN),
			),
			array(
				'id' => 'social_pinterest',
				'type' => 'text',
				'title' => __('Pinterest URL', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('The URL to your account page', Redux_TEXT_DOMAIN),
			),
			array(
				'id' => 'social_rss',
				'type' => 'text',
				'title' => __('Rss URL', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('The URL to your account page', Redux_TEXT_DOMAIN),
			),
			array(
				'id' => 'social_skype',
				'type' => 'text',
				'title' => __('Skype URL', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('The URL to your account page', Redux_TEXT_DOMAIN),
			),
			array(
				'id' => 'social_tumblr',
				'type' => 'text',
				'title' => __('Tumblr URL', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('The URL to your account page', Redux_TEXT_DOMAIN),
			),
			array(
				'id' => 'social_twitter',
				'type' => 'text',
				'title' => __('Twitter URL', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('The URL to your account page', Redux_TEXT_DOMAIN),
				'std' => 'https://twitter.com',
			),
			array(
				'id' => 'social_vimeo',
				'type' => 'text',
				'title' => __('Vimeo URL', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('The URL to your account page', Redux_TEXT_DOMAIN),
			),
			array(
				'id' => 'social_yahoo',
				'type' => 'text',
				'title' => __('Yahoo URL', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('The URL to your account page', Redux_TEXT_DOMAIN),
			),
			array(
				'id' => 'social_youtube',
				'type' => 'text',
				'title' => __('Youtube URL', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('The URL to your account page', Redux_TEXT_DOMAIN),
			),
			array(
				'id' => 'social_vk',
				'type' => 'text',
				'title' => __('Vkontakte URL', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('The URL to your account page', Redux_TEXT_DOMAIN),
			),
		array(
				'id' => 'social_ok',
				'type' => 'text',
				'title' => __('Odnoklassniki URL', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('The URL to your account page', Redux_TEXT_DOMAIN),
			),
		)
	);

	$sections[] = array(
		'icon_type' => 'iconfont',
		'icon' => 'rocket',
		'icon_class' => 'icon-large',
		'title' => __('Font Icons', Redux_TEXT_DOMAIN),
		'desc' => __('<p class="description">You can choose additional icon fonts. The default font icons that are already in use are <a href="http://entypo.com">Entypo</a> (Icon listing <a href="'.get_template_directory_uri().'/framework/font-icons/entypo/demo.html">here</a>) and <a href="http://zocial.smcllns.com">Zocial</a> (Icon listing <a href="'.get_template_directory_uri().'/framework/font-icons/social-icons/demo.html">here</a>). </p>', Redux_TEXT_DOMAIN),
		'fields' => array(
			array(
				'id' => 'icon_elusive',
				'type' => 'checkbox',
				'title' => __('Include Elusive Icons', Redux_TEXT_DOMAIN), 
				'sub_desc' => 'by <a href="http://aristeides.com">Aristeides Stathopoulos</a>, The icon listing is <a href="'.get_template_directory_uri().'/framework/font-icons/elusive/demo.html">here</a>',
				'switch' => true,
				'std' => '0' // 1 = checked | 0 = unchecked
			),
			array(
				'id' => 'icon_awesome',
				'type' => 'checkbox',
				'title' => __('Include Font Awesome Icons', Redux_TEXT_DOMAIN), 
				'sub_desc' => 'by <a href="http://fontawesome.io">Dav Gandy</a>, The icon listing is <a href="'.get_template_directory_uri().'/framework/font-icons/awesome/demo.html">here</a>',
				'switch' => true,
				'std' => '0' // 1 = checked | 0 = unchecked
			),
			array(
				'id' => 'icon_iconic',
				'type' => 'checkbox',
				'title' => __('Include Iconic Icons', Redux_TEXT_DOMAIN), 
				'sub_desc' => 'by <a href="http://somerandomdude.com/work/iconic">P.J. Onori</a>, The icon listing is <a href="'.get_template_directory_uri().'/framework/font-icons/iconic/demo.html">here</a>',
				'switch' => true,
				'std' => '0' // 1 = checked | 0 = unchecked
			),
			array(
				'id' => 'icon_typicons',
				'type' => 'checkbox',
				'title' => __('Include Typicons Icons', Redux_TEXT_DOMAIN), 
				'sub_desc' => 'by <a href="http://typicons.com">Stephen Hutchings</a>, The icon listing is <a href="'.get_template_directory_uri().'/framework/font-icons/typicons/demo.html">here</a>',
				'switch' => true,
				'std' => '0' // 1 = checked | 0 = unchecked
			),
		)
	);

	$sections[] = array(
		'icon_type' => 'iconfont',
		'icon' => 'columns',
		'icon_class' => 'icon-large',
		'title' => __('Logo', Redux_TEXT_DOMAIN),
		'desc' => __('<p class="description">These are options for the logo.</p>', Redux_TEXT_DOMAIN),
		'fields' => array(
			array(
				'id' => 'logo_url',
				'type' => 'upload',
				'title' => __('Origial Logo', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('Upload the original logo', Redux_TEXT_DOMAIN),
			),
			array(
				'id' => 'logo_2x_width',
				'type' => 'text',
				'validate' => 'numeric',
				'title' => __('Width Of Original Logo', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('If Retina Logo is NOT uploaded, please specify the width of the Original Logo', Redux_TEXT_DOMAIN),
				'desc' => 'px',
			),
			array(
				'id' => 'logo_2x_height',
				'type' => 'text',
				'validate' => 'numeric',
				'title' => __('Height Of Original Logo', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('If Retina Logo is NOT uploaded, please specify the height of the Original Logo', Redux_TEXT_DOMAIN),
				'desc' => 'px',
			),
			array(
				'id' => 'logo_2x_url',
				'type' => 'upload',
				'title' => __('Retina Logo', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('Logo for high resolution device.', Redux_TEXT_DOMAIN),
			),
			
		)
	);

	$sections[] = array(
		'icon_type' => 'iconfont',
		'icon' => 'columns',
		'icon_class' => 'icon-large',
		'title' => __('Header', Redux_TEXT_DOMAIN),
		'desc' => __('<p class="description">These are options for header.</p>', Redux_TEXT_DOMAIN),
		'fields' => array(
			array(
				'id' => 'header_layout',
				'type' => 'select',
				'title' => __('Header Layout', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('Choose the layout of header', Redux_TEXT_DOMAIN),
				'std' => 'left-logo',
				'options' => array(
					'left-logo' => __('Left Logo', Redux_TEXT_DOMAIN),
					'center-logo' => __('Center Logo', Redux_TEXT_DOMAIN),
				),
			),
			array(
				'id' => 'header_ads_code',
				'type' => 'textarea',
				'title' => __('Header Ads Code', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('Enter your Advertising Code The code must including <strong>&lt;script&gt;</strong> tag.', Redux_TEXT_DOMAIN),
			),
		)
	);

	$sections[] = array(
		'icon_type' => 'iconfont',
		'icon' => 'columns',
		'icon_class' => 'icon-large',
		'title' => __('Footer', Redux_TEXT_DOMAIN),
		'desc' => __('<p class="description">These are options for footer.</p>', Redux_TEXT_DOMAIN),
		'fields' => array(
			array(
				'id' => 'copyright_text',
				'type' => 'textarea',
				'title' => __('Copyright Text', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('Enter your copyright text. HTML and shortcode is allowed.', Redux_TEXT_DOMAIN),
				'std' => 'Copyright &copy; 2014. All rights reserved.',
			),
		)
	);

	$sections[] = array(
		'icon_type' => 'iconfont',
		'icon' => 'star',
		'icon_class' => 'icon-large',
		'title' => __('Favicon', Redux_TEXT_DOMAIN),
		'desc' => __('<p class="description">These are options for favicons.</p>', Redux_TEXT_DOMAIN),
		'fields' => array(
			
			array(
				'id' => 'fav_icon_url',
				'type' => 'upload',
				'title' => __('Favicon (16x16)', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('Default Favicon', Redux_TEXT_DOMAIN),
			),
			array(
				'id' => 'fav_icon_iphone_url',
				'type' => 'upload',
				'title' => __('Apple iPhone Icon (57x57)', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('Icon for Classic iphone', Redux_TEXT_DOMAIN),
			),
			array(
				'id' => 'fav_icon_iphone_retina_url',
				'type' => 'upload',
				'title' => __('Apple iPhone Retina Icon (114x114)', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('Icon for Retina iPhone', Redux_TEXT_DOMAIN),
			),
			array(
				'id' => 'fav_icon_ipad_url',
				'type' => 'upload',
				'title' => __('Apple iPad Icon (72x72)', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('Icon for Classic iPad', Redux_TEXT_DOMAIN),
			),
			array(
				'id' => 'fav_icon_ipad_retina_url',
				'type' => 'upload',
				'title' => __('Apple iPad Retina Icon (144x144)', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('Icon for Retina iPad', Redux_TEXT_DOMAIN),
			),
		)
	);

	$sections[] = array(
		'icon_type' => 'iconfont',
		'icon' => 'font',
		'icon_class' => 'icon-large',
		'title' => __('Typography', Redux_TEXT_DOMAIN),
		'desc' => __('<p class="description">These are options for text style.</p>', Redux_TEXT_DOMAIN),
		'fields' => array(
			array(
				'id' => 'h1',
				'type' => 'typography',
				'title' => __('Heading Text', Redux_TEXT_DOMAIN),
				'size' => false,
				'std' => array(
					'size' => '32px',
					'family' => 'Oswald',
					'weight' => '700',
					'color' => '#333333',
				)
			),
			
			array(
				'id' => 'body',
				'type' => 'typography',
				'title' => __('Body Text', Redux_TEXT_DOMAIN), 
				'std' => array(
					'size' => '14px',
					'family' => 'Open Sans',
					'weight' => '400',
					'color' => '#666666',
				)
			),
			array(
				'id' => 'info_demo',
				'type' => 'section_info',
				'desc' => __('Custom Font 1', Redux_TEXT_DOMAIN),
			),
			array(
				'id' => 'custom_font1_ttf',
				'type' => 'font_upload',
				'title' => __('.TTF Font File', Redux_TEXT_DOMAIN),
				'std' => '',
			),
			array(
				'id' => 'custom_font1_woff',
				'type' => 'font_upload',
				'title' => __('.WOFF Font File', Redux_TEXT_DOMAIN),
				'std' => '',
			),
			array(
				'id' => 'custom_font1_svg',
				'type' => 'font_upload',
				'title' => __('.SVG Font File', Redux_TEXT_DOMAIN),
				'std' => '',
			),
			array(
				'id' => 'custom_font1_eot',
				'type' => 'font_upload',
				'title' => __('.EOT Font File', Redux_TEXT_DOMAIN),
				'std' => '',
			),
			array(
				'id' => 'info_demo',
				'type' => 'section_info',
				'desc' => __('Custom Font 2', Redux_TEXT_DOMAIN)
			),
			array(
				'id' => 'custom_font2_ttf',
				'type' => 'font_upload',
				'title' => __('.TTF Font File', Redux_TEXT_DOMAIN),
				'std' => '',
			),
			array(
				'id' => 'custom_font2_woff',
				'type' => 'font_upload',
				'title' => __('.WOFF Font File', Redux_TEXT_DOMAIN),
				'std' => '',
			),
			array(
				'id' => 'custom_font2_svg',
				'type' => 'font_upload',
				'title' => __('.SVG Font File', Redux_TEXT_DOMAIN),
				'std' => '',
			),
			array(
				'id' => 'custom_font2_eot',
				'type' => 'font_upload',
				'title' => __('.EOT Font File', Redux_TEXT_DOMAIN),
				'std' => '',
			),
		)
	);

	$sections[] = array(
		'icon_type' => 'iconfont',
		'icon' => 'picture',
		'icon_class' => 'icon-large',
		'title' => __('Gallery Slider', Redux_TEXT_DOMAIN),
		'desc' => __('<p class="description">These are the options for the image gallery slider that is displayed in the blog entry.</p>', Redux_TEXT_DOMAIN),
		'fields' => array(
			array(
				'id' => 'flexslider_slideshow',
				'type' => 'checkbox',
				'title' => __('Automatic Start', Redux_TEXT_DOMAIN), 
				'switch' => true,
				'std' => '1' // 1 = checked | 0 = unchecked
			),
			array(
				'id' => 'flexslider_randomize',
				'type' => 'checkbox',
				'title' => __('Random Order', Redux_TEXT_DOMAIN), 
				'switch' => true,
				'std' => '0' // 1 = checked | 0 = unchecked
			),
			array(
				'id' => 'flexslider_pauseonhover',
				'type' => 'checkbox',
				'title' => __('Pause On Hover', Redux_TEXT_DOMAIN), 
				'switch' => true,
				'std' => '1' // 1 = checked | 0 = unchecked
			),
			array(
				'id' => 'flexslider_transition_section',
				'type' => 'section_info',
				'desc' => __('Transition', Redux_TEXT_DOMAIN)
			),
			array(
				'id' => 'flexslider_animation',
				'type' => 'select',
				'title' => __('Animation', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('Choose the animation style', Redux_TEXT_DOMAIN),
				'std' => 'fade',
				'options' => array(
					'fade' => __('Fade', Redux_TEXT_DOMAIN),
					'slide' => __('Slide', Redux_TEXT_DOMAIN),
				),
			),
			array(
				'id' => 'flexslider_easing',
				'type' => 'select',
				'title' => __('Easing', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('Choose the easing of transition', Redux_TEXT_DOMAIN),
				'std' => 'easeInCirc',
				'options' => array(
					'linear' => __('Linear', Redux_TEXT_DOMAIN),
					'easeInSine' => __('Ease In Sine', Redux_TEXT_DOMAIN),
					'easeOutSine' => __('Ease Out Sine', Redux_TEXT_DOMAIN),
					'easeInOutSine' => __('Ease In-Out Sine', Redux_TEXT_DOMAIN),
					'easeInQuad' => __('Ease In Quad', Redux_TEXT_DOMAIN),
					'easeOutQuad' => __('Ease Out Quad', Redux_TEXT_DOMAIN),
					'easeInOutQuad' => __('Ease In-Out Quad', Redux_TEXT_DOMAIN),
					'easeInCubic' => __('Ease In Cubic', Redux_TEXT_DOMAIN),
					'easeOutCubic' => __('Ease Out Cubic', Redux_TEXT_DOMAIN),
					'easeInOutCubic' => __('Ease In-Out Cubic', Redux_TEXT_DOMAIN),
					'easeInQuart' => __('Ease In Quart', Redux_TEXT_DOMAIN),
					'easeOutQuart' => __('Ease Out Quart', Redux_TEXT_DOMAIN),
					'easeInOutQuart' => __('Ease In-Out Quart', Redux_TEXT_DOMAIN),
					'easeInQuint' => __('Ease In Quint', Redux_TEXT_DOMAIN),
					'easeOutQuint' => __('Ease Out Quint', Redux_TEXT_DOMAIN),
					'easeInOutQuint' => __('Ease In-Out Quint', Redux_TEXT_DOMAIN),
					'easeInExpo' => __('Ease In Expo', Redux_TEXT_DOMAIN),
					'easeOutExpo' => __('Ease Out Expo', Redux_TEXT_DOMAIN),
					'easeInOutExpo' => __('Ease In-Out Expo', Redux_TEXT_DOMAIN),
					'easeInCirc' => __('Ease In Circ', Redux_TEXT_DOMAIN),
					'easeOutCirc' => __('Ease Out Circ', Redux_TEXT_DOMAIN),
					'easeInOutCirc' => __('Ease In-Out Circ', Redux_TEXT_DOMAIN),
					'easeInBack' => __('Ease In Back', Redux_TEXT_DOMAIN),
					'easeOutBack' => __('Ease Out Back', Redux_TEXT_DOMAIN),
					'easeInOutBack' => __('Ease In-Out Back', Redux_TEXT_DOMAIN),
					'easeInElastic' => __('Ease In Elastic', Redux_TEXT_DOMAIN),
					'easeOutElastic' => __('Ease Out Elastic', Redux_TEXT_DOMAIN),
					'easeInOutElastic' => __('Ease In-Out Elastic', Redux_TEXT_DOMAIN),
					'easeInBounce' => __('Ease In Bounce', Redux_TEXT_DOMAIN),
					'easeOutBounce' => __('Ease Out Bounce', Redux_TEXT_DOMAIN),
					'easeInOutBounce' => __('Ease In-Out Bounce', Redux_TEXT_DOMAIN),
				),
			),
			array(
				'id' => 'flexslider_slideshowspeed',
				'type' => 'text',
				'title' => __('Slideshow Duration', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('The time for showing slide, in milliseconds', Redux_TEXT_DOMAIN),
				'validate' => 'numeric',
				'std' => '4000',
			),
			array(
				'id' => 'flexslider_animationspeed',
				'type' => 'text',
				'title' => __('Animation Speed', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('The time for transition, in milliseconds', Redux_TEXT_DOMAIN),
				'validate' => 'numeric',
				'std' => '600',
			),
		)
	);

	$sections[] = array(
		'icon_type' => 'iconfont',
		'icon' => 'tint',
		'icon_class' => 'icon-large',
		'title' => __('Colors', Redux_TEXT_DOMAIN),
		'desc' => __('<p class="description">These are options for theme color.</p>', Redux_TEXT_DOMAIN),
		'fields' => array(
			array(
				'id' => 'color_primary',
				'type' => 'color',
				'title' => __('Primary Color', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('Font color for links, buttons, hilight area, etc', Redux_TEXT_DOMAIN),
				'std' => '#3facd6',
			),

			array(
				'id' => 'info',
				'type' => 'section_info',
				'desc' => __('Top-Bar', Redux_TEXT_DOMAIN)
			),
			array(
				'id' => 'color_topbar_bg',
				'type' => 'color',
				'title' => __('Background Color', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('Default backgroud color', Redux_TEXT_DOMAIN),
				'std' => '#333333',
			),
			array(
				'id' => 'color_topbar_font',
				'type' => 'color',
				'title' => __('Font Color', Redux_TEXT_DOMAIN), 
				'std' => '#eeeeee',
			),
			array(
				'id' => 'color_topbar_highlight',
				'type' => 'color',
				'title' => __('Hightlight Color', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('Hightlight color for link and button hover, etc', Redux_TEXT_DOMAIN),
				'std' => '#3facd6',
			),
			array(
				'id' => 'color_topbar_highlight_font',
				'type' => 'color',
				'title' => __('Hightlight Font Color', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('Hightlight font color for link and button hover, etc', Redux_TEXT_DOMAIN),
				'std' => '#ffffff',
			),

			array(
				'id' => 'info',
				'type' => 'section_info',
				'desc' => __('Header', Redux_TEXT_DOMAIN)
			),
			array(
				'id' => 'color_header_bg',
				'type' => 'color',
				'title' => __('Background Color', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('Default backgroud color', Redux_TEXT_DOMAIN),
				'std' => '#f9f9f9',
			),
			array(
				'id' => 'color_header_font',
				'type' => 'color',
				'title' => __('Font Color', Redux_TEXT_DOMAIN), 
				'std' => '#bbbbbb',
			),

			array(
				'id' => 'info',
				'type' => 'section_info',
				'desc' => __('Main Navigation', Redux_TEXT_DOMAIN)
			),
			array(
				'id' => 'color_nav_bg',
				'type' => 'color',
				'title' => __('Background Color', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('Default backgroud color', Redux_TEXT_DOMAIN),
				'std' => '#333333',
			),			
			array(
				'id' => 'color_nav_font',
				'type' => 'color',
				'title' => __('Font Color', Redux_TEXT_DOMAIN), 
				'std' => '#ffffff',
			),
			array(
				'id' => 'color_nav_highlight',
				'type' => 'color',
				'title' => __('Hightlight Color', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('Hightlight color for link and button hover, etc', Redux_TEXT_DOMAIN),
				'std' => '#3facd6',
			),
			array(
				'id' => 'color_nav_highlight_font',
				'type' => 'color',
				'title' => __('Hightlight Font Color', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('Hightlight font color for link and button hover, etc', Redux_TEXT_DOMAIN),
				'std' => '#ffffff',
			),
			
			array(
				'id' => 'info',
				'type' => 'section_info',
				'desc' => __('Footer', Redux_TEXT_DOMAIN)
			),
			array(
				'id' => 'color_footer_bg',
				'type' => 'color',
				'title' => __('Background Color', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('Default backgroud color', Redux_TEXT_DOMAIN),
				'std' => '#111111',
			),
			array(
				'id' => 'color_footer_font',
				'type' => 'color',
				'title' => __('Font Color', Redux_TEXT_DOMAIN), 
				'std' => '#999999',
			),
			array(
				'id' => 'color_footer_heading',
				'type' => 'color',
				'title' => __('Heading Font Color', Redux_TEXT_DOMAIN), 
				'std' => '#222222',
			),

			array(
				'id' => 'info',
				'type' => 'section_info',
				'desc' => __('Copyright Bar', Redux_TEXT_DOMAIN)
			),
			array(
				'id' => 'color_copyright_bg',
				'type' => 'color',
				'title' => __('Background Color', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('Default backgroud color', Redux_TEXT_DOMAIN),
				'std' => '#000000',
			),
			array(
				'id' => 'color_copyright_font',
				'type' => 'color',
				'title' => __('Font Color', Redux_TEXT_DOMAIN), 
				'std' => '#dddddd',
			),
		)
	);

	$sections[] = array(
		'icon_type' => 'iconfont',
		'icon' => 'css3',
		'icon_class' => 'icon-large',
		'title' => __('bbPress', Redux_TEXT_DOMAIN),
		'desc' => __('<p class="description">These are options for bbPress.</p>', Redux_TEXT_DOMAIN),
		'fields' => array(
			array(
				'id' => 'bbpress_enable_sidebar',
				'type' => 'checkbox',
				'title' => __('Enable Sidebar', Redux_TEXT_DOMAIN), 
				'switch' => true,
				'std' => '0' // 1 = checked | 0 = unchecked
			),
			array(
				'id' => 'bbpress_sidebar',
				'type' => 'sidebar_select',
				'title' => __('Default Sidebar for bbPress', Redux_TEXT_DOMAIN),
				'std' => 'blog-sidebar'
			),
		)
	);

	$sections[] = array(
		'icon_type' => 'iconfont',
		'icon' => 'css3',
		'icon_class' => 'icon-large',
		'title' => __('Custom JS / CSS', Redux_TEXT_DOMAIN),
		'desc' => __('<p class="description">These are options for customizing the javascript and css without editing the files.</p>', Redux_TEXT_DOMAIN),
		'fields' => array(
			array(
				'id' => 'custom_css',
				'type' => 'textarea',
				'title' => __('Custom CSS', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('Add your CSS', Redux_TEXT_DOMAIN),
				'std' => '',
			),
			array(
				'id' => 'custom_js',
				'type' => 'textarea',
				'title' => __('Custom JS', Redux_TEXT_DOMAIN), 
				'sub_desc' => __('Add your JS', Redux_TEXT_DOMAIN),
				'std' => '',
			),
		)
	);
				
	$tabs = array();

	// if (function_exists('wp_get_theme')){
		$theme_data = wp_get_theme();
		$item_uri = $theme_data->get('ThemeURI');
		$description = $theme_data->get('Description');
		$author = $theme_data->get('Author');
		$author_uri = $theme_data->get('AuthorURI');
		$version = $theme_data->get('Version');
		$tags = $theme_data->get('Tags');
	// }else{
	// 	/* In case old WP */ $theme_data = get_theme_data(trailingslashit(get_stylesheet_directory()) . 'style.css');
	// 	$item_uri = $theme_data['URI'];
	// 	$description = $theme_data['Description'];
	// 	$author = $theme_data['Author'];
	// 	$author_uri = $theme_data['AuthorURI'];
	// 	$version = $theme_data['Version'];
	// 	$tags = $theme_data['Tags'];
	//  }
	
	$item_info = '<div class="redux-opts-section-desc">';
	$item_info .= '<p class="redux-opts-item-data description item-uri">' . __('<strong>Theme URL:</strong> ', Redux_TEXT_DOMAIN) . '<a href="' . esc_url( $item_uri ) . '" target="_blank">' . $item_uri . '</a></p>';
	$item_info .= '<p class="redux-opts-item-data description item-author">' . __('<strong>Author:</strong> ', Redux_TEXT_DOMAIN) . ($author_uri ? '<a href="' . esc_url( $author_uri ) . '" target="_blank">' . $author . '</a>' : $author) . '</p>';
	$item_info .= '<p class="redux-opts-item-data description item-version">' . __('<strong>Version:</strong> ', Redux_TEXT_DOMAIN) . $version . '</p>';
	$item_info .= '<p class="redux-opts-item-data description item-description">' . $description . '</p>';
	$item_info .= '<p class="redux-opts-item-data description item-tags">' . __('<strong>Tags:</strong> ', Redux_TEXT_DOMAIN) . implode(', ', $tags) . '</p>';
	$item_info .= '</div>';

	$tabs['item_info'] = array(
		'icon' => 'info-sign',
		'icon_class' => 'icon-large',
		'title' => __('Theme Information', Redux_TEXT_DOMAIN),
		'content' => $item_info
	);

	global $Redux_Options;
	$Redux_Options = new Redux_Options($sections, $args, $tabs);
}
add_action('init', 'vw_setup_framework_options', 0);