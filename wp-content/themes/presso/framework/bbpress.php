<?php 

function vw_filter_bbp_breadcrumb( $args ) {
	$my_args = array(
		'before'          => '<div class="bbp-breadcrumb header-font"><p>',
		'after'           => '</p></div>',
	);

	$args = wp_parse_args( $my_args, $args );
	return $args;
}

add_filter( 'bbp_before_get_breadcrumb_parse_args', 'vw_filter_bbp_breadcrumb' );