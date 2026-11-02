<?php $video_oembed = get_post_meta( get_the_ID(), 'vw_post_format_video_oembed', true ); ?>
<?php if ( ! empty( $video_oembed ) ) : ?>
	<div class="post-video-wrapper">
		<?php echo wp_oembed_get( $video_oembed ); ?>
	</div>
<?php else : ?>
	<?php get_template_part( 'templates/post-formats/format' ); ?>
<?php endif; ?>