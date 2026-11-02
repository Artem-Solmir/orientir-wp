<?php
/*
Template Name: Page Composer
*/
get_header(); ?>

<?php if ( have_posts() ) : the_post(); ?>
	
<div id="page-wrapper" class="container">
	<?php vwpc_render(); ?>
</div>
<?php endif; ?>

<?php get_footer(); ?>