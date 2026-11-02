<?php

add_action( 'after_setup_theme', 'vw_setup_vw_widgets_init_image_banner' );
function vw_setup_vw_widgets_init_image_banner() {
	add_action( 'widgets_init', 'vw_widgets_init_image_banner' );
}

function vw_widgets_init_image_banner() {
	register_widget( 'Vw_widget_image_banner' );
}

class Vw_widget_image_banner extends WP_Widget {
	private $default = array(
		'imgurl' => '',
		'linkurl' => '',
	);

	public function __construct() {
		// widget actual processes
		parent::__construct(
	 		'vw_widget_image_banner', // Base ID
			'Envirra Image Banner', // Name
			array( 'description' => __( 'Display image banner 300x300, 300x250 or 250x250', 'envirra-backend' ), ) // Args
		);
	}

	function widget( $args, $instance ) {
		extract($args);
		
		echo $before_widget;
		?>
			<a class="image-banner-link" href="<?php echo esc_attr( $instance['linkurl'] ); ?>">
				<img class="image-banner" src="<?php echo esc_attr( $instance['imgurl'] ); ?>" alt="">
			</a>
		<?php

		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args( (array) $new_instance, $this->default );
		$instance['imgurl'] = strip_tags( $new_instance['imgurl'] );
		$instance['linkurl'] = strip_tags( $new_instance['linkurl'] );

		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->default );

		$imgurl = strip_tags( $instance['imgurl'] );
		$linkurl = strip_tags( $instance['linkurl'] );
?>
		<!-- image url -->
		<p>
			<label for="<?php echo $this->get_field_id('imgurl'); ?>"><?php _e('Image Url:','envirra-backend'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('imgurl'); ?>" name="<?php echo $this->get_field_name('imgurl'); ?>" type="text" value="<?php echo esc_attr($imgurl); ?>" />
		</p>

		<!-- link url -->
		<p>
			<label for="<?php echo $this->get_field_id('linkurl'); ?>"><?php _e('Link Url:','envirra-backend'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('linkurl'); ?>" name="<?php echo $this->get_field_name('linkurl'); ?>" type="text" value="<?php echo esc_attr($linkurl); ?>" />
		</p>
<?php
	}
}
