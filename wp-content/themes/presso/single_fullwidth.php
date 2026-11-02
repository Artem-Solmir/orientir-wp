<?php get_header(); ?>

<div id="page-wrapper" class="container">
	<div class="row">
		<div id="page-content" class="col-sm-12">
			<?php if (have_posts()) : ?>
				<?php while (have_posts()) : the_post(); ?>
					
					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						
						<div class="tags clearfix">
							<?php vw_render_categories( '' ); ?>
						</div>

						<h1 class="post-title title title-large entry-title"><?php the_title(); ?></h1>

						<?php if ( has_excerpt() ) : ?>
						<h2 class="post-subtitle subtitle"><?php echo get_the_excerpt() ?></h2>
						<?php endif; ?>

						<hr class="hr-thin-bottom">

						<?php if ( vw_get_option( 'blog_enable_sharebox' ) ) get_template_part( 'templates/social-share' ); ?>
						
						<?php get_template_part( 'templates/post-meta' ); ?>
						
						<?php get_template_part( 'templates/post-formats/format', get_post_format() ); ?>
						
						<div class="post-content clearfix">
							<?php get_template_part( 'templates/post-review' ); ?>
							<?php the_content(); ?>
						</div>
						
						<?php wp_link_pages( array(
								'before' => '<div class="post-page-links header-font">'.__( 'Pages:' ),
								'pagelink' => '<span>%</span>',
								'after' => '</div>',
							)
						 ); ?>

						<?php $posttags = get_the_tags();
						if ($posttags) : ?>
						<div class="post-tags clearfix">
							<span class="post-tags-title header-font"><?php _e( 'Tags:', 'envirra' ) ?></span>
							<?php foreach( $posttags as $tag ) {
								echo '<a class="label label-small label-light" href="'.get_tag_link($tag->term_id).'" title="' . esc_attr( sprintf( __( "View all posts in %s", 'envirra' ), $tag->name ) ) . '" rel="category">'.$tag->name.'</a>';
							} ?>
						</div>
						<?php endif; ?>

					</article>
				<?php endwhile; ?>

				<?php get_template_part( 'templates/pagination' ); ?>

				<?php get_template_part( 'templates/post-navigation' ); ?>
				
				<?php if ( vw_get_option( 'blog_enable_author_info' ) ) get_template_part( 'templates/about-author' ); ?>

				<?php if ( vw_get_option( 'blog_enable_related_post' ) ) get_template_part( 'templates/post-related' ); ?>
				
				<?php comments_template(); ?>

			<?php else : ?>

				<h2><?php _e('Sorry, no posts were found', 'envirra') ?></h2>

			<?php endif; ?>
		</div>
	</div>
</div>

<?php get_footer(); ?>