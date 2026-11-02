<?php get_header(); ?>

<div id="page-wrapper" class="container">
	<div class="row">
		<div id="page-content" class="col-sm-7 col-md-8">
			
			<?php if (have_posts()) : ?>

				<?php get_template_part( 'templates/page-title' ); ?>
				
				<?php $blog_layout = vw_get_option( 'blog_layout' ); ?>
				
				<?php if ( 'classic' == $blog_layout ) : ?>

				<div class="row archive-posts post-box-list">
					<?php while (have_posts()) : the_post(); ?>
					<div class="col-sm-12 post-box-wrapper">
						<?php get_template_part( 'templates/post-box/classic', get_post_format() ); ?>
					</div>
					<?php endwhile; ?>
				</div>

				<?php else: ?>

				<div class="row archive-posts vw-isotope post-box-list">
					<?php while (have_posts()) : the_post(); ?>
					<div class="col-sm-6 post-box-wrapper">
						<?php get_template_part( 'templates/post-box/large-thumbnail', get_post_format() ); ?>
					</div>
					<?php endwhile; ?>
				</div>

				<?php endif; ?>

				<?php get_template_part( 'templates/pagination' ); ?>

				<?php comments_template(); ?>

			<?php else : ?>

				<h2><?php _e('Sorry, no posts were found', 'envirra') ?></h2>

			<?php endif; ?>
			
		</div>

		<aside id="page-sidebar" class="sidebar-wrapper col-sm-5 col-md-4">
			<?php get_sidebar(); ?>
		</aside>
	</div>
</div>

<?php get_footer(); ?>