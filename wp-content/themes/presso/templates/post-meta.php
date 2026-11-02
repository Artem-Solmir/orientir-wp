<div class="post-meta header-font">
	<?php 
	if ( function_exists( 'coauthors_posts_links' ) ) {
		coauthors_posts_links( null, '<span class="author-separator">and</span>' );
	} else { ?>
		<?php echo get_avatar( get_the_author_meta('user_email'), 48 ); ?>
		<a class="author-name vcard author" href="<?php echo get_author_posts_url(get_the_author_meta( 'ID' )); ?>" title="<?php _e('View all posts by', 'envirra'); ?> <?php the_author(); ?>"><span class="fn"><?php the_author(); ?></span></a>
	<?php }; ?>

	<?php $post_date = get_the_time(get_option('date_format')); ?>
	<span class="post-meta-separator">&mdash;</span> <a href="<?php the_permalink(); ?>" class="post-date updated" title="<?php printf( esc_attr__('Permalink to %s', 'envirra'), the_title_attribute('echo=0') ); ?>" rel="bookmark"><?php echo $post_date; ?></a>
</div>