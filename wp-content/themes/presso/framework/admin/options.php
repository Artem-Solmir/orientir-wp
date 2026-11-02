<?php

if ( ! defined( 'VW_CONST_REDUX_ASSET_URL' ) ) define( 'VW_CONST_REDUX_ASSET_URL', get_template_directory_uri() . '/img/admin' );

/* -----------------------------------------------------------------------------
 * Theme Option Proxy
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vw_get_option' ) ) {
	function vw_get_option( $opt_name, $default = null ) {
		global $vw_presso;
		if ( isset( $vw_presso[ $opt_name ] ) ) return $vw_presso[ $opt_name ];
		else return $default;
	}
}

/* -----------------------------------------------------------------------------
 * Prepare Options
 * -------------------------------------------------------------------------- */
$theme = wp_get_theme();
$vw_opt_name = 'vw_presso';

$help_html = '<a href="http://envirra.com/themes/presso/document/" target="_blank"><img src="'.VW_CONST_REDUX_ASSET_URL.'/help-documentation.png"></a>';
$help_html .= '<a href="http://envirra.com/themes/presso/document/#troubleshooting" target="_blank"><img src="'.VW_CONST_REDUX_ASSET_URL.'/help-troubleshooting.png"></a>';
$help_html .= '<a href="http://themeforest.net/user/envirra/portfolio?ref=envirra" target="_blank"><img src="'.VW_CONST_REDUX_ASSET_URL.'/help-more-themes.png"></a>';

$args = array(
	// TYPICAL -> Change these values as you need/desire
	'opt_name'             => $vw_opt_name,
	// This is where your data is stored in the database and also becomes your global variable name.
	'display_name'         => $theme->get( 'Name' ),
	// Name that appears at the top of your panel
	'display_version'      => $theme->get( 'Version' ),
	// Version that appears at the top of your panel
	'menu_type'            => 'menu',
	//Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
	'allow_sub_menu'       => true,
	// Show the sections below the admin menu item or not
	'menu_title'           => 'Theme Options',
	'page_title'           => 'Theme Options',
	// You will need to generate a Google API key to use this feature.
	// Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
	'google_api_key'       => 'AIzaSyCNyDK8sPUuf9bTcG1TdFFLAVUfA1IDm38',
	// Set it you want google fonts to update weekly. A google_api_key value is required.
	'google_update_weekly' => false,
	// Must be defined to add google fonts to the typography module
	'async_typography'     => VW_CONST_ASYNC_TYPOGRAPHY,
	// Use a asynchronous font on the front end or font string
	//'disable_google_fonts_link' => true,                    // Disable this in case you want to create your own google fonts loader
	'admin_bar'            => true,
	// Show the panel pages on the admin bar
	'admin_bar_icon'       => 'dashicons-portfolio',
	// Choose an icon for the admin bar menu
	'admin_bar_priority'   => 50,
	// Choose an priority for the admin bar menu
	'global_variable'      => '',
	// Set a different name for your global variable other than the opt_name
	'dev_mode'             => false,
	// Show the time the page took to load, etc
	'update_notice'        => false,
	// If dev_mode is enabled, will notify developer of updated versions available in the GitHub Repo
	'customizer'           => true,
	// Enable basic customizer support
	//'open_expanded'     => true,                    // Allow you to start the panel in an expanded way initially.
	//'disable_save_warn' => true,                    // Disable the save warning when a user changes a field

	// OPTIONAL -> Give you extra features
	'page_priority'        => null,
	// Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
	'page_parent'          => 'themes.php',
	// For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
	'page_permissions'     => 'manage_options',
	// Permissions needed to access the options panel.
	'menu_icon'            => '',
	// Specify a custom URL to an icon
	'last_tab'             => '',
	// Force your panel to always open to a specific tab (by id)
	'page_icon'            => 'icon-themes',
	// Icon displayed in the admin panel next to your menu_title
	'page_slug'            => 'vw_theme_options',
	// Page slug used to denote the panel
	'save_defaults'        => true,
	// On load save the defaults to DB before user clicks save or not
	'default_show'         => false,
	// If true, shows the default value next to each field that is not the default value.
	'default_mark'         => '',
	// What to print by the field's title if the value shown is default. Suggested: *
	'show_import_export'   => true,
	// Shows the Import/Export panel when not used as a field.

	// CAREFUL -> These options are for advanced use only
	'transient_time'       => 60 * MINUTE_IN_SECONDS,
	'output'               => true,
	// Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
	'output_tag'           => true,
	// Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
	// 'footer_credit'     => '',                   // Disable the footer credit of Redux. Please leave if you can help it.

	// FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
	'database'             => '',
	// possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
	'system_info'          => false,
	// REMOVE

	//'compiler'             => true,

	// HINTS
	'hints'                => array(
		'icon'          => 'el el-question-sign',
		'icon_position' => 'right',
		'icon_color'    => 'lightgray',
		'icon_size'     => 'normal',
		'tip_style'     => array(
			'color'   => 'light',
			'shadow'  => true,
			'rounded' => false,
			'style'   => '',
		),
		'tip_position'  => array(
			'my' => 'top left',
			'at' => 'bottom right',
		),
		'tip_effect'    => array(
			'show' => array(
				'effect'   => 'slide',
				'duration' => '500',
				'event'    => 'mouseover',
			),
			'hide' => array(
				'effect'   => 'slide',
				'duration' => '500',
				'event'    => 'click mouseleave',
			),
		),
	)
);

