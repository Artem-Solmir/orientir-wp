<?php

add_action( 'after_setup_theme', 'vw_setup_custom_css' );
function vw_setup_custom_css() {
	add_action( 'wp_head', 'vw_render_custom_css', 99 );
}

/* -----------------------------------------------------------------------------
 * Render Custom CSS
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vw_render_custom_css' ) ) {
	function vw_render_custom_css() {
		global $vw_presso;
	?>
	<!-- Theme's Custom CSS -->
	<style type="text/css">
		::selection { color: white; background-color: <?php echo $vw_presso['color_primary']; ?>; }
		
		h1 { line-height: 1.1; }
		h2 { line-height: 1.2; }
		h3, h4, h5, h6 { line-height: 1.4; }

		.header-font,
		woocommerce div.product .woocommerce-tabs ul.tabs li, .woocommerce-page div.product .woocommerce-tabs ul.tabs li, .woocommerce #content div.product .woocommerce-tabs ul.tabs li, .woocommerce-page #content div.product .woocommerce-tabs ul.tabs li
		{ font-family: <?php echo $vw_presso['h1']['font-family'] ?>; font-weight: <?php echo $vw_presso['h1']['font-weight'] ?>; }
		.header-font-color { color: <?php echo $vw_presso['h1']['color'] ?>; }

		.wp-caption p.wp-caption-text {
			color: <?php echo $vw_presso['h1']['color'] ?>;
			border-bottom-color: <?php echo $vw_presso['h1']['color'] ?>;
		}
		
		.body-font { font-family: <?php echo $vw_presso['body']['font-family']; ?>; font-weight: <?php echo $vw_presso['body']['font-weight']; ?>; }

		/* Only header font, No font-weight */
		.mobile-nav,
		.top-nav,
		.comment .author > span, .pingback .author > span, 
		.label, .tagcloud a,
		.woocommerce .product_meta .post-tags a,
		.bbp-topic-tags a,
		.woocommerce div.product span.price, .woocommerce-page div.product span.price, .woocommerce #content div.product span.price, .woocommerce-page #content div.product span.price, .woocommerce div.product p.price, .woocommerce-page div.product p.price, .woocommerce #content div.product p.price, .woocommerce-page #content div.product p.price,
		.main-nav .menu-link { font-family: <?php echo $vw_presso['h1']['font-family']; ?>; }

		/* Primary Color */
		.primary-bg,
		.label, .tagcloud a,
		.woocommerce nav.woocommerce-pagination ul li span.current, .woocommerce-page nav.woocommerce-pagination ul li span.current, .woocommerce #content nav.woocommerce-pagination ul li span.current, .woocommerce-page #content nav.woocommerce-pagination ul li span.current, .woocommerce nav.woocommerce-pagination ul li a:hover, .woocommerce-page nav.woocommerce-pagination ul li a:hover, .woocommerce #content nav.woocommerce-pagination ul li a:hover, .woocommerce-page #content nav.woocommerce-pagination ul li a:hover, .woocommerce nav.woocommerce-pagination ul li a:focus, .woocommerce-page nav.woocommerce-pagination ul li a:focus, .woocommerce #content nav.woocommerce-pagination ul li a:focus, .woocommerce-page #content nav.woocommerce-pagination ul li a:focus,
		#pagination > span {
			background-color: <?php echo $vw_presso['color_primary']; ?>;
		}
		a, .social-share a:hover, .site-social-icons a:hover,
		.bbp-topic-header a:hover,
		.bbp-forum-header a:hover,
		.bbp-reply-header a:hover { color: <?php echo $vw_presso['color_primary']; ?>; }
		.button-primary { color: <?php echo $vw_presso['color_primary']; ?>; border-color: <?php echo $vw_presso['color_primary']; ?>; }
		.primary-border { border-color: <?php echo $vw_presso['color_primary']; ?>; }

		/* Top-bar Colors */
		.top-bar {
			background-color: <?php echo $vw_presso['color_topbar_bg']; ?>;
			color: <?php echo $vw_presso['color_topbar_font']; ?>;
		}

		#open-mobile-nav, .top-nav  a, .top-bar-right > a {
			color: <?php echo $vw_presso['color_topbar_font']; ?>;
		}

		#open-mobile-nav:hover, .top-nav  a:hover, .top-bar-right > a:hover {
			background-color: <?php echo $vw_presso['color_topbar_highlight']; ?>;
			color: <?php echo $vw_presso['color_topbar_highlight_font']; ?>;
		}

		.top-nav .menu-item:hover { background-color: <?php echo $vw_presso['color_topbar_highlight']; ?>; }
		.top-nav .menu-item:hover > a { color: <?php echo $vw_presso['color_topbar_highlight_font']; ?>; }

		/* Header Colors */
		.main-bar {
			background-color: <?php echo $vw_presso['color_header_bg']; ?>;
			color: <?php echo $vw_presso['color_header_font']; ?>;
		}

		/* Main Navigation Colors */
		.main-nav-bar {
			background-color: <?php echo $vw_presso['color_nav_bg']; ?>;
		}

		.main-nav-bar, .main-nav > .menu-item > a {
			color: <?php echo $vw_presso['color_nav_font']; ?>;
		}

		.main-nav .menu-item:hover > .menu-link,
		.main-nav > .current-menu-ancestor > a,
		.main-nav > .current-menu-item > a {
			background-color: <?php echo $vw_presso['color_nav_highlight']; ?>;
			color: <?php echo $vw_presso['color_nav_highlight_font']; ?>;
		}

		/* Widgets */
		.widget_vw_widget_social_subscription .social-subscription:hover .social-subscription-icon { background-color: <?php echo $vw_presso['color_primary']; ?>; }
		.widget_vw_widget_social_subscription .social-subscription:hover .social-subscription-count { color: <?php echo $vw_presso['color_primary']; ?>; }

		.widget_vw_widget_categories a:hover { color: <?php echo $vw_presso['color_primary']; ?>; }

		/* Footer Colors */
		#footer {
			background-color: <?php echo $vw_presso['color_footer_bg']; ?>;
		}

		#footer .widget-title {
			color: <?php echo $vw_presso['color_primary']; ?>;
		}

		#footer,
		#footer .title,
		#footer .comment-author,
		#footer .social-subscription-count
		{ color: <?php echo $vw_presso['color_footer_font']; ?>; }

		.copyright {
			background-color: <?php echo $vw_presso['color_copyright_bg']; ?>;
		}
		.copyright, .copyright a {
			color: <?php echo $vw_presso['color_copyright_font']; ?>;
		}

		/* Custom Styles */
		<?php do_action( 'vw_render_custom_css' ); ?>
		<?php echo vw_get_option( 'custom_css' ); ?>
	</style>
	<?php
	}
}