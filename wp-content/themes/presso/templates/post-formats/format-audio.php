<?php $audio_oembed = get_post_meta( get_the_ID(), 'vw_post_format_audio_oembed', true ); ?>
<?php if ( ! empty( $audio_oembed ) ) : ?>
	<div class="post-audio-wrapper">
		<?php echo wp_oembed_get( $audio_oembed ); ?>
	</div>
<?php else : ?>
	<?php get_template_part( 'templates/post-formats/format' ); ?>
<?php endif; ?>