if ( ! class_exists( 'Redux' ) ) return;

Redux::setArgs( $vw_opt_name, $args );

/**
General
 */

Redux::setSection( $vw_opt_name, array(
	'title' => 'General',
	'id'    => 'vw-options-general',
	'desc'  => '',
	'icon'  => 'el el-website',
	'fields'     => array(
	   array(
			'id'=>'theme_info_1',
			'type' => 'raw', 
			'content' => $help_html,
		),
		
		array(
			'id'=>'site_enable_open_graph',
			'type' => 'switch', 
			'title' => 'Enable Facebook Open Graph Supports',
			'default' => 1,
		),

		array(
			'id'=>'site_enable_rtl',
			'type' => 'switch', 
			'title' => 'Enable RTL Supports',
			'subtitle'=> 'Enabling this option, The site will be shown in RTL direction.',
			'default' => 0,
		),

		array(
			'id'=>'site_enable_post_box_effects',
			'type' => 'switch', 
			'title' => 'Enable Fly-in Effects for Post',
			'default' => 1,
		),

		array(
			'id'=>'site_enable_meta_description',
			'type' => 'switch', 
			'title' => 'Enable Meta Description',
			'subtitle'=> 'You can disable the meta description tag when using the SEO plugin like Yoast',
			'default' => 1,
		),

		array(
			'id'=>'tracking_code',
			'type' => 'ace_editor', 
			'theme' => 'monokai',
			'mode' => 'html',
			'title' => 'Tracking Code',
			'subtitle'=> 'Enter your Google Analytics Code or other tracking code. The code must including <strong>&lt;script&gt;</strong> tag.',
		),

	),
) );

/**
Site
 */

Redux::setSection( $vw_opt_name, array(
	'title'      => 'Site',
	'desc'       => '',
	'id'         => 'vw-options-site',
	'icon'       => 'el el-home',
	'fields'     => array(
		array(
			'id'=>'site_layout',
			'type' => 'select',
			'title' => 'Site Layout', 
			'subtitle' => 'Select the site layout.',
			'options' => array(
				'full-medium' => 'Full-Page (970px Wide)',
				'full-large' => 'Full-Page (1200px Wide)',
				'boxed' => 'Boxed',
			),
			'default' => 'boxed',
		),

	),
) );

Redux::setSection( $vw_opt_name, array(
	'title'      => 'Site Header',
	'desc'       => '',
	'id'         => 'vw-options-site-header',
	'subsection' => true,
	'fields'     => array(

		array(
			'id'=>'header_layout',
			'type' => 'select',
			'title' => 'Site Header Layout', 
			'subtitle' => 'Select a site header style.',
			'default' => 'left-logo',
			'options' => array(
				'left-logo' => 'Left Logo',
				'center-logo' => 'Centered Logo',
			),
		),
	),
) );

Redux::setSection( $vw_opt_name, array(
	'title'      => 'Header Ads',
	'desc'       => 'Insert Ads on site header. The ads will be displayed depends on width of screen.',
	'id'         => 'vw-options-site-header-ads',
	'subsection' => true,
	'fields'     => array(
	   array(
			'id'=>'header_ads_code',
			'type' => 'ace_editor',
			'theme' => 'monokai',
			'mode' => 'html',
			'title' => 'Header Ads Code',
		),
	),
) );

Redux::setSection( $vw_opt_name, array(
	'title'      => 'Site Footer',
	'desc'       => '',
	'id'         => 'vw-options-site-footer',
	'subsection' => true,
	'fields'     => array(
		array(
			'id'=>'copyright_text',
			'type' => 'textarea', 
			'title' => 'Copyright',
			'subtitle'=> 'Enter copyright text',
			'default' => 'Copyright &copy;, All Rights Reserved.',
		),
	),
) );

/**
Blog/Archive
 */

