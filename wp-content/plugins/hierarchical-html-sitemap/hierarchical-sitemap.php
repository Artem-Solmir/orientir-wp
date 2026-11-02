<?php
/* ====================================================================================================
 *	Plugin Name: Hierarchical HTML Sitemap
 *	Description: Generates hierarchical HTML sitemap wich displays hierarchically sorted categories with posts links. You need add <code>[htmlmap]</code> shortcode at any page. It's working easy and fast. Super lightweight PHP code, without external CSS/JS files.
 *	 Plugin URI: https://wp-puzzle.com/hierarchical-html-sitemap/
 *	     Author: Alexandra Vovk & WP Puzzle
 *	    Version: 1.3
 *	 Author URI: http://wp-puzzle.com/
 *	Donate link: https://www.liqpay.com/ru/checkout/card/avovkdesin
 *  Text Domain: hierarchical-html-sitemap
 *  Domain Path: /languages
 * ==================================================================================================== */

__( "Hierarchical HTML Sitemap", 'hierarchical-html-sitemap' );
__( "Generates hierarchical HTML sitemap wich displays hierarchically sorted categories with posts links. You need add <code>[htmlmap]</code> shortcode at any page. It's working easy and fast. Super lightweight PHP code, without external CSS/JS files.", 'hierarchical-html-sitemap' );



/**
 * Load textdomain
 *
 */
function hierarchicalsitemap_load_textdomain() {

	load_plugin_textdomain( 'hierarchical-html-sitemap', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );

}

add_action( 'plugins_loaded', 'hierarchicalsitemap_load_textdomain' );


/**
 *
 * HIERARCHICAL BLOG MAP shortcode
 *
 * @param      $atts
 * @param null $content
 *
 * @return string
 *
 */
function hierarchicalsitemap_shortcode_htmlmap( $atts, $content = null ) {

	$hidecloud = ( '' != $atts && in_array( 'hidecloud', $atts ) ) ? 1 : 0;
	$showpages = ( '' != $atts && in_array( 'showpages', $atts ) ) ? 1 : 0;

	/**
	 * new params - showdescription, hidedate
	 * @since v.1.1
	 */
	$showdescription = ( '' != $atts && in_array( 'showdescription', $atts ) ) ? 1 : 0;
	$hidedate        = ( '' != $atts && in_array( 'hidedate', $atts ) ) ? 1 : 0;
	$hidecount       = ( '' != $atts && in_array( 'hidecount', $atts ) ) ? 1 : 0;

	/**
	 * @var string $exclude
	 * @var string $exclude_cat
	 * @var string $max_per_page
	 */
	extract(
		shortcode_atts(
			array(
				'exclude'     => '',
				'exclude_cat' => '',
			),
			$atts
		)
	);
	$exclude     = ( $exclude ) ? '&exclude=' . $exclude : '';
	$exclude_cat = ( $exclude_cat ) ? '&exclude=' . $exclude_cat : '';

	$html = hierarchicalsitemap_hierarchical_category_tree(
		0,
		$exclude,
		$exclude_cat,
		array(
			'hidecloud'       => $hidecloud,
			'showdescription' => $showdescription,
			'hidedate'        => $hidedate,
			'hidecount'       => $hidecount,
		)
	);
	$out  = ( $hidecloud ) ? "" : "<p id=\"htmlmap_cats\">" . substr( $html['cloud'], 2 ) . "</p>";
	$out  .= "<div id=\"htmlmap_posts\">" . $html['posts'] . "</div>";

	/**
	 * Filters the posts list HTML.
	 *
	 * @since 1.3
	 *
	 * @param string $out Full posts list HTML code
	 */
	$out = apply_filters( 'hierarchicalsitemap_posts_list_html', $out );

	$out .= ( $showpages ) ? hierarchicalsitemap_get_pages_list( $exclude ) : "";

	return $out;
}

add_shortcode( 'htmlmap', 'hierarchicalsitemap_shortcode_htmlmap' );


/**
 * RECURSIVE get html blog map
 *
 *
 *
 *
 * @param   string $cid                     category ID
 * @param   string $ex                      list posts IDs to exclude from HTML Map
 * @param   string $ex_cat                  list categories IDs to exclude from HTML Map
 * @param   array  $arg                     array with display options:
 *                                          hidecloud=1 - hide categories cloud
 *                                          showdescription=0 - show category description
 *                                          hidedate=0 - hide post date
 *
 * @param array    $out
 *
 * @return array|string HTML code with sitemap
 *
 */
