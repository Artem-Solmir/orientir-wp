<?php

add_action( 'after_setup_theme', 'vw_setup_vw_widgets_init_most_commented' );
function vw_setup_vw_widgets_init_most_commented() {
	add_action( 'widgets_init', 'vw_widgets_init_most_commented' );
}

function vw_widgets_init_most_commented() {
	register_widget( 'Vw_widget_most_commented' );
}

class Vw_widget_most_commented extends WP_Widget {
	private $default = array(
		'supertitle' => '',
		'title' => '',
		'count' => '5',
	);

	public function __construct() {
		// widget actual processes
		parent::__construct(
	 		'vw_widget_most_commented', // Base ID
			'Envirra Most Commented', // Name
			array( 'description' => __( 'Display most commented posts', 'envirra' ), ) // Args
		);
	}

	function widget( $args, $instance ) {
		extract($args);

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

		echo $before_widget;
		if ( $instance['title'] ) echo $before_title . $title_html . $after_title;

		global $post;

		$posts = get_posts( array(
			'post_type' => 'post',
			'order' => 'DECS',
			'orderby' => 'comment_count',
			'posts_per_page' => $count,
		) );
		echo '<div class="post-box-list">';
		foreach ( $posts as $post ) : setup_postdata( $post );
			get_template_part( 'templates/post-box/comment-count', get_post_format() );
		endforeach;
		echo '</div>';

		wp_reset_postdata();
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args( (array) $new_instance, $this->default );
		$instance['supertitle'] = strip_tags( $new_instance['supertitle'] );
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['count'] = intval( $new_instance['count'] );

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
			<label for="<?php echo $this->get_field_id('count'); ?>">Number of posts to show:</label>
			<input id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="text" value="<?php echo $count; ?>" size="3">
		</p>

		<?php
	}
}