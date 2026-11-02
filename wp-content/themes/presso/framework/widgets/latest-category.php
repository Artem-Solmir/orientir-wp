<?php

add_action( 'after_setup_theme', 'vw_setup_vw_widgets_init_latest_category' );
function vw_setup_vw_widgets_init_latest_category() {
	add_action( 'widgets_init', 'vw_widgets_init_latest_category' );
}

function vw_widgets_init_latest_category() {
	register_widget( 'Vw_widget_latest_category' );
}

class Vw_widget_latest_category extends WP_Widget {
	private $default = array(
		'supertitle' => '',
		'title' => '',
		'subtitle' => '',
		'category' => '1',
		'format' => '',
		'thumbnail' => 'large-small',
		'count' => '5',
	);

	public function __construct() {
		// widget actual processes
		parent::__construct(
	 		'vw_widget_latest_category', // Base ID
			'Envirra Latest By Category', // Name
			array( 'description' => __( 'Display latest posts from category', 'envirra' ), ) // Args
		);
	}

	function widget( $args, $instance ) {
		extract($args);
		$instance = wp_parse_args( $instance, $this->default );

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
		$thumbnail = $instance['thumbnail'];
		$format = $instance['format'];
		$count = intval( $instance['count'] );

		echo $before_widget;
		if ( $instance['title'] ) echo $before_title . $title_html . $after_title . $subtitle_html;

		global $post;

		$args = array(
			'post_type' => 'post',
			'orderby' => 'post_date',
			'ignore_sticky_posts' => true,
			'posts_per_page' => $count,
		);

		if ( $category > 0 ) {
			$args['cat'] = $category;
		}

		if ( ! empty( $format ) ) {
			$args['tax_query'] = array(
					array(
						'taxonomy' => 'post_format',
						'field'    => 'slug',
						'terms'    => array( 'post-format-'.$format ),
					),
				);
		}

		$the_query = new WP_Query( $args );

		echo '<div class="post-box-list">';

		if ( 'large' == $thumbnail || 'small' == $thumbnail ) {
			while ( $the_query->have_posts() ) { $the_query->the_post();
				get_template_part( 'templates/post-box/'.$thumbnail.'-thumbnail', get_post_format() );
			}
		} else {
			if ( $the_query->have_posts() ) { $the_query->the_post();
				get_template_part( 'templates/post-box/large-thumbnail', get_post_format() );
			}

			while ( $the_query->have_posts() ) { $the_query->the_post();
				get_template_part( 'templates/post-box/small-thumbnail', get_post_format() );
			}
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
		$instance['category'] = strip_tags( $new_instance['category'] );
		$instance['format'] = strip_tags( $new_instance['format'] );
		$instance['thumbnail'] = strip_tags( $new_instance['thumbnail'] );
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
		$format = strip_tags( $instance['format'] );
		$thumbnail = strip_tags( $instance['thumbnail'] );
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
			<label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Show posts in category:','envirra-backend'); ?></label>
			<?php wp_dropdown_categories( array(
				'name' => $this->get_field_name('category'),
				'id' => $this->get_field_name('category'),
				'selected' => $category,
				'show_option_all' => __( 'All', 'envirra' ),
				'hide_empty' => 0,
				'hierarchical' => true )
			); ?>
		</p>

		<!-- format -->
		<p>
			<label for="<?php echo $this->get_field_id('format'); ?>"><?php _e('Format:','envirra-backend'); ?></label>
			<select id="<?php echo $this->get_field_id('format'); ?>" name="<?php echo $this->get_field_name('format'); ?>">
				<option value="" <?php selected( $format, '' ); ?>><?php _e( 'All Formats', 'envirra-backend' ) ?></option>
				<option value="audio" <?php selected( $format, 'audio' ); ?>><?php _e( 'Audio', 'envirra-backend' ) ?></option>
				<option value="gallery" <?php selected( $format, 'gallery' ); ?>><?php _e( 'Gallery', 'envirra-backend' ) ?></option>
				<option value="video" <?php selected( $format, 'video' ); ?>><?php _e( 'Video', 'envirra-backend' ) ?></option>
			</select>
		</p>

		<!-- thumbnail -->
		<p>
			<label for="<?php echo $this->get_field_id('thumbnail'); ?>"><?php _e('Thumbnail size:','envirra-backend'); ?></label>
			<select id="<?php echo $this->get_field_id('thumbnail'); ?>" name="<?php echo $this->get_field_name('thumbnail'); ?>">
				<option value="small" <?php selected( $thumbnail, 'small' ); ?>><?php _e( 'Small Thumbnail', 'envirra-backend' ) ?></option>
				<option value="large" <?php selected( $thumbnail, 'large' ); ?>><?php _e( 'Large Thumbnail', 'envirra-backend' ) ?></option>
				<option value="large-small" <?php selected( $thumbnail, 'large-small' ); ?>><?php _e( 'Large &amp; Small Thumbnail', 'envirra-backend' ) ?></option>
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