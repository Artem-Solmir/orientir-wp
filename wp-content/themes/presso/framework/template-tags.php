<?php
/* -----------------------------------------------------------------------------
 * Render Comments
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vw_render_comments' ) ) {
	function vw_render_comments( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment; ?>

		<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
			<div id="comment-<?php comment_ID(); ?>" class="comment-body clearfix"> 

				<?php echo get_avatar($comment, $size = '100'); ?>

				<div class="comment-text">

					<div class="author">
						<span><?php printf( __( '%s', 'envirra'), get_comment_author_link() ) ?></span>
						<div class="date">
							<?php printf(__('%1$s at %2$s', 'envirra'), get_comment_date(),  get_comment_time() ) ?><?php edit_comment_link( __( '(Edit)', 'envirra'),'  ','' ) ?>
							<?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
						</div>  
					</div>

					<div class="text"><?php comment_text() ?></div>

					<?php if ( $comment->comment_approved == '0' ) : ?>
						<em><?php _e( 'Your comment is awaiting moderation.', 'envirra' ) ?></em>
						<br>
					<?php endif; ?>

				</div>

			</div>
		<?php
	}
}

/* -----------------------------------------------------------------------------
 * Render Categories
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vw_render_categories' ) ) {
	function vw_render_categories( $classes='' ) {
		$categories = get_the_category();
		$html = '';

		if ( is_sticky() ) {
			$html .= '<div class="label label-sticky '.$classes.'" title="'.__( 'Sticky Post', 'envirra' ).'"><i class="icon-entypo-megaphone"></i></div>';
		}

		if ( 'post' == get_post_type() ) {
			if ( '1' == get_post_meta( get_the_id(), 'vw_enable_review', true ) ) {
				$avg_score = get_post_meta( get_the_id(), 'vw_review_average_score', true );
				$html .= '<div class="label label-review '.$classes.'" title="'.__( 'Review Score', 'envirra' ).'"><i class="icon-entypo-star"></i> '.$avg_score.'</div>';
			} else { // Show post format if not a review
				if ( 'gallery' == get_post_format() ) {
					$html .= '<div class="label label-light '.$classes.'" title="'.__( 'Gallery Post', 'envirra' ).'"><i class="icon-entypo-picture"></i></div>';
				} else if ( 'video' == get_post_format() ) {
					$html .= '<div class="label label-light '.$classes.'" title="'.__( 'Video Post', 'envirra' ).'"><i class="icon-entypo-play"></i></div>';
				} else if ( 'audio' == get_post_format() ) {
					$html .= '<div class="label label-light '.$classes.'" title="'.__( 'Audio Post', 'envirra' ).'"><i class="icon-entypo-note-beamed"></i></div>';
				}
			}
		}

		if( $categories ){
			foreach( $categories as $category ) {
				$html .= '<a class="label '.$classes.'" href="'.get_category_link( $category->term_id ).'" title="' . esc_attr( sprintf( __( "View all posts in %s", 'envirra' ), $category->name ) ) . '" rel="category">'.$category->cat_name.'</a>';
			}
		}
		echo $html;
	}
}

/* -----------------------------------------------------------------------------
 * Render Site Social Icons
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vw_render_site_social_icons' ) ) {
	function vw_render_site_social_icons() {
		 $url = vw_get_option( 'social_delicious' );
		if ( ! empty( $url ) ) printf( '<a class="site-social-icon" href="%s" title="Delicious" target="_blank"><i class="icon-social-delicious"></i></a>', $url );

		$url = vw_get_option( 'social_digg' );
		if ( ! empty( $url ) ) printf( '<a class="site-social-icon" href="%s" title="Digg" target="_blank"><i class="icon-social-digg"></i></a>', $url );

		$url = vw_get_option( 'social_dribbble' );
		if ( ! empty( $url ) ) printf( '<a class="site-social-icon" href="%s" title="Dribbble" target="_blank"><i class="icon-social-dribbble"></i></a>', $url );

		$url = vw_get_option( 'social_facebook' );
		if ( ! empty( $url ) ) printf( '<a class="site-social-icon" href="%s" title="Facebook" target="_blank"><i class="icon-social-facebook"></i></a>', $url );

		$url = vw_get_option( 'social_flickr' );
		if ( ! empty( $url ) ) printf( '<a class="site-social-icon" href="%s" title="Flickr" target="_blank"><i class="icon-social-flickr"></i></a>', $url );

		$url = vw_get_option( 'social_forrst' );
		if ( ! empty( $url ) ) printf( '<a class="site-social-icon" href="%s" title="Forrst" target="_blank"><i class="icon-social-forrst"></i></a>', $url );

		$url = vw_get_option( 'social_github' );
		if ( ! empty( $url ) ) printf( '<a class="site-social-icon" href="%s" title="GitHub" target="_blank"><i class="icon-social-github"></i></a>', $url );

		$url = vw_get_option( 'social_googleplus' );
		if ( ! empty( $url ) ) printf( '<a class="site-social-icon" href="%s" title="Google+" target="_blank"><i class="icon-social-gplus"></i></a>', $url );

		$url = vw_get_option( 'social_instagram' );
		if ( ! empty( $url ) ) printf( '<a class="site-social-icon" href="%s" title="Instagram" target="_blank"><i class="icon-social-instagram"></i></a>', $url );

		$url = vw_get_option( 'social_linkedin' );
		if ( ! empty( $url ) ) printf( '<a class="site-social-icon" href="%s" title="LinkedIn" target="_blank"><i class="icon-social-linkedin"></i></a>', $url );

		$url = vw_get_option( 'social_pinterest' );
		if ( ! empty( $url ) ) printf( '<a class="site-social-icon" href="%s" title="Pinterest" target="_blank"><i class="icon-social-pinterest"></i></a>', $url );

		$url = vw_get_option( 'social_rss' );
		if ( ! empty( $url ) ) printf( '<a class="site-social-icon" href="%s" title="RSS" target="_blank"><i class="icon-social-rss"></i></a>', $url );

		$url = vw_get_option( 'social_skype' );
		if ( ! empty( $url ) ) printf( '<a class="site-social-icon" href="%s" title="Skype" target="_blank"><i class="icon-social-skype"></i></a>', $url );

		$url = vw_get_option( 'social_tumblr' );
		if ( ! empty( $url ) ) printf( '<a class="site-social-icon" href="%s" title="Tumblr" target="_blank"><i class="icon-social-tumblr"></i></a>', $url );

		$url = vw_get_option( 'social_twitter' );
		if ( ! empty( $url ) ) printf( '<a class="site-social-icon" href="%s" title="Twitter" target="_blank"><i class="icon-social-twitter"></i></a>', $url );

		$url = vw_get_option( 'social_vimeo' );
		if ( ! empty( $url ) ) printf( '<a class="site-social-icon" href="%s" title="Vimeo" target="_blank"><i class="icon-social-vimeo"></i></a>', $url );

		$url = vw_get_option( 'social_yahoo' );
		if ( ! empty( $url ) ) printf( '<a class="site-social-icon" href="%s" title="Yahoo" target="_blank"><i class="icon-social-yahoo"></i></a>', $url );

		$url = vw_get_option( 'social_youtube' );
		if ( ! empty( $url ) ) printf( '<a class="site-social-icon" href="%s" title="Youtube" target="_blank"><i class="icon-social-youtube"></i></a>', $url );
	
		$url = vw_get_option( 'social_vk' );
		if ( ! empty( $url ) ) printf( '<a class="site-social-icon" href="%s" title="Мы ВКонтакте" target="_blank"><i class="icon-social-vk"></i></a>', $url );
	
		$url = vw_get_option( 'social_ok' );
		if ( ! empty( $url ) ) printf( '<a class="site-social-icon" href="%s" title="Ok" target="_blank"><i class="icon-social-ok"></i></a>', $url );
	}
}