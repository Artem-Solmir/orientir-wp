<article class="post-<?php the_ID(); ?> post-box post-box-large-thumbnail">
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
		
		<h3 class="title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__('Permalink to %s', 'envirra'), the_title_attribute('echo=0') ); ?>" rel="bookmark"><?php the_title(); ?></a></h3>
		<div class="post-meta header-font">
			<?php $post_date = get_the_time(get_option('date_format')); ?>
			<?php global $post; ?>
			<?php echo get_avatar( get_the_author_meta('user_email', $post->post_author), 32 ); ?>
			<a class="author-name" href="<?php echo get_author_posts_url(get_the_author_meta( 'ID', $post->post_author )); ?>" title="<?php _e('View all posts by', 'envirra'); ?> <?php echo esc_attr( get_the_author_meta( 'display_name', $post->post_author ) ); ?>"><?php the_author_meta( 'display_name', $post->post_author ); ?></a><span class="post-meta-separator">,</span>
			<a href="<?php the_permalink(); ?>" class="post-date" title="<?php printf( esc_attr__('Permalink to %s', 'envirra'), the_title_attribute('echo=0') ); ?>" rel="bookmark"><?php echo $post_date; ?></a>
		</div>
	</div>

</article>