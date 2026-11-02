<?php
// prevent direct access to functions.php in wordpress
if ( ! defined( 'ABSPATH' ) ) die( 'Error!' );

// Define content width
if ( ! isset( $content_width ) )
	$content_width = 1140;

/* -----------------------------------------------------------------------------
 * Constants
 * -------------------------------------------------------------------------- */
if ( function_exists( 'wp_get_theme' ) ) {
	$theme_version = wp_get_theme()->get('Version');
} else {
	$theme_version = '2.0.3';
}
if ( ! defined( 'VW_THEME_VERSION' ) )
	define( 'VW_THEME_VERSION', $theme_version );

// Plugin url for MetaBox
if ( ! defined( 'RWMB_URL' ) )
	define( 'RWMB_URL', get_template_directory_uri().'/framework/meta-box/' );

// Plugin url for Express Slider
// define( 'ES_PLUGIN_URL', get_template_directory_uri().'/framework/express-slider' );

// Plugin url for Instant Search
if ( ! defined( 'INSTANT_SEARCH_PLUGIN_URL' ) )
	define( 'INSTANT_SEARCH_PLUGIN_URL', get_template_directory_uri().'/framework/instant-search' );

// Plugin url for Redux Framwork
if ( ! defined( 'Redux_OPTIONS_URL' ) )
	define( 'Redux_OPTIONS_URL', get_template_directory_uri().'/framework/admin/options/' ); // must ending with slash

if ( ! defined( 'VW_CONST_LOAD_ALL_HEADER_GOOGLE_FONT_STYLES' ) ) define( 'VW_CONST_LOAD_ALL_HEADER_GOOGLE_FONT_STYLES', true );
if ( ! defined( 'VW_CONST_LOAD_ALL_BODY_GOOGLE_FONT_STYLES' ) ) define( 'VW_CONST_LOAD_ALL_BODY_GOOGLE_FONT_STYLES', true );
if ( ! defined( 'VW_CONST_ASYNC_TYPOGRAPHY' ) ) define( 'VW_CONST_ASYNC_TYPOGRAPHY', true );

/* -----------------------------------------------------------------------------
 * Includes
 * -------------------------------------------------------------------------- */

if ( true ) {
	// New version of theme options panel
	
	if ( ! class_exists( 'ReduxFramework' ) ) {
		// Load ReduxFramework for Theme Options Panel
		require_once get_template_directory().'/framework/redux-framework/redux-framework.php';
	}

	require_once get_template_directory().'/framework/theme-options-migration.php';
	require_once get_template_directory().'/framework/admin/options.php';
	require_once get_template_directory().'/framework/custom-css.php';
	require_once get_template_directory().'/framework/custom-font.php';

} else {
	// Deprecated code for backward-compatible
	require_once get_template_directory().'/framework/admin/options.deprecated.php';
	require_once get_template_directory().'/framework/custom-css.deprecated.php';
}

require_once get_template_directory().'/framework/enqueue.php';
require_once get_template_directory().'/framework/navigation.php';
require_once get_template_directory().'/framework/sidebars.php';
require_once get_template_directory().'/framework/custom-js.php';
require_once get_template_directory().'/framework/gallery.php';
require_once get_template_directory().'/framework/user-profile-fields.php';
require_once get_template_directory().'/framework/instant-search/instant-search.php';
require_once get_template_directory().'/framework/widgets/custom-text.php';
require_once get_template_directory().'/framework/widgets/about-us.php';
require_once get_template_directory().'/framework/widgets/image-banner.php';
require_once get_template_directory().'/framework/widgets/custom-banner.php';
require_once get_template_directory().'/framework/widgets/most-commented.php';
require_once get_template_directory().'/framework/widgets/latest-category.php';
require_once get_template_directory().'/framework/widgets/latest-comments.php';
require_once get_template_directory().'/framework/widgets/categories.php';
require_once get_template_directory().'/framework/widgets/authors.php';
require_once get_template_directory().'/framework/widgets/review-posts.php';
require_once get_template_directory().'/framework/widgets/social-subscription.php';
require_once get_template_directory().'/framework/widgets/post-slider.php';
require_once get_template_directory().'/framework/page-composer/page-composer.php';
require_once get_template_directory().'/framework/class.SidebarGenerator.php';
require_once get_template_directory().'/framework/template-tags.php';
require_once get_template_directory().'/framework/bbpress.php';
require_once get_template_directory().'/framework/woocommerce.php';
require_once get_template_directory().'/framework/rtl.php';

