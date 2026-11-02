<?php

add_action( 'after_setup_theme', 'vw_setup_vw_widgets_init_review_posts' );
function vw_setup_vw_widgets_init_review_posts() {
	add_action( 'widgets_init', 'vw_widgets_init_review_posts' );
}

function vw_widgets_init_review_posts() {
	register_widget( 'Vw_widget_review_posts' );
}

class Vw_widget_review_posts extends WP_Widget {
	private $default = array(
		'supertitle' => '',
		'title' => '',
		'subtitle' => '',
		'order' => 'best',
		'count' => '5',
	);

	public function __construct() {
		// widget actual processes
		parent::__construct(
	 		'vw_widget_review_posts', // Base ID
			'Envirra Review Posts', // Name
			array( 'description' => __( 'Display review posts', 'envirra' ), ) // Args
		);
	}

	function widget( $args, $instance ) {
		extract($args);

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

		$order = $instance['order'];
		$count = intval( $instance['count'] );		

		echo $before_widget;
		if ( $instance['title'] ) echo $before_title . $title_html . $after_title . $subtitle_html;

		global $post;

		$args = array(
			'post_type' => 'post',
			'orderby' => 'post_date',
			'ignore_sticky_posts' => true,
			'posts_per_page' => $count,
			'meta_query' => array(
				array(
					'key' => 'vw_enable_review', //Only posts that have review
					'value' => '1',
					'compare' => '=',
				)
			),
		);

		if ( 'best' == $order ) {
			$args['meta_key'] = 'vw_review_average_score';
			$args['orderby'] = 'meta_value_num';
		}

		$the_query = new WP_Query( $args );

		echo '<div class="post-box-list">';

		while ( $the_query->have_posts() ) { $the_query->the_post();
				get_template_part( 'templates/post-box/small-thumbnail', get_post_format() );
		}

		echo '</div>';

		wp_reset_postdata();
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args( (array) $new_instance, $this->default );
		$instance['supertitle'] = strip_tags( $new_instance['supertitle'] );
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['subtitle'] = strip_tags( $new_instance['subtitle'] );
		$instance['order'] = strip_tags( $new_instance['order'] );
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
		$order = strip_tags( $instance['order'] );
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

		<!-- order -->
		<p>
			<label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('Order by:','envirra-backend'); ?></label>
			<select id="<?php echo $this->get_field_id('order'); ?>" name="<?php echo $this->get_field_name('order'); ?>">
				<option value="best" <?php selected( $order, 'best' ); ?>><?php _e( 'Best score', 'envirra-backend' ) ?></option>
				<option value="latest" <?php selected( $order, 'latest' ); ?>><?php _e( 'Latest review', 'envirra-backend' ) ?></option>
			</select>
		</p>

		<!-- count -->
		<p>
			<label for="<?php echo $this->get_field_id('count'); ?>">Number of posts to show:</label>
			<input id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="text" value="<?php echo $count; ?>" size="3">
		</p>

		<?php
	}
}