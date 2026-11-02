<article class="post-<?php the_ID(); ?> post-box post-box-comment-count clearfix">
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="post-thumbnail-wrapper">
			<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__('Permalink to %s', 'envirra'), the_title_attribute('echo=0') ); ?>" rel="bookmark">
				<?php the_post_thumbnail( 'vw_square_tiny' ); ?>
			</a>
		</div>
	<?php endif; ?>

	<a class="post-count" href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__('Permalink to %s', 'envirra'), the_title_attribute('echo=0') ); ?>"><?php comments_number( '0', '1', '%' ); ?></a>

	<h3 class="title title-small header-font"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__('Permalink to %s', 'envirra'), the_title_attribute('echo=0') ); ?>" rel="bookmark"><?php the_title(); ?></a></h3>
</article>