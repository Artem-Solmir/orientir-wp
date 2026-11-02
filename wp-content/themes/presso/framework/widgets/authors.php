<?php

add_action( 'after_setup_theme', 'vw_setup_vw_widgets_init_authors' );
function vw_setup_vw_widgets_init_authors() {
	add_action( 'widgets_init', 'vw_widgets_init_authors' );
}

function vw_widgets_init_authors() {
	register_widget( 'Vw_widget_authors' );
}

class Vw_widget_authors extends WP_Widget {
	private $default = array(
		'supertitle' => '',
		'title' => '',
		'count' => '8',
		'role' => 'author',
	);

	public function __construct() {
		// widget actual processes
		parent::__construct(
	 		'vw_widget_authors', // Base ID
			'Envirra Authors', // Name
			array( 'description' => __( 'Display authors', 'envirra' ), ) // Args
		);
	}

	function widget( $args, $instance ) {
		global $wp_roles;
		extract($args);

		if ( function_exists( 'icl_t' ) ) {
			$instance['supertitle'] = icl_t( 'PRESSO Widget', $this->id.'_supertitle', $instance['supertitle'] );
			$instance['title'] = icl_t( 'PRESSO Widget', $this->id.'_title', $instance['title'] );
		}

		$instance = wp_parse_args( (array) $instance, $this->default );
		if ( ! array_key_exists( $instance['role'], $wp_roles->roles ) ) {
			$instance['role'] = $this->default['role'];
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


		$authors = get_users( array(
			'role' => $instance['role'],
			'order' => 'DESC',
			'orderby' => 'post_count',
			'number' => $count
		) );

		echo '<ul class="clearfix">';
		foreach ( $authors as $author ) : ?>
			<li>
				<a href="<?php echo get_author_posts_url( $author->ID ); ?>" title="<?php printf( esc_attr__('Permalink to %s', 'envirra'), get_the_author_meta( 'display_name', $author->ID ) ); ?>" rel="bookmark">
					<?php echo get_avatar( get_the_author_meta( 'user_email', $author->ID ), 160, '', get_the_author_meta( 'display_name', $author->ID ) ); ?>
				</a>
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
		$instance['role'] = strip_tags( $new_instance['role'] );

		if ( function_exists( 'icl_register_string' ) ) {
			icl_register_string( 'PRESSO Widget', $this->id.'_supertitle', $instance['supertitle'] );
			icl_register_string( 'PRESSO Widget', $this->id.'_title', $instance['title'] );
		}

		return $instance;
	}

	function form( $instance ) {
		global $wp_roles;

		$instance = wp_parse_args( (array) $instance, $this->default );

		$supertitle = strip_tags( $instance['supertitle'] );
		$title = strip_tags( $instance['title'] );
		$role = strip_tags( $instance['role'] );
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

		<!-- role -->
		<p>
			<label for="<?php echo $this->get_field_id('role'); ?>"><?php _e('Author Role:','envirra-backend'); ?></label>
			<select id="<?php echo $this->get_field_id('role'); ?>" name="<?php echo $this->get_field_name('role'); ?>">
				<?php foreach ( $wp_roles->roles as $slug => $role_data ) : ?>
				<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $role, $slug ); ?>><?php echo $role_data['name']; ?></option>
			<?php endforeach; ?>
			</select>
		</p>

		<!-- count -->
		<p>
			<label for="<?php echo $this->get_field_id('count'); ?>">Number of authors to show:</label>
			<input id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="text" value="<?php echo $count; ?>" size="3">
		</p>

		<?php
	}
}