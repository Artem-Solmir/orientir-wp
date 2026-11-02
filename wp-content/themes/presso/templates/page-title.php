<?php if ( is_page() && ! is_front_page() || ( function_exists( 'bp_current_component' ) && bp_current_component() )  ) : ?>
<h1 class="page-title title title-large"><?php the_title(); ?></h1>
<?php 
$page_subtitle = trim( get_post_meta( get_the_ID(), 'vw_page_subtitle', true ) );
if ( ! empty( $page_subtitle ) ) : ?>
<h2 class="subtitle"><?php echo $page_subtitle; ?></h2>
<?php endif; ?>
<hr>
<?php endif; ?>