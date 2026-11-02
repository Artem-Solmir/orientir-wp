<article class="post-<?php the_ID(); ?> post-box post-box-poster">
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="post-thumbnail-wrapper vw-imgliquid">
			<div class="post-box-overlay-content">
				<?php $post_date = get_the_time(get_option('date_format')); ?>
				<h3 class="title">
					<span class="super-title"><?php echo $post_date; ?></span>
					<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__('Permalink to %s', 'envirra'), the_title_attribute('echo=0') ); ?>" rel="bookmark"><?php the_title(); ?></a>
				</h3>
				<a class="read-more label label-large" href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__('Permalink to %s', 'envirra'), the_title_attribute('echo=0') ); ?>" rel="bookmark">
					<?php _e( 'Read more', 'envirra' ); ?> <i class="icon-entypo-right-open"></i>
				</a>
				<div class="clearfix"></div>
			</div>
			
			<?php the_post_thumbnail( 'vw_medium' ); ?>
		</div>
	<?php endif; ?>

</article>