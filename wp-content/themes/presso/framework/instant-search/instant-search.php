<?php
define( 'INSTANT_SEARCH_PLUGIN_DIR', dirname( __FILE__ ) );
if ( ! defined( 'INSTANT_SEARCH_PLUGIN_URL' ) )
	define( 'INSTANT_SEARCH_PLUGIN_URL', plugins_url( 'instant-search') );

add_action( 'after_setup_theme', 'vw_setup_instant_search' );
function vw_setup_instant_search() {
	add_action( 'wp_ajax_vw_instant_search', 'vw_instant_search' );
	add_action( 'wp_ajax_nopriv_vw_instant_search', 'vw_instant_search' );
	add_action( 'wp_enqueue_scripts', 'vw_instant_search_localize' );
}

function vw_instant_search() {
	global $wpdb;
	if (isset($_GET['s'])){
		$q = htmlspecialchars($_GET['s']);
		$q = sanitize_text_field($q);
		$q = esc_sql($q);
	}else{
		echo json_encode(apply_filters( 'instant_search_res', array() ) );
		die();
	}

	$query = array(
		// 'post_type' => apply_filters('vw_instant_search_post_type', array( 'post', 'page' ) ),
		'suppress_filters' => true,
		'update_post_term_cache' => false,
		'update_post_meta_cache' => false,
		'post_status' => 'publish',
		'order' => 'DESC',
		'orderby' => 'post_date',
		'posts_per_page' => 6,
		's' => $q
	);

	query_posts($query);

	// Check if any posts were found.
	if ( ! have_posts() ){
		$html = '<li class="no-result">';
		$html .= '<span class="result-item-title">'.__( 'No result was found.', 'envirra').'</span>';
		$html .= '</li>';
		echo $html;
		die();
	}

	//Create an array with the results
	$html = '';
	while ( have_posts() ) { the_post();
		$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(), 'thumbnail' );
		
		$html .= '<li>';
		$html .= '<a href="'.get_permalink().'">';
		$html .= '<span class="result-item-thumbnail vw-imgliquid">';
		if ( ! empty( $thumbnail[0] ) ) {
			$html .= '<img src="'.$thumbnail[0].'">';
		} else {
			if ( 'link' == get_post_format() ) {
				$post_icon = 'symbol-link-1';

			} elseif ( 'audio' == get_post_format() ) {
				$post_icon = 'symbol-headphones';

			} elseif ( 'video' == get_post_format() ) {
				$post_icon = 'symbol-play';

			} elseif ( 'gallery' == get_post_format() ) {
				$post_icon = 'symbol-picture';

			} elseif ( 'quote' == get_post_format() ) {
				$post_icon = 'symbol-quote';

			} else {
				$post_icon = 'symbol-align-left';

			}

			$html .= '<i class="icon-'.$post_icon.'"></i>';
		}
		$html .= '</span>';

		$html .= '<div class="result-item-content header-font">';
		$html .= '<span class="result-item-title">'.get_the_title().'</span>';
		$html .= '<span class="result-item-date">'.get_the_date().'</span>';
		$html .= '</div>';
		$html .= '</a>';

		$html .= '</li>';
	}

	$html .= '<li class="all-result header-font">';
	$html .= '<a href="'.get_search_link().'"> <span class="result-item-title">'.__( 'View all results &raquo;', 'envirra' ).'</span> </a>';
	$html .= '</li>';

	echo $html;
	wp_reset_query();
	die();
}

function vw_instant_search_localize(){
	wp_enqueue_script( 'instant-search', INSTANT_SEARCH_PLUGIN_URL.'/instant-search.js', array( 'jquery' ), VW_THEME_VERSION );
	wp_localize_script( 'instant-search', 'instant_search', array(
		'blog_url' => home_url(),
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'placeholder' => __( 'Search', 'envirra' ),
	));
}