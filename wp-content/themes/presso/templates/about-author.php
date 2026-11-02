<?php
if ( is_single() ) {
	global $post;
	$curauth = get_userdata( $post->post_author );
} else {
	$curauth = (get_query_var('author_name')) ? get_user_by('slug', get_query_var('author_name')) : get_userdata(get_query_var('author'));
}
?>
<div class="about-author section-container clearfix vcard author">
	<?php echo get_avatar( get_the_author_meta( 'user_email', $curauth->ID ), 200, '', get_the_author_meta( 'display_name', $curauth->ID ) ); ?>

	<div class="about-author-info">
		<h3 class="author-name title title-medium">
			<span class="super-title"><?php _e( 'About the Author', 'envirra' ); ?></span>
			<span class="fn"><?php echo $curauth->display_name; ?></span>
		</h3>

		<div class="author-bio"><?php echo $curauth->user_description; ?></div>
		<div class="author-socials">
			<?php if ( get_the_author_meta( 'vw_user_twitter', $curauth->ID ) != '' ) : ?>
			<a href="<?php echo esc_url( get_the_author_meta( 'vw_user_twitter', $curauth->ID ) ) ?>" rel="author"><i class="icon-social-twitter icon-small"></i> Twitter</a>
			<?php endif; ?>

			<?php if ( get_the_author_meta( 'vw_user_facebook', $curauth->ID ) != '' ) : ?>
			<a href="<?php echo esc_url( get_the_author_meta( 'vw_user_facebook', $curauth->ID ) ) ?>" rel="author"><i class="icon-social-facebook icon-small"></i> Facebook</a>
			<?php endif; ?>

			<?php if ( get_the_author_meta( 'vw_user_google', $curauth->ID ) != '' ) : ?>
			<a href="<?php echo esc_url( get_the_author_meta( 'vw_user_google', $curauth->ID ) ) ?>" rel="author"><i class="icon-social-gplus icon-small"></i> Google</a>
			<?php endif; ?>

			<?php if ( get_the_author_meta( 'vw_user_pinterest', $curauth->ID ) != '' ) : ?>
			<a href="<?php echo esc_url( get_the_author_meta( 'vw_user_pinterest', $curauth->ID ) ) ?>" rel="author"><i class="icon-social-pinterest icon-small"></i> Pinterest</a>
			<?php endif; ?>
		</div>
	</div>
</div>