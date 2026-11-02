<?php get_header(); ?>
<?php global $wp_query; ?>

<div id="page-wrapper" class="container">
	<div class="row">
		<div id="page-content" class="col-sm-7 col-md-8">
			<h1 class="page-title title title-large"><?php _e( 'Search', 'envirra' ); ?></h1>

			<?php if ( '' != get_search_query() ) : ?>
			<h2 class="subtitle">
				<?php if ( $wp_query->found_posts != 1 ) {
					printf( __( '%s search results found for &ldquo;%s&rdquo;.', 'envirra' ), $wp_query->found_posts, get_search_query() ); 
				} else { 
					printf( __( '%s search result found for &ldquo;%s&rdquo;.', 'envirra' ), $wp_query->found_posts, get_search_query() ); 
				} ?></h2>
			<?php endif; ?>
			<hr>

			<?php if ( have_posts() ) : ?>
			<div class="search-result">

				<?php while ( have_posts() ) : the_post(); global $post; ?>
					<div class="search-result-post">
						<div class="tags clearfix">
							<?php vw_render_categories( 'label-small' ); ?>
						</div>

						<h2 class="title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__('Permalink to %s', 'envirra'), the_title_attribute('echo=0') ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>

						<?php get_template_part( 'templates/post-meta' ); ?>

						<?php if( get_the_excerpt() ) : ?>
						<div class="post-excerpt">
							<?php the_excerpt(); ?>
						</div>
						<?php endif; ?>
						
					</div>

				<?php endwhile; ?>

				<?php get_template_part( 'templates/pagination' ); ?>
			
			</div>
			<?php endif; ?>
		</div>
		
		<div id="page-sidebar" class="sidebar-wrapper col-sm-5 col-md-4">
			<?php get_sidebar(); ?>
		</div>
	</div>
</div>

<?php get_footer(); ?>