Redux::setSection( $vw_opt_name, array(
	'title'      => 'Blog / Archive',
	'desc'       => '',
	'id'         => 'vw-options-blog',
	'icon'       => 'el el-pencil',
	'fields'     => array(
		array(
			'id'		=> 'blog_excerpt_length',
			'type'		=> 'text',
			'title'		=> 'Excerpt Length', 
			'subtitle'	=> 'The number of first words to be show when the custom excerpt is not provided.',
			'validate'	=> 'numeric',
			'default'	=> '25',
		),

		array(
			'id'=>'blog_layout',
			'type' => 'select',
			'title' => 'Blog Layout', 
			'subtitle' => 'Select default blog layout for blog page, search page, archive and category.',
			'options' => array(
				'large-thumbnail' => 'Large Thumbnail',
				'classic' => 'Classic',
			),
			'default' => 'large-thumbnail',
		),

		array(
			'id'=>'blog_sidebar',
			'type' => 'select',
			'title' => 'Default Sidebar for Blog', 
			'subtitle' => 'Used for blog page, archives & search results.',
			'data' => 'sidebar',
			'default' => 'blog-sidebar',
		),

		array(
			'id'=>'archive_enable_post_slider',
			'type' => 'switch', 
			'title' => 'Enable Posts Slider on Archive Page',
			'subtitle'=> 'All posts marked as featured will be shown in post slider.',
			'default' => 1,
		),

		array(
			'id'=>'blog_show_featured_image_single_post',
			'type' => 'switch', 
			'title' => 'Show Featured Image',
			'subtitle'=> 'Show/hide the featured image for standard post type on single-post page.',
			'default' => 1,
		),

		array(
			'id'=>'blog_show_video_player_for_thumbnail',
			'type' => 'switch', 
			'title' => 'Show Video Player on Featured Image',
			'default' => 1,
		),

	),
) );

/**
Single Post
 */

Redux::setSection( $vw_opt_name, array(
	'title'      => 'Single Post',
	'desc'       => 'An options for single post page.',
	'id'         => 'vw-options-single',
	'icon'       => 'el el-edit',
	'fields'     => array(
		array(
			'id'=>'blog_show_posts_at_top',
			'type' => 'select', 
			'title' => 'Show Posts At The Top',
			'subtitle'=> 'Choose the way to show the posts at the top of single post page.',
			'options' => array(
				'hidden' => 'Not Shown',
				'latest' => 'Show Latest Posts',
				'random' => 'Show Random Posts',
			),
			'default' => 'latest',
		),

		array(
			'id'=>'blog_enable_author_info',
			'type' => 'switch', 
			'title' => 'Enable Author Info',
			'default' => 1,
		),

		array(
			'id'=>'blog_enable_related_post',
			'type' => 'switch', 
			'title' => 'Enable Related Post',
			'default' => 1,
		),

	),
) );

Redux::setSection( $vw_opt_name, array(
	'title'      => 'Custom Tiled Gallery',
	'desc'       => '',
	'id'         => 'vw-options-single-tiled-gallery',
	'subsection' => true,
	'fields'     => array(
	  array(
			'id' => 'blog_enable_custom_gallery',
			'type' => 'switch',
			'title' => 'Enable Custom Wordpress Gallery',
			'subtitle' => 'Turn it off if you need to use the Jetpack Carousel or other gallery plugins.',
			'default' => '1' // 1 = checked | 0 = unchecked
		),

		array(
			'id' => 'blog_custom_gallery_layout',
			'type' => 'text',
			'title' => 'Gallery Layout',
			'subtitle' => 'A numbers representing the number of columns for each row. Example, "213" is the 1st row has 2 images, 2nd row has 1 image, 3rd row has 3 images.',
			'validate' => 'numeric',
			'default' => '213'
		),
	),
) );

Redux::setSection( $vw_opt_name, array(
	'title'      => 'Share Box',
	'desc'       => '',
	'id'         => 'vw-options-single-share-box',
	'subsection' => true,
	'fields'     => array(
		array(
			'id' => 'blog_enable_sharebox',
			'type' => 'switch',
			'title' => 'Enable Share-Box',
			'default' => '1'
		),

		array(
			'id' => 'sharebox_delicious',
			'type' => 'switch',
			'title' => 'Enable Share To Delicious',
			'default' => '0'
		),

		array(
			'id' => 'sharebox_digg',
			'type' => 'switch',
			'title' => 'Enable Share To Digg',
			'default' => '0'
		),

		array(
			'id' => 'sharebox_email',
			'type' => 'switch',
			'title' => 'Enable Share To Email',
			'default' => '0'
		),

		array(
			'id' => 'sharebox_facebook',
			'type' => 'switch',
			'title' => 'Enable Share To Facebook',
			'default' => '1'
		),

		array(
			'id' => 'sharebox_googleplus',
			'type' => 'switch',
			'title' => 'Enable Share To Google+',
			'default' => '1'
		),

		array(
			'id' => 'sharebox_linkedin',
			'type' => 'switch',
			'title' => 'Enable Share To LinkedIn',
			'default' => '0'
		),

		array(
			'id' => 'sharebox_pinterest',
			'type' => 'switch',
			'title' => 'Enable Share To Pinterest',
			'default' => '1'
		),

		array(
			'id' => 'sharebox_reddit',
			'type' => 'switch',
			'title' => 'Enable Share To Reddit',
			'default' => '0'
		),

		array(
			'id' => 'sharebox_tumblr',
			'type' => 'switch',
			'title' => 'Enable Share To Tumblr',
			'default' => '0'
		),

		array(
			'id' => 'sharebox_twitter',
			'type' => 'switch',
			'title' => 'Enable Share To Twitter',
			'default' => '1'
		),
		
	),
) );

/**
Typography
 */

