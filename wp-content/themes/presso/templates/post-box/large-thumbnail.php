<article class="post-<?php the_ID(); ?> post-box fly-in animated-content post-box-large-thumbnail">
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="post-thumbnail-wrapper">
			
			<?php
			$video_url = get_post_meta( get_the_ID(), 'vw_post_format_video_oembed', true );
			if ( vw_get_option( 'blog_show_video_player_for_thumbnail' ) && 'video' == get_post_format() && ! empty( $video_url ) ) : ?>
			<a class="swipebox swipebox-video" href="<?php echo $video_url ?>" title="<?php printf( esc_attr__('Permalink to %s', 'envirra'), the_title_attribute('echo=0') ); ?>" rel="youtube">
				<span class="post-play-video"></span>
			<?php else: ?>
			<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__('Permalink to %s', 'envirra'), the_title_attribute('echo=0') ); ?>" rel="bookmark">
			<?php endif; ?>
				<?php the_post_thumbnail( 'vw_small' ); ?>
			</a>

		</div>
	<?php endif; ?>
	
	<div class="post-box-inner">
		<?php $post_date = get_the_time(get_option('date_format')); ?>
		
		<div class="post-categories clearfix">
			<?php vw_render_categories( 'label-small' ); ?>
		</div>
		
		<h3 class="title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__('Permalink to %s', 'envirra'), the_title_attribute('echo=0') ); ?>" rel="bookmark"><?php the_title(); ?></a></h3>
		<div class="post-meta header-font">
			<?php $post_date = get_the_time(get_option('date_format')); ?>
			<?php echo get_avatar( get_the_author_meta('user_email'), 32 ); ?>
			<a class="author-name" href="<?php echo get_author_posts_url(get_the_author_meta( 'ID' )); ?>" title="<?php _e('View all posts by', 'envirra'); ?> <?php the_author(); ?>"><?php the_author(); ?></a><span class="post-meta-separator">,</span>
			<a href="<?php the_permalink(); ?>" class="post-date" title="<?php printf( esc_attr__('Permalink to %s', 'envirra'), the_title_attribute('echo=0') ); ?>" rel="bookmark"><?php echo $post_date; ?></a>
		</div>
		
		<div class="post-excerpt "><?php the_excerpt(); ?></div>
		<div class="post-box-footer clearfix">
			<a class="read-more" href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__('Permalink to %s', 'envirra'), the_title_attribute('echo=0') ); ?>" rel="bookmark">
				<?php _e( 'Read more', 'envirra' ); ?> <i class="icon-entypo-right-open"></i>
			</a>
			<a class="comment-count" href="<?php the_permalink(); ?>#comments" title="<?php printf( esc_attr__('Permalink to %s', 'envirra'), the_title_attribute('echo=0') ); ?>" rel="bookmark">
				<i class="icon-entypo-comment"></i> <?php comments_number( __('0 Comments', 'envirra'), __('<span>1</span> Comment', 'envirra'), __('<span>%</span> Comments', 'envirra') );?>
			</a>
		</div>
	</div>

</article>