if ( is_admin() ) {
	require_once get_template_directory().'/framework/meta-box/meta-box.php';
	require_once get_template_directory().'/framework/meta-box/meta-box-custom-fields.php';
	require_once get_template_directory().'/framework/meta-boxes.php';
	require_once get_template_directory().'/framework/envato-wordpress-toolkit-library/connect-wordpress-toolkit.php';
	require_once get_template_directory().'/framework/admin-notices.php';
	
	if ( ! defined( 'VW_DISABLE_DEMO_IMPORTER' ) )
		require_once get_template_directory().'/framework/demo-importer/demo-importer.php';
}

/* -----------------------------------------------------------------------------
 * Setup theme
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vw_after_theme_setup' ) ) {
	function vw_after_theme_setup() {
		/**
		 * Make theme translatable
		 */
		$lang_folder = get_stylesheet_directory() . '/languages';
		load_theme_textdomain( 'envirra', $lang_folder );
		load_theme_textdomain( 'envirra-backend', $lang_folder );

		/**
		 * Add supported features
		 */
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'post-formats', array( 'audio', 'gallery', 'video' ) );
		add_theme_support( 'custom-background', apply_filters( 'vw_custom_background_args', array(
			'wp-head-callback' => 'vw_custom_background_cb',
		) ) );

		/**
		 * Define thumbnail sizes
		 */
		add_theme_support( 'post-thumbnails' );
		$vw_image_sizes = apply_filters( 'vw_image_sizes', array(
			'vw_square_tiny' => array( 80, 80, true ),
			'vw_extra_small' => array( 165, 75, true ),
			'vw_small_2' => array( 265, 147, true ),
			'vw_small' => array( 360, 200, true ),
			'vw_square_small' => array( 360, 360, true ),
			'vw_medium' => array( 750, 420, true ),
			'vw_square_medium' => array( 750, 750, true ),
			'vw_large' => array( 1140, 641, true ),
		) );

		foreach ( $vw_image_sizes as $name => $args ) {
			add_image_size( $name, $args[0], $args[1], $args[2] );
		}

		/**
		 * Register menu
		 */
		register_nav_menu( 'main_navigation', 'Main Navigation' );
		register_nav_menu( 'top_navigation', 'Top Navigation' );

		/**
		 * Add custom filters
		 */
		add_filter( 'excerpt_length', 'vw_custom_excerpt_length' );

		add_filter( 'excerpt_more', 'vw_custom_read_more' );
		add_filter( 'the_content_more_link', 'vw_custom_read_more' );
		add_filter( 'body_class', 'vw_body_class_options' );
		add_filter( 'wp_title', 'vw_wp_title', 10, 2 );

		/**
		 * Add custom actions
		 */
	}
	add_action( 'after_setup_theme', 'vw_after_theme_setup' );
}