Redux::setSection( $vw_opt_name, array(
	'title'      => 'Typography',
	'desc'       => '',
	'id'         => 'vw-options-typography',
	'icon'       => 'el el-fontsize',
	'fields'     => array(
		array(
			'id'            => 'h1',
			'type'          => 'typography',
			'title'         => 'Heading Text',
			//'compiler'      => true,  // Use if you want to hook in your own CSS compiler
			'google'        => true,    // Disable google fonts. Won't work if you haven't defined your google api key
			'font-backup'   => true,    // Select a backup non-google font in addition to a google font
			// 'font-style'    => true, // Includes font-style and weight. Can use font-style or font-weight to declare
			//'subsets'       => false, // Only appears if google is true and subsets not set to false
			'font-size'     => false,
			// 'font-weight'     => true,
			'line-height'   => false,
			//'word-spacing'  => true,  // Defaults to false
			'letter-spacing'=> false,  // Defaults to false
			//'color'         => false,
			'text-align'      => false,
			'text-transform'  => true,
			//'preview'       => false, // Disable the previewer
			'all_styles'    => VW_CONST_LOAD_ALL_HEADER_GOOGLE_FONT_STYLES,    // Enable all Google Font style/weight variations to be added to the page
			'output'      => array(
				'h1, h2, h3, h4, h5, h6',
				), // An array of CSS selectors to apply this font style to dynamically
			'units'         => 'px', // Defaults to px
			'subtitle'      => 'Choose font for header text.',
			'default'       => array(
				'color'         => '#333333',
				'font-style'    => '700',
				'font-family'   => 'Oswald',
				'text-transform'        => 'uppercase',
			),
		),
		
		array(
			'id'            => 'body',
			'type'          => 'typography',
			'title'         => 'Body Text',
			//'compiler'      => true,  // Use if you want to hook in your own CSS compiler
			'google'        => true,    // Disable google fonts. Won't work if you haven't defined your google api key
			'font-backup'   => true,    // Select a backup non-google font in addition to a google font
			//'font-style'    => false, // Includes font-style and weight. Can use font-style or font-weight to declare
			//'subsets'       => false, // Only appears if google is true and subsets not set to false
			//'font-size'     => false,
			'line-height'   => false,
			//'word-spacing'  => true,  // Defaults to false
			//'letter-spacing'=> true,  // Defaults to false
			//'color'         => false,
			'text-align'      => false,
			//'preview'       => false, // Disable the previewer
			'all_styles'    => VW_CONST_LOAD_ALL_BODY_GOOGLE_FONT_STYLES,    // Enable all Google Font style/weight variations to be added to the page
			'output'      => array(
				'body',
			), // An array of CSS selectors to apply this font style to dynamically
			'units'         => 'px', // Defaults to px
			'subtitle'      => 'Choose font for body text.',
			'default'       => array(
				'color'         => '#666666',
				'font-style'    => '400',
				'font-family'   => 'Open Sans',
				'google'        => true,
				'font-size'     => '14px',
			),
		),
	),
) );

Redux::setSection( $vw_opt_name, array(
	'title'      => 'Custom Font 1',
	'desc'       => '',
	'id'         => 'vw-options-typography-custom-font-1',
	'subsection' => true,
	'fields'     => array(
	  array(
			'id'=>'custom_font1_ttf',
			'type' => 'media',
			'preview'=> false,
			'mode'=> 'font',
			'title' => '.TTF/.OTF Font File',
		),
		array(
			'id'=>'custom_font1_woff',
			'type' => 'media',
			'preview'=> false,
			'mode'=> 'font',
			'title' => '.WOFF Font File',
		),
		array(
			'id'=>'custom_font1_svg',
			'type' => 'media',
			'preview'=> false,
			'mode'=> 'font',
			'title' => '.SVG Font File',
		),
		array(
			'id'=>'custom_font1_eot',
			'type' => 'media',
			'preview'=> false,
			'mode'=> 'font',
			'title' => '.EOT Font File',
		),
	),
) );

Redux::setSection( $vw_opt_name, array(
	'title'      => 'Custom Font 2',
	'desc'       => '',
	'id'         => 'vw-options-typography-custom-font-2',
	'subsection' => true,
	'fields'     => array(
	  array(
			'id'=>'custom_font2_ttf',
			'type' => 'media',
			'preview'=> false,
			'mode'=> 'font',
			'title' => '.TTF/.OTF Font File',
		),
		array(
			'id'=>'custom_font2_woff',
			'type' => 'media',
			'preview'=> false,
			'mode'=> 'font',
			'title' => '.WOFF Font File',
		),
		array(
			'id'=>'custom_font2_svg',
			'type' => 'media',
			'preview'=> false,
			'mode'=> 'font',
			'title' => '.SVG Font File',
		),
		array(
			'id'=>'custom_font2_eot',
			'type' => 'media',
			'preview'=> false,
			'mode'=> 'font',
			'title' => '.EOT Font File',
		),
	),
) );


/**
Logo / Favicon
 */