function hierarchicalsitemap_hierarchical_category_tree(
	$cid,
	$ex,
	$ex_cat,
	$arg = array(
		'hidecloud'       => 1,
		'showdescription' => 0,
		'hidedate'        => 0,
		'hidecount'       => 0,
	),
	$out = array( 'cloud' => '', 'posts' => '' )
) {

	$categories = get_categories( 'hide_empty=false&orderby=name&order=ASC&parent=' . $cid . $ex_cat );

	if ( $categories ) :
		foreach ( $categories as $cat ) :
			$out['cloud'] .= ', <span class="cat"><a href="#cat_' . $cat->term_id . '">' . $cat->name . '</a> <small>[' . $cat->count . ']</small></span>';
			$tocloudlink  = ( $arg['hidecloud'] ) ? "" : " <a href='#htmlmap_cats'>&uarr;</a>";

			$tag        = ( 0 == $cid ) ? "h2" : "h3";
			$count_html = ( $arg['hidecount'] ) ? '' : " <small>[" . $cat->count . "]</small>";

			$posts = get_posts( 'posts_per_page=-1&orderby=post_date&order=DESC&category__in=' . $cat->term_id . $ex );

			/**
			 * Filters the category title html.
			 *
			 * @since 1.3
			 *
			 * @param string  $cat ->name  Category title.
			 * @param WP_Term $cat Category term object.
			 */
			$out['posts'] .= apply_filters(
				'hierarchicalsitemap_category_title_html',
				"\n<$tag id='cat_$cat->term_id'>" . $cat->name . $count_html . $tocloudlink . "</$tag>\n",
				$cat->name,
				$cat
			);
			$out['posts'] .= ( "" != $cat->category_description && $arg['showdescription'] ) ? "<p><i>" . $cat->category_description . "</i></p>\n" : '';

			$out['posts'] .= "<ul>\n";

			if ( count( $posts ) > 0 ) {
				$cnt = 0;
				foreach ( $posts as $post ) {

					$cnt ++;
					$date         = explode( " ", $post->post_date );
					$out['posts'] .= ( $arg['hidedate'] ) ? "<li>" : "<li><small>$date[0]</small>&nbsp;";
					$out['posts'] .= "<a href=\"" . get_permalink( $post->ID ) . "\">$post->post_title</a>";

					if ( $cnt == count( $posts ) ) {
						$out = hierarchicalsitemap_hierarchical_category_tree(
							$cat->term_id,
							$ex,
							$ex_cat,
							$arg,
							$out
						);
					}

					$out['posts'] .= "</li>\n";
				}

			} else if ( 0 == count( $posts ) ) {

				// check empty category for all childrens and show it
				$out['posts'] .= '<li class="null" style="list-style:none">';
				$out          = hierarchicalsitemap_hierarchical_category_tree(
					$cat->term_id,
					$ex,
					$ex_cat,
					$arg,
					$out
				);
				$out['posts'] .= "</li>";
			}

			$out['posts'] .= "</ul>\n";

		endforeach;
	endif;

	$out['posts'] .= "\n";

	return $out;
}


/**
 * get html pages list
 *
 * @param $exclude
 *
 * @return string
 */
function hierarchicalsitemap_get_pages_list( $exclude ) {

	$html  = "<ul id=\"htmlmap_pages\">";
	$pages = get_pages( $exclude );

	foreach ( $pages as $page ) {
		$id    = $page->ID;
		$title = $page->post_title;
		$link  = apply_filters( 'the_permalink', get_permalink( $id ) );
		$html  .= "<li><a href=\"$link\" title=\"$title\">$title</a></li>";
	}

	$html .= "</ul>";


	/**
	 * Filters the pages list HTML.
	 *
	 * @since 1.3
	 *
	 * @param string $out Full pages list HTML code
	 */
	$html = apply_filters(
		'hierarchicalsitemap_pages_list_html',
		"<h2>" . __( "All Pages", 'hierarchical-html-sitemap' ) . "</h2>" . $html
	);

	return $html;

}

