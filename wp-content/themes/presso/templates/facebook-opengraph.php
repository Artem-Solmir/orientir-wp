<!-- Facebook integration -->
<?php if ( have_posts() ): while( have_posts() ): the_post(); endwhile; endif;?>  

<meta property="og:site_name" content="<?php bloginfo('name'); ?>">

<?php if ( is_single() ) : ?>
<meta property="og:url" content="<?php the_permalink(); ?>"/>  
<meta property="og:type" content="article" />
<meta property="og:title" content="<?php single_post_title(''); ?>" />  
<meta property="og:description" content="<?php echo esc_attr( wp_strip_all_tags( get_the_excerpt(), true ) ); ?>">
<?php if ( has_post_thumbnail() ) : $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_id() ), 'full' );	?>
	<meta property="og:image" content="<?php echo $thumbnail[0] ?>" />
<?php endif; ?>

<?php else : ?>
<meta property="og:url" content="<?php echo home_url(); ?>"/>  
<meta property="og:type" content="website" />
<meta property="og:title" content="<?php echo esc_attr( wp_strip_all_tags( wp_title('', false) ) ); ?>">
<meta property="og:description" content="<?php echo esc_attr( wp_strip_all_tags( bloginfo('description') ) ); ?>">

<?php endif; ?>

<?php if ( have_posts() ): rewind_posts(); endif;?>