Redux::setSection( $vw_opt_name, array(
	'title'      => 'Logo / Favicon',
	'desc'       => '',
	'id'         => 'vw-options-logo',
	'icon'       => 'el el-star-empty',
	'fields'     => array(
		array(
			'id'		=> 'logo',
			'type'		=> 'media',
			'title'		=> 'Original Logo', 
			'subtitle'	=> 'Upload the original site logo.',
		),
		array(
			'id'		=> 'logo_2x',
			'type'		=> 'media',
			'title'		=> 'Retina Logo', 
			'subtitle'	=> 'The retina logo must be double size (2X) of the original logo.',
		),
		array(
			'id' => 'show_site_tagline',
			'type' => 'switch',
			'title' => 'Show Site Tagline', 
			'desc' => 'You can set the site tagline at "Settings > General".',
			'default' => 1
		),
		
		array(
			'id' => 'fav_icon',
			'type' => 'media',
			'title' => 'Favicon (16x16)', 
			'subtitle' => 'Default Favicon.',
		),
		array(
			'id' => 'fav_icon_iphone',
			'type' => 'media',
			'title' => 'Apple iPhone Icon (57x57)', 
			'subtitle' => 'Icon for Classic iphone.',
		),
		array(
			'id' => 'fav_icon_iphone_retina',
			'type' => 'media',
			'title' => 'Apple iPhone Retina Icon (114x114)', 
			'subtitle' => 'Icon for Retina iPhone.',
		),
		array(
			'id' => 'fav_icon_ipad',
			'type' => 'media',
			'title' => 'Apple iPad Icon (72x72)', 
			'subtitle' => 'Icon for Classic iPad.',
		),
		array(
			'id' => 'fav_icon_ipad_retina',
			'type' => 'media',
			'title' => 'Apple iPad Retina Icon (144x144)', 
			'subtitle' => 'Icon for Retina iPad.',
		),
	),
) );


/**
Font Icons
 */

Redux::setSection( $vw_opt_name, array(
	'title'      => 'Font Icons',
	'desc'       => 'You can choose additional icon fonts. The default font icons that are already in use are <a href="https://useiconic.com/icons/">Iconic</a> (Icon listing <a href="'.get_template_directory_uri().'/components/font-icons/iconic/demo.html">here</a>) and <a href="http://zocial.smcllns.com">Zocial</a> (Icon listing <a href="'.get_template_directory_uri().'/components/font-icons/social-icons/demo.html">here</a>).',
	'id'         => 'vw-options-font-icons',
	'icon'       => 'el el-puzzle',
	'fields'     => array(
		array(
			'id' => 'icon_iconic',
			'type' => 'switch',
			'title' => 'Include Iconic Icons', 
			'desc' => 'by <a href="http://somerandomdude.com/work/iconic">P.J. Onori</a>, The icon listing is <a href="'.get_template_directory_uri().'/framework/font-icons/iconic/demo.html">here</a>',
			'default' => 0
		),
		array(
			'id' => 'icon_elusive',
			'type' => 'switch',
			'title' => 'Include Elusive Icons', 
			'desc' => 'by <a href="http://aristeides.com">Aristeides Stathopoulos</a>, The icon listing is <a href="'.get_template_directory_uri().'/components/font-icons/elusive/demo.html">here</a>',
			'default' => 0
		),
		array(
			'id' => 'icon_awesome',
			'type' => 'switch',
			'title' => 'Include Font Awesome Icons', 
			'desc' => 'by <a href="http://fontawesome.io">Dav Gandy</a>, The icon listing is <a href="'.get_template_directory_uri().'/components/font-icons/awesome/demo.html">here</a>',
			'default' => 0
		),
		/*array(
			'id' => 'icon_entypo',
			'type' => 'switch',
			'title' => 'Include Entypo Icons', 
			'desc' => 'by <a href="http://entypo.com">Entypo.com</a>, The icon listing is <a href="'.get_template_directory_uri().'/components/font-icons/entypo/demo.html">here</a>',
			'default' => 1
		),*/
		array(
			'id' => 'icon_typicons',
			'type' => 'switch',
			'title' => 'Include Typicons Icons', 
			'desc' => 'by <a href="http://typicons.com">Stephen Hutchings</a>, The icon listing is <a href="'.get_template_directory_uri().'/framework/font-icons/typicons/demo.html">here</a>',
			'default' => 0
		),
	),
) );


/**
Social Profiles
 */

