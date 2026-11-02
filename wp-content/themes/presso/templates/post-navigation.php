<div class="post-nav section-container clearfix">
	<?php $next_post = get_next_post(); ?>
	<?php if ( ! empty( $next_post ) ) : ?>
	<a class="post-next" href="<?php echo get_permalink( $next_post->ID ); ?>">
		<i class="icon-entypo-right-open-big"></i>
		<h3 class="title title-small">
			<span class="super-title"><?php _e( 'Next post', 'envirra' ); ?></span>
			<?php echo $next_post->post_title; ?>
		</h3>
	</a>
	<?php endif; ?>

	<?php $prev_post = get_previous_post(); ?>
	<?php if ( ! empty( $prev_post ) ) : ?>
	<a class="post-previous" href="<?php echo get_permalink( $prev_post->ID ); ?>">
		<i class="icon-entypo-left-open-big"></i>
		<h3 class="title title-small">
			<span class="super-title"><?php _e( 'Previous post', 'envirra' ); ?></span>
			<?php echo $prev_post->post_title; ?>
		</h3>
	</a>
	<?php endif; ?>
</div>