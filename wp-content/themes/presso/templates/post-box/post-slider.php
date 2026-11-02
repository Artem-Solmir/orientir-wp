<div class="flexslider no-control-nav post-slider">
	<ul class="slides">

	<?php while( have_posts() ) : the_post(); ?>
		<li>
			<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__('Permalink to %s', 'envirra'), the_title_attribute('echo=0') ); ?>" rel="bookmark">
				<div class="post-thumbnail-wrapper">
					<?php the_post_thumbnail( 'vw_large' ); ?>
				</div>

				<div class="post-box-inner">
					<?php $post_date = get_the_time(get_option('date_format')); ?>

					<h3 class="title">
						<span class="super-title"><?php echo $post_date; ?></span>
						<span><?php the_title(); ?></span>
					</h3>
					<span class="read-more label label-large">
						<?php _e( 'Read more', 'envirra' ); ?> <i class="icon-entypo-right-open"></i>
					</span>
				</div>
			</a>
		</li>
	<?php endwhile; ?>
	
	</ul>
</div>