Redux::setSection( $vw_opt_name, array(
	'title'      => 'Social Profiles',
	'desc'       => "These are options for setting up the site's social media profiles.",
	'id'         => 'vw-options-social',
	'icon'       => 'el el-share-alt',
	'fields'     => array(
		array(
			'id' => 'social_delicious',
			'type' => 'text',
			'title' => 'Delicious URL', 
			'subtitle' => 'Enter URL to your account page.',
			'placeholder' => 'http://',
			'validate' => 'url',
		),
		array(
			'id' => 'social_digg',
			'type' => 'text',
			'title' => 'Digg URL', 
			'subtitle' => 'Enter URL to your account page.',
			'placeholder' => 'http://',
			'validate' => 'url',
		),
		array(
			'id' => 'social_dribbble',
			'type' => 'text',
			'title' => 'Dribbble URL', 
			'subtitle' => 'Enter URL to your account page.',
			'placeholder' => 'http://',
			'validate' => 'url',
		),
		array(
			'id' => 'social_facebook',
			'type' => 'text',
			'title' => 'Facebook URL', 
			'subtitle' => 'Enter URL to your account page.',
			'placeholder' => 'http://',
			'default' => 'https://facebook.com',
			'validate' => 'url',
		),
		array(
			'id' => 'social_flickr',
			'type' => 'text',
			'title' => 'Flickr URL', 
			'subtitle' => 'Enter URL to your account page.',
			'placeholder' => 'http://',
			'validate' => 'url',
		),
		array(
			'id' => 'social_forrst',
			'type' => 'text',
			'title' => 'Forrst URL', 
			'subtitle' => 'Enter URL to your account page.',
			'placeholder' => 'http://',
			'validate' => 'url',
		),
		array(
			'id' => 'social_github',
			'type' => 'text',
			'title' => 'Github URL', 
			'subtitle' => 'Enter URL to your account page.',
			'placeholder' => 'http://',
			'validate' => 'url',
		),
		array(
			'id' => 'social_googleplus',
			'type' => 'text',
			'title' => 'Google+ URL', 
			'subtitle' => 'Enter URL to your account page.',
			'placeholder' => 'http://',
			'validate' => 'url',
			'default' => 'https://plus.google.com',
		),
		array(
			'id' => 'social_instagram',
			'type' => 'text',
			'title' => 'Instagram URL', 
			'subtitle' => 'Enter URL to your account page.',
			'placeholder' => 'http://',
			'validate' => 'url',
		),
		array(
			'id' => 'social_linkedin',
			'type' => 'text',
			'title' => 'Linkedin URL', 
			'subtitle' => 'Enter URL to your account page.',
			'placeholder' => 'http://',
			'validate' => 'url',
		),
		array(
			'id' => 'social_pinterest',
			'type' => 'text',
			'title' => 'Pinterest URL', 
			'subtitle' => 'Enter URL to your account page.',
			'placeholder' => 'http://',
			'validate' => 'url',
		),
		array(
			'id' => 'social_rss',
			'type' => 'text',
			'title' => 'Rss URL', 
			'subtitle' => 'Enter URL to your account page.',
			'placeholder' => 'http://',
			'validate' => 'url',
		),
		array(
			'id' => 'social_skype',
			'type' => 'text',
			'title' => 'Skype URL', 
			'subtitle' => 'Enter URL to your account page.',
			'placeholder' => 'http://',
			'validate' => 'url',
		),
		array(
			'id' => 'social_tumblr',
			'type' => 'text',
			'title' => 'Tumblr URL', 
			'subtitle' => 'Enter URL to your account page.',
			'placeholder' => 'http://',
			'validate' => 'url',
		),
		array(
			'id' => 'social_twitter',
			'type' => 'text',
			'title' => 'Twitter URL', 
			'subtitle' => 'Enter URL to your account page.',
			'placeholder' => 'http://',
			'default' => 'https://twitter.com',
			'validate' => 'url',
		),
		array(
			'id' => 'social_vimeo',
			'type' => 'text',
			'title' => 'Vimeo URL', 
			'subtitle' => 'Enter URL to your account page.',
			'placeholder' => 'http://',
			'validate' => 'url',
		),
		array(
			'id' => 'social_yahoo',
			'type' => 'text',
			'title' => 'Yahoo URL', 
			'subtitle' => 'Enter URL to your account page.',
			'placeholder' => 'http://',
			'validate' => 'url',
		),
		array(
			'id' => 'social_youtube',
			'type' => 'text',
			'title' => 'Youtube URL', 
			'subtitle' => 'Enter URL to your account page.',
			'placeholder' => 'http://',
			'validate' => 'url',
		),
		array(
			'id' => 'social_vk',
			'type' => 'text',
			'title' => 'Vkontakte URL', 
			'subtitle' => 'Enter URL to your account page.',
			'placeholder' => 'http://',
			'validate' => 'url',
		),
		array(
			'id' => 'social_ok',
			'type' => 'text',
			'title' => 'Odnoklassniki URL', 
			'subtitle' => 'Enter URL to your account page.',
			'placeholder' => 'http://',
			'validate' => 'url',
		),
	),
) );

/**
Gallery Slider
 */

