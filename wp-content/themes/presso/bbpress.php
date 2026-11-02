<?php get_header(); ?>
<?php
$enable_sidebar = vw_get_option( 'bbpress_enable_sidebar' );
?>
<div id="page-wrapper" class="container sidebar-right">
	<div class="row">
		<?php if ( $enable_sidebar ) : ?>
		<div id="page-content" class="col-sm-7 col-md-8">
		<?php else : ?>
		<div id="page-content" class="col-sm-12">
		<?php endif; ?>

			<?php if (have_posts()) : ?>
				<?php while ( have_posts() ) : the_post(); ?>

					<?php get_template_part( 'templates/page-title' ); ?>
					
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'clearfix' ); ?>>
						<?php get_template_part( 'templates/post-formats/format', get_post_format() ); ?>
						<div class="post-content"><?php the_content(); ?></div>
					</article>
					
				<?php endwhile; ?>

				<?php get_template_part( 'templates/pagination' ); ?>

				<?php comments_template(); ?>

			<?php else : ?>

				<h2><?php _e('Not Found', 'envirra') ?></h2>

			<?php endif; ?>
		</div>

		<?php if ( $enable_sidebar ) : ?>
		<aside id="page-sidebar" class="sidebar-wrapper col-sm-5 col-md-4">
			<div class="sidebar-inner">
			<?php dynamic_sidebar( vw_get_option( 'bbpress_sidebar' ) ); ?>
			</div>
		</aside>
		<?php endif; ?>
	</div>
</div>

<?php get_footer(); ?>