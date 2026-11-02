<?php

add_action( 'after_setup_theme', 'vw_setup_vw_widgets_init_about_us' );
function vw_setup_vw_widgets_init_about_us() {
	add_action( 'widgets_init', 'vw_widgets_init_about_us' );
}

function vw_widgets_init_about_us() {
	register_widget( 'Vw_widget_about_us' );
}

class Vw_widget_about_us extends WP_Widget {
	private $default = array(
		'supertitle' => '',
		'title' => '',
		'content' => '',
	);

	public function __construct() {
		// widget actual processes
		parent::__construct(
	 		'vw_widget_about_us', // Base ID
			'Envirra About Us', // Name
			array( 'description' => __( 'Display Information About The Site', 'envirra-backend' ), ) // Args
		);
	}

	function widget( $args, $instance ) {
		extract($args);

		if ( function_exists( 'icl_t' ) ) {
			$instance['supertitle'] = icl_t( 'PRESSO Widget', $this->id.'_supertitle', $instance['supertitle'] );
			$instance['title'] = icl_t( 'PRESSO Widget', $this->id.'_title', $instance['title'] );
			$instance['content'] = icl_t( 'PRESSO Widget', $this->id.'_content', $instance['content'] );
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

		echo $before_widget;
		if ( $instance['title'] ) echo $before_title . $title_html . $after_title;
		?>
			<?php echo $instance['content']; ?>
			<div class="site-social-icons"><?php vw_render_site_social_icons(); ?></div>
		<?php

		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args( (array) $new_instance, $this->default );
		$instance['supertitle'] = strip_tags( $new_instance['supertitle'] );
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['content'] = strip_tags( $new_instance['content'] );

		if ( function_exists( 'icl_register_string' ) ) {
			icl_register_string( 'PRESSO Widget', $this->id.'_supertitle', $instance['supertitle'] );
			icl_register_string( 'PRESSO Widget', $this->id.'_title', $instance['title'] );
			icl_register_string( 'PRESSO Widget', $this->id.'_content', $instance['content'] );
		}

		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->default );

		$supertitle = strip_tags( $instance['supertitle'] );
		$title = strip_tags( $instance['title'] );
		$content = strip_tags( $instance['content'] );
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

		<!-- content -->
		<p>
			<label for="<?php echo $this->get_field_id('content'); ?>"><?php _e('Content:','envirra-backend'); ?></label>
			<textarea class="widefat" id="<?php echo $this->get_field_id('content'); ?>" name="<?php echo $this->get_field_name('content'); ?>" rows="16" cols="20"><?php echo esc_attr($content); ?></textarea>
		</p>
<?php
	}
}