Redux::setSection( $vw_opt_name, array(
	'title'      => 'Gallery Slider',
	'desc'       => 'These are the options for the image gallery slider that is displayed in the blog entry, page composer.',
	'id'         => 'vw-options-gallery-slider',
	'icon'       => 'el el-picture',
	'fields'     => array(

		array(
			'id'=>'flexslider_slideshow',
			'type' => 'switch',
			'title' => 'Automatic Start', 
			'default' => 1,
		),

		array(
			'id'=>'flexslider_randomize',
			'type' => 'switch',
			'title' => 'Random Order', 
			'default' => 0,
		),

		array(
			'id'=>'flexslider_pauseonhover',
			'type' => 'switch',
			'title' => 'Pause On Hover', 
			'default' => 1,
		),

		array(
			'id'=>'flexslider_animation',
			'type' => 'select',
			'title' => 'Animation', 
			'subtitle' => 'Choose the animation style.',
			'options' => array(
				'fade' => 'Fade',
				'slide' => 'Slide',
			),
			'default' => 'fade',
		),

		array(
			'id'=>'flexslider_easing',
			'type' => 'select',
			'title' => 'Easing', 
			'subtitle' => 'Choose the easing of transition.',
			'options' => array(
				'linear' => 'Linear',
				'easeInSine' => 'Ease In Sine',
				'easeOutSine' => 'Ease Out Sine',
				'easeInOutSine' => 'Ease In-Out Sine',
				'easeInQuad' => 'Ease In Quad',
				'easeOutQuad' => 'Ease Out Quad',
				'easeInOutQuad' => 'Ease In-Out Quad',
				'easeInCubic' => 'Ease In Cubic',
				'easeOutCubic' => 'Ease Out Cubic',
				'easeInOutCubic' => 'Ease In-Out Cubic',
				'easeInQuart' => 'Ease In Quart',
				'easeOutQuart' => 'Ease Out Quart',
				'easeInOutQuart' => 'Ease In-Out Quart',
				'easeInQuint' => 'Ease In Quint',
				'easeOutQuint' => 'Ease Out Quint',
				'easeInOutQuint' => 'Ease In-Out Quint',
				'easeInExpo' => 'Ease In Expo',
				'easeOutExpo' => 'Ease Out Expo',
				'easeInOutExpo' => 'Ease In-Out Expo',
				'easeInCirc' => 'Ease In Circ',
				'easeOutCirc' => 'Ease Out Circ',
				'easeInOutCirc' => 'Ease In-Out Circ',
				'easeInBack' => 'Ease In Back',
				'easeOutBack' => 'Ease Out Back',
				'easeInOutBack' => 'Ease In-Out Back',
				'easeInElastic' => 'Ease In Elastic',
				'easeOutElastic' => 'Ease Out Elastic',
				'easeInOutElastic' => 'Ease In-Out Elastic',
				'easeInBounce' => 'Ease In Bounce',
				'easeOutBounce' => 'Ease Out Bounce',
				'easeInOutBounce' => 'Ease In-Out Bounce',
			),
			'default' => 'easeInCirc',
		),

		array(
			'id' => 'flexslider_slideshowspeed',
			'type' => 'text',
			'title' => 'Slideshow Duration', 
			'subtitle' => 'The time for showing slide, in milliseconds.',
			'validate' => 'numeric',
			'default' => '4000',
		),

		array(
			'id' => 'flexslider_animationspeed',
			'type' => 'text',
			'title' => 'Animation Speed', 
			'subtitle' => 'The time for transition, in milliseconds.',
			'validate' => 'numeric',
			'default' => '600',
		),
	),
) );

/**
Colors
 */

Redux::setSection( $vw_opt_name, array(
	'title'      => 'Colors',
	'desc'       => 'These are options for theme colors and background.',
	'id'         => 'vw-options-colors',
	'icon'       => 'el el-tint',
	'fields'     => array(
		array(
			'id'=>'color_primary',
			'type' => 'color', 
			'title' => 'Primary Color',
			'subtitle'=> 'Font color for links, buttons, hilight area, etc.',
			'transparent' => false,
			'default' => '#3facd6',
		),
	),
) );

Redux::setSection( $vw_opt_name, array(
	'title'      => 'Top Bar Colors',
	'desc'       => '',
	'id'         => 'vw-options-colors-top-bar',
	'subsection' => true,
	'fields'     => array(
	 	array(
			'id'=>'color_topbar_bg',
			'type' => 'color', 
			'title' => 'Background Color',
			'mode' => 'background',
			'transparent'=> false,
			'default' => '#333333'
		),
		array(
			'id'=>'color_topbar_font',
			'type' => 'color', 
			'title' => 'Font Color',
			'transparent' => false,
			'default' => '#eeeeee',
		),
		array(
			'id'=>'color_topbar_highlight',
			'type' => 'color', 
			'title' => 'Hightlight Color',
			'subtitle'=> 'Hightlight color for link and button hover, etc.',
			'transparent' => false,
			'default' => '#3facd6',
		),
		array(
			'id'=>'color_topbar_highlight_font',
			'type' => 'color', 
			'title' => 'Hightlight Font Color',
			'subtitle'=> 'Hightlight font color for link and button hover, etc.',
			'transparent' => false,
			'default' => '#ffffff',
		),
	),
) );

Redux::setSection( $vw_opt_name, array(
	'title'      => 'Header Colors',
	'desc'       => '',
	'id'         => 'vw-options-colors-header',
	'subsection' => true,
	'fields'     => array(
		array(
			'id'=>'color_header_bg',
			'type' => 'color', 
			'title' => 'Background Color',
			'mode' => 'background',
			'transparent'=> false,
			'default' => '#f9f9f9'
		),
		array(
			'id'=>'color_header_font',
			'type' => 'color', 
			'title' => 'Font Color',
			'transparent' => false,
			'default' => '#bbbbbb',
		),
	),
) );

