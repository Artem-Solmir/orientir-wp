<?php
$post_ID = get_the_ID();
$args = array(
	'post__not_in' => array($post_ID),  
	'posts_per_page'=> 3, 
	'ignore_sticky_posts'=> 1,
	// 'meta_key' => '_thumbnail_id', //Only posts that have featured image
);

// Find the related posts
$tags = wp_get_post_tags( $post_ID, array( 'fields' => 'ids' ) );
if ( $tags ) {
	// Find the related posts by tag
	foreach( $tags as $tag ) {
		$args['tag__in'][] = $tag;	
	}
} else {
	// Find the related posts by category when no tag.
	$cats = wp_get_post_categories( $post_ID, array('fields' => 'ids') );

	if ( ! $cats ) return;

	foreach( $cats as $cat_ID ) {
		$args['category__in'][] = $cat_ID;	
	}
}

$the_query = new WP_Query( $args );

if ( $the_query->have_posts() ) :
?>
<div class="post-related section-container clearfix">
	<h2 class="section-title"><?php _e( 'Related Posts', 'envirra' ); ?></h2>
	<div class="row">
	<?php while ( $the_query->have_posts() ) : $the_query->the_post();
		$hidden_class = '';
		if ( $the_query->current_post == $the_query->post_count-1 ) {
			$hidden_class = 'hidden-sm';
		}?>
		<div class="post-box-wrapper col-sm-6 col-md-4 <?php echo $hidden_class; ?>">
			<?php get_template_part( 'templates/post-box/headline', get_post_format() ); ?>
		</div>
	<?php endwhile; ?>
	</div>
</div>
<?php
endif;
wp_reset_postdata();
?>