<?php

add_action( 'after_setup_theme', 'vw_setup_sidebars' );
function vw_setup_sidebars() {
	add_action( 'widgets_init', 'vw_register_sidebars' );
}

/* -----------------------------------------------------------------------------
 * Register sidebars
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vw_register_sidebars' ) ) {
	function vw_register_sidebars() {
		/**
		 * Blog widget sidebar
		 */

		register_sidebar( array(
			'name' => __( 'Blog Sidebar', 'envirra-backend' ),
			'id'   => 'blog-sidebar',
			'description'   => __( 'These are widgets for the Blog sidebar.', 'envirra-backend' ),
			'before_widget' => '<div id="%1$s" class="widget vw-sidebar-blog %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>'
		) );

		/**
		 * Page widget sidebar
		 */
		register_sidebar( array(
			'name' => __( 'Page Sidebar', 'envirra-backend' ),
			'id'   => 'page-sidebar',
			'description'   => __( 'These are widgets for the static page.', 'envirra-backend' ),
			'before_widget' => '<div id="%1$s" class="widget vw-sidebar-page %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>'
		) );
		
		/**
		 * Footer sidebar
		 */
		register_sidebar( array(
			'name' => __( 'Footer Sidebar 1', 'envirra-backend' ),
			'id'   => 'footer-sidebar-1',
			'description'   => __( 'These are widgets for the Footer.', 'envirra-backend' ),
			'before_widget' => '<div id="%1$s" class="widget vw-sidebar-footer-1 %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>'
		) );
		register_sidebar( array(
			'name' => __( 'Footer Sidebar 2', 'envirra-backend' ),
			'id'   => 'footer-sidebar-2',
			'description'   => __( 'These are widgets for the Footer.', 'envirra-backend' ),
			'before_widget' => '<div id="%1$s" class="widget vw-sidebar-footer-2 %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>'
		) );
		register_sidebar( array(
			'name' => __( 'Footer Sidebar 3', 'envirra-backend' ),
			'id'   => 'footer-sidebar-3',
			'description'   => __( 'These are widgets for the Footer.', 'envirra-backend' ),
			'before_widget' => '<div id="%1$s" class="widget vw-sidebar-footer-3 %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>'
		) );
	}
}