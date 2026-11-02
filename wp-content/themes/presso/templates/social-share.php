<div class="social-share">
	<span class="header-font"><?php _e( 'SHARE ON:', 'envirra' ); ?></span>

	<?php if ( vw_get_option( 'sharebox_facebook' ) ) : ?>
	<a class="social-facebook" href="http://www.facebook.com/sharer.php?u=<?php echo urlencode( get_permalink() ); ?>&amp;t=<?php echo esc_url( get_the_title() ); ?>" title="<?php esc_attr_e( 'Share on Facebook', 'envirra' ) ?>" target="_blank"><i class="icon-social-facebook"></i></a>
	<?php endif; ?>

	<?php if ( vw_get_option( 'sharebox_twitter' ) ) : ?>
	<a class="social-twitter" href="https://twitter.com/home?status=<?php echo esc_url( get_the_title().' '.get_permalink() ); ?>" title="<?php esc_attr_e( 'Share on Twitter', 'envirra' ) ?>" target="_blank"><i class="icon-social-twitter"></i></a>
	<?php endif; ?>

	<?php if ( vw_get_option( 'sharebox_googleplus' ) ) : ?>
	<a class="social-googleplus" href="https://plus.google.com/share?url=<?php echo urlencode( get_permalink() ); ?>" title="<?php esc_attr_e( 'Share on Google+', 'envirra' ) ?>" target="_blank"><i class="icon-social-gplus"></i></a>
	<?php endif; ?>

	<?php if ( vw_get_option( 'sharebox_pinterest' ) ) : ?>
	<?php
	$media = '';
	if ( has_post_thumbnail() ) {
		$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
		$media = '&amp;media='.$thumbnail[0];
	}

	$description = '';
	if ( get_the_title() ) $description = '&amp;description='.esc_url( get_the_title() );
	?>
	<a class="social-pinterest" href="http://pinterest.com/pin/create/button/?url=<?php echo urlencode( get_permalink() ) . $media . $description; ?>" title="<?php esc_attr_e( 'Share on Pinterest', 'envirra' ) ?>" target="_blank"><i class="icon-social-pinterest"></i></a>
	<?php endif; ?>

	<?php if ( vw_get_option( 'sharebox_tumblr' ) ) : ?>
	<a class="social-tumblr" href="http://www.tumblr.com/share/link?url=<?php echo urlencode( get_permalink() ); ?>&amp;name=<?php echo esc_url(the_title('', '', false)) ?>&amp;description=<?php echo urlencode(the_title('', '', false)) ?>" title="<?php esc_attr_e( 'Share on Tumblr', 'envirra' ) ?>" target="_blank"><i class="icon-social-tumblr"></i></a>
	<?php endif; ?>

	<?php if ( vw_get_option( 'sharebox_linkedin' ) ) : ?>
	<a class="social-linkedin" href="http://linkedin.com/shareArticle?mini=true&amp;url=<?php echo urlencode( get_permalink() );?>&amp;title=<?php echo esc_url( get_the_title() );?>" title="<?php esc_attr_e( 'Share on LinkedIn', 'envirra' ) ?>" target="_blank"><i class="icon-social-linkedin"></i></a>
	<?php endif; ?>

	<?php if ( vw_get_option( 'sharebox_delicious' ) ) : ?>
	<a class="social-delicious" href="http://www.delicious.com/post?v=2&amp;url=<?php echo urlencode( get_permalink() ); ?>&amp;notes=&amp;tags=&amp;title=<?php echo esc_url(the_title('', '', false)) ?>" title="<?php esc_attr_e( 'Share on Delicious', 'envirra' ) ?>" target="_blank"><i class="icon-social-delicious"></i></a>
	<?php endif; ?>

	<?php if ( vw_get_option( 'sharebox_reddit' ) ) : ?>
	<a class="social-reddit" href="http://www.reddit.com/submit?url=<?php echo urlencode( get_permalink() ); ?>&amp;title=<?php echo esc_url(the_title('', '', false)) ?>" title="<?php esc_attr_e( 'Share on Reddit', 'envirra' ) ?>" target="_blank"><i class="icon-social-reddit"></i></a>
	<?php endif; ?>

	<?php if ( vw_get_option( 'sharebox_digg' ) ) : ?>
	<a class="social-digg" href="http://digg.com/submit?phase=2&amp;url=<?php echo urlencode( get_permalink() ); ?>&amp;bodytext=&amp;tags=&amp;title=<?php echo esc_url(the_title('', '', false)) ?>" target="_blank" title="<?php esc_attr_e( 'Share on Digg', 'envirra' ) ?>"><i class="icon-social-digg"></i></a>
	<?php endif; ?>

	<?php if ( vw_get_option( 'sharebox_email' ) ) : ?>
	<a class="social-email" href="mailto:?subject=<?php echo esc_url(the_title('', '', false)) ?>&amp;body=<?php echo urlencode( get_permalink() ); ?>" title="<?php esc_attr_e( 'Share on E-Mail', 'envirra' ) ?>" target="_blank"><i class="icon-social-email"></i></a>
	<?php endif; ?>
</div>