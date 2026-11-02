<?php
$blog_show_featured_image_single_post = vw_get_option( 'blog_show_featured_image_single_post' );
if ( '0' == $blog_show_featured_image_single_post ) return;

if ( has_post_thumbnail() ) :
	$full_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' );
	$image_caption = get_post( get_post_thumbnail_id($post->ID) )->post_excerpt;
	?>
	<div class="post-thumbnail-wrapper">
		<a class="swipebox" href="<?php echo $full_image_url[0]; ?>" title="<?php printf( esc_attr__('Permalink to image of %s', 'envirra'), the_title_attribute('echo=0') ); ?>" rel="bookmark">
			<?php the_post_thumbnail( 'vw_large' ); ?>
		</a>
		<?php if ( ! empty( $image_caption ) ) : ?>
			<div class="vw-featured-image-caption-wrapper">
				<div class="vw-featured-image-caption">
					<i class="icon-entypo-camera"></i> <?php echo $image_caption; ?>
				</div>
			</div>
		<?php endif; ?>
		
	</div>
<?php endif; ?>