Redux::setSection( $vw_opt_name, array(
	'title'      => 'Main Menu Colors',
	'desc'       => '',
	'id'         => 'vw-options-colors-main-menu',
	'subsection' => true,
	'fields'     => array(
		array(
			'id'=>'color_nav_bg',
			'type' => 'color', 
			'title' => 'Background Color',
			'mode' => 'background',
			'transparent'=> false,
			'default' => '#333333',
		),

		array(
			'id'=>'color_nav_font',
			'type' => 'color', 
			'title' => 'Font Color',
			'transparent'=> false,
			'default' => '#ffffff',
		),

		array(
			'id'=>'color_nav_highlight',
			'type' => 'color', 
			'title' => 'Hightlight Color',
			'subtitle' => 'Hightlight color for link and button hover, etc.',
			'transparent'=> false,
			'default' => '#3facd6',
		),

		array(
			'id'=>'color_nav_highlight_font',
			'type' => 'color', 
			'title' => 'Hightlight Font Color',
			'subtitle' => 'Hightlight font color for link and button hover, etc.',
			'transparent'=> false,
			'default' => '#ffffff',
		),
		
	),
) );

Redux::setSection( $vw_opt_name, array(
	'title'      => 'Footer Colors',
	'desc'       => '',
	'id'         => 'vw-options-colors-footer',
	'subsection' => true,
	'fields'     => array(
		array(
			'id'=>'color_footer_bg',
			'type' => 'color', 
			'title' => 'Background Color',
			'mode' => 'background',
			'transparent'=> false,
			'default' => '#111111',
		),

		array(
			'id'=>'color_footer_font',
			'type' => 'color', 
			'title' => 'Font Color',
			'transparent'=> false,
			'default' => '#999999'
		),

		array(
			'id'=>'color_footer_heading',
			'type' => 'color', 
			'title' => 'Heading Font Color',
			'transparent'=> false,
			'default' => '#222222'
		),
	),
) );

Redux::setSection( $vw_opt_name, array(
	'title'      => 'Copyright Bar Colors',
	'desc'       => '',
	'id'         => 'vw-options-colors-copyright-bar',
	'subsection' => true,
	'fields'     => array(
		array(
			'id'=>'color_copyright_bg',
			'type' => 'color', 
			'title' => 'Background Color',
			'mode' => 'background',
			'transparent'=> false,
			'default' => '#000000'
		),
		array(
			'id'=>'color_copyright_font',
			'type' => 'color', 
			'title' => 'Font Color',
			'transparent'=> false,
			'default' => '#dddddd',
		),
	),
) );

/**
bbPress
 */

Redux::setSection( $vw_opt_name, array(
	'title'      => 'bbPress',
	'desc'       => 'These are options for bbPress. You need to install the <a href="https://wordpress.org/plugins/bbpress/" target="_blank">bbPress plugin</a> before using these options.',
	'id'         => 'vw-options-bbpress',
	'icon'       => 'el el-group-alt',
	'fields'     => array(

		array(
			'id'=>'bbpress_enable_sidebar',
			'type' => 'switch',
			'title' => 'Enable Sidebar', 
			'default' => 0,
		),

		array(
			'id'=>'bbpress_sidebar',
			'type' => 'select',
			'title' => 'Default Sidebar for bbPress', 
			'data' => 'sidebar',
			'default' => 'blog-sidebar',
		),
	),
) );

/**
Custom CSS
 */

Redux::setSection( $vw_opt_name, array(
	'title'      => 'Custom CSS/JS',
	'desc'       => '',
	'id'         => 'vw-options-custom-css',
	'icon'       => 'el el-certificate',
	'fields'     => array(
		array(
			'id'=>'custom_css',
			'type' => 'ace_editor', 
			'theme' => 'monokai',
			'mode' => 'css',
			'title' => 'Custom CSS',
			'subtitle'=> 'Paste your CSS code here.',
		),
		array(
			'id'=>'custom_js',
			'type' => 'ace_editor',
			'theme' => 'monokai',
			'mode' => 'javascript',
			'title' => 'Custom JS',
			'subtitle'=> 'Paste your JS code here.',
		),
	),
) );

do_action( 'vw_action_init_theme_options', $vw_opt_name );

/* -----------------------------------------------------------------------------
 * Actions
 * -------------------------------------------------------------------------- */
add_action( 'redux/options/'.$vw_opt_name.'/saved', 'vw_options_saved' );
if ( ! function_exists( 'vw_options_saved' ) ) {
	function vw_options_saved() {
		if ( function_exists( 'icl_register_string' ) ) {
			$copyright_text = vw_get_option( 'copyright_text' );
			icl_register_string( VW_THEME_NAME.' Copyright', strtolower(VW_THEME_NAME.'_copyright'), $copyright_text );
		}
	}
}