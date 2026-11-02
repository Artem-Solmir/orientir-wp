<?php

add_action( 'after_setup_theme', 'vw_setup_vw_widgets_init_latest_comments' );
function vw_setup_vw_widgets_init_latest_comments() {
	add_action( 'widgets_init', 'vw_widgets_init_latest_comments' );
}

function vw_widgets_init_latest_comments() {
	register_widget( 'Vw_widget_latest_comments' );
}

class Vw_widget_latest_comments extends WP_Widget {
	private $default = array(
		'supertitle' => '',
		'title' => '',
		'count' => '5',
		'excerpt_length' => '30',
	);

	public function __construct() {
		// widget actual processes
		parent::__construct(
	 		'vw_widget_latest_comments', // Base ID
			'Envirra Latest Comments', // Name
			array( 'description' => __( 'Display latest comments', 'envirra' ), ) // Args
		);
	}

	function widget( $args, $instance ) {
		$instance = wp_parse_args( $instance, $this->default );
		extract( $args );

		if ( function_exists( 'icl_t' ) ) {
			$instance['supertitle'] = icl_t( 'PRESSO Widget', $this->id.'_supertitle', $instance['supertitle'] );
			$instance['title'] = icl_t( 'PRESSO Widget', $this->id.'_title', $instance['title'] );
		}

		$supertitle_html = '';
		if ( ! empty( $instance['supertitle'] ) ) {
			$supertitle_html = sprintf( __( '<span class="super-title">%s</span>', 'envirra' ), $instance['supertitle'] );
		}

		$title_html = '';
		if ( ! empty( $instance['title'] ) ) {
			$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base);
			$title_html = $supertitle_html.$title;
		}

		$count = intval( $instance['count'] );		
		$excerpt_length = intval( $instance['excerpt_length'] );

		echo $before_widget;
		if ( $instance['title'] ) echo $before_title . $title_html . $after_title;

		global $post;

		$comments = get_comments( array(
			'status' => 'approve',
			'number' => $count,
		) );

		echo '<ul>';
		foreach ( $comments as $comment ) : ?>
			<li>
				<a href="<?php echo get_permalink( $comment->comment_post_ID ); ?>" title="<?php printf( esc_attr__('Permalink to %s', 'envirra'), get_the_title( $comment->comment_post_ID ) ); ?>" rel="bookmark">
					<?php echo get_avatar( $comment->comment_author_email, 30 ); ?>
				</a>

				<div class="comment-meta clearfix">
					<div class="comment-author header-font">
						<?php echo $comment->comment_author ?>
					</div>
					<div class="commented-post">
						&rarr;
						<a href="<?php echo get_permalink( $comment->comment_post_ID ); ?>" title="<?php printf( esc_attr__('Permalink to %s', 'envirra'), get_the_title( $comment->comment_post_ID ) ); ?>" rel="bookmark">
							<?php echo get_the_title( $comment->comment_post_ID ); ?>
						</a>
					</div>
					<?php if ( $excerpt_length > 0 ) : ?>
					<div class="comment-content"><?php echo wp_trim_words( $comment->comment_content, $excerpt_length ) ?></div>
					<?php endif; ?>
				</div>
			</li>
		<?php endforeach;
		echo '</ul>';

		wp_reset_postdata();
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args( (array) $new_instance, $this->default );
		$instance['supertitle'] = strip_tags( $new_instance['supertitle'] );
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['count'] = intval( $new_instance['count'] );
		$instance['excerpt_length'] = intval( $new_instance['excerpt_length'] );

		if ( function_exists( 'icl_register_string' ) ) {
			icl_register_string( 'PRESSO Widget', $this->id.'_supertitle', $instance['supertitle'] );
			icl_register_string( 'PRESSO Widget', $this->id.'_title', $instance['title'] );
		}

		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->default );

		$supertitle = strip_tags( $instance['supertitle'] );
		$title = strip_tags( $instance['title'] );
		$count = $instance['count'];
		$excerpt_length = $instance['excerpt_length'];
		?>

		<!-- super title -->
		<p>
			<label for="<?php echo $this->get_field_id('supertitle'); ?>"><?php _e('Super-title:','envirra-backend'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('supertitle'); ?>" name="<?php echo $this->get_field_name('supertitle'); ?>" type="text" value="<?php echo esc_attr($supertitle); ?>" />
		</p>

		<!-- title -->
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','envirra-backend'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>

		<!-- count -->
		<p>
			<label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Number of comments to show:','envirra-backend'); ?></label>
			<input id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="text" value="<?php echo $count; ?>" size="3">
		</p>

		<!-- excerpt_length -->
		<p>
			<label for="<?php echo $this->get_field_id('excerpt_length'); ?>"><?php _e('Excerpt length (0 for disabled):','envirra-backend'); ?></label>
			<input id="<?php echo $this->get_field_id('excerpt_length'); ?>" name="<?php echo $this->get_field_name('excerpt_length'); ?>" type="text" value="<?php echo $excerpt_length; ?>" size="3">
		</p>

		<?php
	}
}