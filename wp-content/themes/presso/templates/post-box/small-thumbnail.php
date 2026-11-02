<article class="post-<?php the_ID(); ?> post-box fly-in animated-content post-box-small-thumbnail clearfix">
	
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="post-thumbnail-wrapper">
				<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__('Permalink to %s', 'envirra'), the_title_attribute('echo=0') ); ?>" rel="bookmark">
					<?php the_post_thumbnail( 'vw_square_tiny' ); ?>
				</a>
			</div>
		<?php endif; ?>

		<h3 class="title title-small"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__('Permalink to %s', 'envirra'), the_title_attribute('echo=0') ); ?>" rel="bookmark"><?php the_title(); ?></a></h3>
		<div class="post-meta header-font">
			<?php $post_date = get_the_time(get_option('date_format')); ?>
			<a class="author-name" href="<?php echo get_author_posts_url(get_the_author_meta( 'ID' )); ?>" title="<?php _e('View all posts by', 'envirra'); ?> <?php the_author(); ?>"><?php the_author(); ?></a><span class="post-meta-separator">,</span>
			<a href="<?php the_permalink(); ?>" class="post-date" title="<?php printf( esc_attr__('Permalink to %s', 'envirra'), the_title_attribute('echo=0') ); ?>" rel="bookmark"><?php echo $post_date; ?></a>
		</div>
		
		<div class="post-categories clearfix">
			<?php vw_render_categories( 'label-small' ); ?>
		</div>
		
</article>