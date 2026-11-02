<?php get_header(); ?>

<div id="page-wrapper" class="container">
	<div class="row">
		<div id="page-content" class="col-sm-7 col-md-8">
			<?php if ( is_author() ) : ?>
				<?php get_template_part( 'templates/about-author' ); ?>
				<hr>
			<?php else : ?>
				<?php
				$title = '';
				$supertitle = '';
				$subtitle = '';
				if ( is_category() ) {
					$curcategory = get_the_category();
					$title = single_cat_title( '', false );
					$subtitle = category_description();
					$supertitle = __( 'Posts in category', 'envirra' );

				} elseif ( is_tag() ) {
					$title = single_tag_title( '', false );
					$subtitle = tag_description();
					$supertitle = __( 'Posts tagged', 'envirra' );

				} elseif ( is_day() ) {
					$title = vw_get_archive_date();
					$supertitle = __( 'All posts on', 'envirra' );

				} elseif ( is_month() ) {
					$title = vw_get_archive_date();
					$supertitle = __( 'All posts on', 'envirra' );

				} elseif ( is_year() ) {
					$title = vw_get_archive_date();
					$supertitle = __( 'All posts on', 'envirra' );

				} elseif ( get_post_format() ) {
					$title = get_post_format();
				}
				?>
				<h1 class="page-title title title-large">
					<?php if ( ! empty( $supertitle ) ): ?>
					<span class="super-title"><?php echo $supertitle; ?></span>
					<?php endif ?>

					<?php echo $title; ?>
				</h1>
				<?php if ( ! empty( $subtitle ) ): ?>
				<h2 class="subtitle"><?php echo $subtitle; ?></h2>
				<?php endif ?>
				
				<hr>
			<?php endif; ?>
			
			<?php if (have_posts()) : ?>
				
				<?php 
				// Featured Post Slider
				$enable_slider = vw_get_option( 'archive_enable_post_slider' );
				
				if ( $enable_slider == '1' ) :
				 ?>
					<?php 
					global $wp_query;
					$args = array_merge( $wp_query->query, array(
						'post_type' => 'post',
						'orderby' => 'post_date',
						'ignore_sticky_posts' => true,
						'posts_per_page' => 5,
						'meta_query' => array(
							array(
								'key' => 'vw_post_featured', //Only posts that have review
								'value' => '1',
								'compare' => '=',
							)
						),
					) );

					query_posts( $args );
					if ( have_posts() ) : ?>
					<div class="row">
						<div class="col-sm-12 archive-slider">
							<?php get_template_part( 'templates/post-box/post-slider' ); ?>
						</div>
					</div>

					<?php endif; wp_reset_query(); ?>
				<?php endif; ?>

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

				<h2><?php _e('No entry was found', 'envirra') ?></h2>

			<?php endif; ?>
		</div>

		<aside id="page-sidebar" class="sidebar-wrapper col-sm-5 col-md-4">
			<?php get_sidebar(); ?>
		</aside>
	</div>
</div>

<?php get_footer(); ?>