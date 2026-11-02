<?php
$show_posts = vw_get_option( 'blog_show_posts_at_top' );

if ( 'hidden' == $show_posts ) return;

global $wp_query;
global $post;

$args = array(
	'post_type' => 'post',
	'order' => 'DESC',
	'orderby' => 'post_date',
	'posts_per_page' => 8,
	'ignore_sticky_posts' => true,
);

if ( 'random' == $show_posts ) {
	$args['orderby'] = 'rand';
}

$the_query = new WP_Query( $args );
?>
<div class="top-posts">
	<div class="container">
		<div class="row">
			<a href="#" class="carousel-nav-prev"></a>
			<a href="#" class="carousel-nav-next"></a>
				<div class="top-posts-inner">
				<?php while ( $the_query->have_posts() ) : $the_query->the_post();
					$hidden_class = '';
					if ( $the_query->current_post == $the_query->post_count-1 ) {
						$hidden_class = 'hidden-sm';
					}
					?>
					<div class="post-box-wrapper  <?php echo $hidden_class; ?>">
						<?php get_template_part( 'templates/post-box/headline', get_post_format() ); ?>
					</div>
				<?php endwhile; ?>
				</div>
		</div>
	</div>
</div>
<?php wp_reset_postdata(); ?>