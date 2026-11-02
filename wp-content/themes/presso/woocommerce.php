<?php get_header(); ?>

<?php
$page_content_class = 'col-sm-7 col-md-8';
$shop_page_id = woocommerce_get_page_id('shop');
$sidebar_position = get_post_meta( $shop_page_id, 'vw_page_sidebar_position', true );
$sidebar_position_class = '';
if ( 'left' == $sidebar_position ) {
	$sidebar_position_class = 'sidebar-left';
} else if( 'right' == $sidebar_position ) {
	$sidebar_position_class = 'sidebar-right';
} else {
	$sidebar_position_class = 'sidebar-hidden';
	$page_content_class = 'col-sm-12';
}
?>

<div id="page-wrapper" class="container <?php echo $sidebar_position_class; ?>">
	<div class="row">

		<div id="page-content" class="<?php echo $page_content_class; ?>">

			<?php if ( ! is_shop() ) {
				if (function_exists('woocommerce_breadcrumb')) woocommerce_breadcrumb();
			} ?>

			<?php /* Page title */ ?>
			<?php if ( ! is_singular( 'product' ) ) : ?>
				<h1 class="page-title title title-large"><?php woocommerce_page_title(); ?></h1>
				<?php 
				$page_subtitle = trim( get_post_meta( get_option('woocommerce_shop_page_id'), 'vw_page_subtitle', true ) );

				if ( is_shop() && ! empty( $page_subtitle ) ) : ?>
				<h2 class="subtitle"><?php echo $page_subtitle; ?></h2>
				<?php endif; ?>
				<hr>
			<?php endif; ?>

			<?php woocommerce_content(); ?>

		</div>

		<?php if ( 'left' == $sidebar_position || 'right' == $sidebar_position ) : ?>
		<aside id="page-sidebar" class="sidebar-wrapper col-sm-5 col-md-4">
			<?php get_sidebar(); ?>
		</aside>
		<?php endif; ?>

	</div>
</div>

<?php get_footer(); ?>