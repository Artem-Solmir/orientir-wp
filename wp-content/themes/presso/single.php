<?php get_header(); ?>

<?php get_template_part( 'templates/post-top' ); ?>

<?php
$post_id = get_queried_object_id();
$post_layout = get_post_meta( $post_id, 'vw_post_layout', true );

if ( 'fullwidth' == $post_layout ) {
	get_template_part( 'single_fullwidth' );
} else {
	get_template_part( 'single_right_sidebar' );
}
?>

<?php get_footer(); ?>