<?php $attachments = get_post_meta( get_the_ID(), 'vw_post_format_gallery_images', false ); ?>
<?php if ( $attachments ) : ?>
	<div class="flexslider no-control-nav post-thumbnail-wrapper">
		<ul class="slides">
			<?php foreach( $attachments as $attachment_ID ) :
				$full_image_url = wp_get_attachment_image_src( $attachment_ID, 'full' );
				$image_caption = get_post( $attachment_ID )->post_excerpt;
			?>
				<li>
					<a class="swipebox" href="<?php echo $full_image_url[0]; ?>" title="<?php printf( esc_attr__('Permalink to image of %s', 'envirra'), the_title_attribute('echo=0') ); ?>" rel="bookmark">
						<?php echo wp_get_attachment_image( $attachment_ID, 'vw_large' ); ?>
					</a>
					<?php if ( ! empty( $image_caption ) ) : ?>
						<div class="vw-featured-image-caption-wrapper">
							<div class="vw-featured-image-caption">
								<i class="icon-entypo-camera"></i> <?php echo $image_caption; ?>
							</div>
						</div>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php else : ?>
	<?php get_template_part( 'templates/post-formats/format' ); ?>
<?php endif; ?>