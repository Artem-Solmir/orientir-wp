<?php

add_action( 'after_setup_theme', 'vw_setup_vw_widgets_init_post_slider' );
function vw_setup_vw_widgets_init_post_slider() {
	add_action( 'widgets_init', 'vw_widgets_init_post_slider' );
}

function vw_widgets_init_post_slider() {
	register_widget( 'Vw_widget_post_slider' );
}

class Vw_widget_post_slider extends WP_Widget {
	private $default = array(
		'supertitle' => '',
		'title' => '',
		'subtitle' => '',
		'category' => '0',
		'count' => '5',
	);

	public function __construct() {
		// widget actual processes
		parent::__construct(
	 		'vw_widget_post_slider', // Base ID
			'Envirra Post Slider', // Name
			array( 'description' => __( 'Display latest posts as slider', 'envirra' ), ) // Args
		);
	}

	function widget( $args, $instance ) {
		extract($args);
		$instance = wp_parse_args( (array) $instance, $this->default );

		if ( function_exists( 'icl_t' ) ) {
			$instance['supertitle'] = icl_t( 'PRESSO Widget', $this->id.'_supertitle', $instance['supertitle'] );
			$instance['title'] = icl_t( 'PRESSO Widget', $this->id.'_title', $instance['title'] );
			$instance['subtitle'] = icl_t( 'PRESSO Widget', $this->id.'_subtitle', $instance['subtitle'] );
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
		$subtitle_html = '';
		if ( ! empty( $instance['subtitle'] ) ) {
			$subtitle_html = sprintf( __( '<p class="section-description">%s</p>', 'envirra' ), $instance['subtitle'] );
		}

		$category = intval( $instance['category'] );		
		$count = intval( $instance['count'] );

		echo $before_widget;
		if ( $instance['title'] ) echo $before_title . $title_html . $after_title . $subtitle_html;

		global $post;

		$args = array(
			'post_type' => 'post',
			'cat' => $category,
			'orderby' => 'post_date',
			'ignore_sticky_posts' => true,
			'posts_per_page' => $count,
		);

		query_posts( $args );

		echo '<div class="post-box-list">';

		get_template_part( 'templates/post-box/post-slider' );

		echo '</div>';

		wp_reset_query();
		wp_reset_postdata();
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args( (array) $new_instance, $this->default );
		$instance['supertitle'] = strip_tags( $new_instance['supertitle'] );
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['subtitle'] = strip_tags( $new_instance['subtitle'] );
		$instance['category'] = strip_tags( $new_instance['category'] );
		$instance['count'] = intval( $new_instance['count'] );

		if ( function_exists( 'icl_register_string' ) ) {
			icl_register_string( 'PRESSO Widget', $this->id.'_supertitle', $instance['supertitle'] );
			icl_register_string( 'PRESSO Widget', $this->id.'_title', $instance['title'] );
			icl_register_string( 'PRESSO Widget', $this->id.'_subtitle', $instance['subtitle'] );
		}
		
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->default );

		$supertitle = strip_tags( $instance['supertitle'] );
		$title = strip_tags( $instance['title'] );
		$subtitle = strip_tags( $instance['subtitle'] );
		$category = strip_tags( $instance['category'] );
		$count = intval( $instance['count'] );
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

		<!-- sub title -->
		<p>
			<label for="<?php echo $this->get_field_id('subtitle'); ?>"><?php _e('Sub-title:','envirra-backend'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('subtitle'); ?>" name="<?php echo $this->get_field_name('subtitle'); ?>" type="text" value="<?php echo esc_attr($subtitle); ?>" />
		</p>

		<!-- category -->
		<p>
			<label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Show posts from category:','envirra-backend'); ?></label>
			<?php wp_dropdown_categories( array(
				'name' => $this->get_field_name('category'),
				'id' => $this->get_field_name('category'),
				'selected' => $category,
				'show_option_all' => __( 'All', 'envirra' ),
				'hide_empty' => 0,
				'hierarchical' => true )
			); ?>
		</p>

		<!-- count -->
		<p>
			<label for="<?php echo $this->get_field_id('count'); ?>">Number of posts to show:</label>
			<input id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="text" value="<?php echo $count; ?>" size="3">
		</p>

		<?php
	}
}