/* -----------------------------------------------------------------------------
 * Add Custom Excerpt Length
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vw_custom_excerpt_length' ) ) {
	function vw_custom_excerpt_length( $length ) {
		return intval( vw_get_option( 'blog_excerpt_length' ) );
	}
}

/* -----------------------------------------------------------------------------
 * Add Custom Read More
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vw_custom_read_more' ) ) {
	function vw_custom_read_more( $length ) {
		global $post;
		return ' ...';
	}
}

/* -----------------------------------------------------------------------------
 * Add Body Classes
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vw_body_class_options' ) ) {
	function vw_body_class_options( $classes ) {
		// Site layout option
		$site_layout = vw_get_option( 'site_layout' );
		if ( empty( $site_layout ) || ! $site_layout ) $site_layout = 'full-large';
		$classes[] = sprintf( 'site-layout-%s', $site_layout );

		// Post box effect option
		$site_enable_post_box_effects = vw_get_option( 'site_enable_post_box_effects' );
		if ( $site_enable_post_box_effects == '1' ) {
			$classes[] = 'site-enable-post-box-effects';
		} else {
			$classes[] = 'site-disable-post-box-effects';
		}

		return $classes;
	}
}

/* -----------------------------------------------------------------------------
 * Title
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vw_wp_title' ) ) {
	function vw_wp_title( $title, $sep ) {
		global $paged, $page;

		if ( is_feed() ) {
			return $title;
		}

		// Add the site name.
		$title .= get_bloginfo( 'name' );

		// Add the site description for the home/front page.
		$site_description = get_bloginfo( 'description', 'display' );
		if ( $site_description && ( is_home() || is_front_page() ) ) {
			$title = "$title $sep $site_description";
		}

		// Add a page number if necessary.
		if ( $paged >= 2 || $page >= 2 ) {
			$title = "$title $sep " . sprintf( __( 'Page %s', 'twentyfourteen' ), max( $paged, $page ) );
		}

		return $title;
	}
}

/* -----------------------------------------------------------------------------
 * Custom Background
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vw_custom_background_cb' ) ) {
	function vw_custom_background_cb() {
		// $background is the saved custom image, or the default image.
		$background = set_url_scheme( get_background_image() );

		// $color is the saved custom color.
		// A default has to be specified in style.css. It will not be printed here.
		$color = get_theme_mod( 'background_color' );

		if ( ! $background && ! $color )
			return;

		$style = $color ? "background-color: #$color;" : '';

		if ( $background ) {
			$image = " background-image: url('$background');";

			$repeat = get_theme_mod( 'background_repeat', get_theme_support( 'custom-background', 'default-repeat' ) );
			if ( ! in_array( $repeat, array( 'no-repeat', 'repeat-x', 'repeat-y', 'repeat' ) ) )
				$repeat = 'repeat';
			$repeat = " background-repeat: $repeat;";

			$position = get_theme_mod( 'background_position_x', get_theme_support( 'custom-background', 'default-position-x' ) );
			if ( ! in_array( $position, array( 'center', 'right', 'left' ) ) )
				$position = 'left';
			$position = " background-position: top $position;";

			$attachment = get_theme_mod( 'background_attachment', get_theme_support( 'custom-background', 'default-attachment' ) );
			if ( ! in_array( $attachment, array( 'fixed', 'scroll' ) ) )
				$attachment = 'scroll';
			$attachment = " background-attachment: $attachment;";

			$style .= $image . $repeat . $position . $attachment;
		}
	?>
	<style type="text/css" id="custom-background-css">
	body.custom-background.site-layout-boxed
	, body.custom-background.site-layout-full-large #off-canvas-body-inner
	, body.custom-background.site-layout-full-medium #off-canvas-body-inner
	{ <?php echo trim( $style ); ?> }
	</style>
	<?php
	}
}

add_filter( 'get_comment', 'vw_force_comment_author_url' );
function vw_force_comment_author_url( $comment ) {
	// does the comment have a valid author URL?
	$no_url = !$comment->comment_author_url || $comment->comment_author_url == 'http://';

	if ( $comment->user_id && $no_url ) {
		// comment was written by a registered user but with no author URL
		$comment->comment_author_url = get_author_posts_url( $comment->user_id );
	}

	return $comment;
}

// add_filter('the_excerpt_rss', 'vw_rss_post_thumbnail');
add_filter('the_content_feed', 'vw_rss_post_thumbnail');
if ( ! function_exists( 'vw_rss_post_thumbnail' ) ) {
	function vw_rss_post_thumbnail( $content ) {
		global $post;
		if( has_post_thumbnail( $post->ID ) ) {
			$content = '<p>' . get_the_post_thumbnail( $post->ID ) . '</p>' . $content;
		}

		return $content;
	}
}

/* -----------------------------------------------------------------------------
 * Remove extra padding in image caption
 * -------------------------------------------------------------------------- */
add_filter( 'img_caption_shortcode_width', 'vw_remove_caption_padding' );
if ( ! function_exists( 'vw_remove_caption_padding' ) ) {
	function vw_remove_caption_padding( $width ) {
		return $width - 10;
	}
}

/* -----------------------------------------------------------------------------
 * Get Archive Date for Displaying on Archive Page
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vw_get_archive_date' ) ) {
	function vw_get_archive_date() {
		if ( is_year() ) {
			return get_the_date( _x( 'Y', 'yearly archives date format', 'envirra' ) );	

		} elseif ( is_month() ) {
			return get_the_date( _x( 'F Y', 'monthly archives date format', 'envirra' ) );

		} else {
			return get_the_date();
			
		}
	}
}