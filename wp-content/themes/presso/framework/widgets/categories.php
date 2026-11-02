<?php

add_action( 'after_setup_theme', 'vw_setup_vw_widgets_init_categories' );
function vw_setup_vw_widgets_init_categories() {
	add_action( 'widgets_init', 'vw_widgets_init_categories' );
}

function vw_widgets_init_categories() {
	register_widget( 'Vw_widget_categories' );
}

class Vw_widget_categories extends WP_Widget {
	private $default = array(
		'supertitle' => '',
		'title' => '',
		'show_count' => 1,
		// 'show_description' => 1,
		'show_posts' => 1,
		'selected_cats' => array(),
	);

	public function __construct() {
		// widget actual processes
		parent::__construct(
	 		'vw_widget_categories', // Base ID
			'Envirra Categories', // Name
			array( 'description' => __( 'Display categories', 'envirra' ), ) // Args
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

		$show_count = ! empty( $instance['show_count'] ) ? '1' : '0';
		// $show_description = ! empty( $instance['show_description'] ) ? '1' : '0';
		$show_posts = ! empty( $instance['show_posts'] ) ? '1' : '0';

		echo $before_widget;
		if ( $instance['title'] ) echo $before_title . $title_html . $after_title;

		$cats_args = array(
			'pad_counts' => true,
		);

		if ( isset( $instance['selected_cats'] ) ) {
			$cats_args['include'] = $instance['selected_cats'];
		}

		$categories = get_categories( $cats_args );

		echo '<ul>';
		foreach ( $categories as $category ) :
			$category_classes = '';

			// if ( ! empty( $category->description ) ) {
			// 	$category_classes .= ' has-description';
			// }

			if ( $show_count ) {
				$category_classes .= ' show-count';
			}

			if ( empty( $title_html ) ) {
				$category_classes .= ' no-title';
			}
			?>
			<li class="category <?php echo $category_classes ?> clearfix">
				<?php if ( $show_count ) : ?>
				<div class="category-post-count label"><?php echo sprintf( "%02s", $category->count ); ?></div>
				<?php endif; ?>

				<div class="category-title title title-small header-font">
					<a href="<?php echo get_category_link( $category->cat_ID ); ?>" title="<?php printf( esc_attr__('Permalink to %s', 'envirra'), single_cat_title( '', false ) ); ?>" rel="bookmark">
						<?php echo apply_filters( 'list_cats', $category->name, $category ); ?>
					</a>
				</div>

				<?php 
				if ( $show_posts ) :
					$args = array(
						'post_type' => 'post',
						'cat' => $category->cat_ID,
						'orderby' => 'post_date',
						'ignore_sticky_posts' => true,
						'posts_per_page' => 3,
						'meta_key' => '_thumbnail_id', //Only posts that have featured image
					);

					query_posts( $args );
				 ?>
				<div class="category-featured-posts">
					<?php while( have_posts() ) : the_post(); ?>
					<div class="category-featured-post vw-imgliquid">
						<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__('Permalink to %s', 'envirra'), the_title_attribute('echo=0') ); ?>" rel="bookmark">
							<?php the_post_thumbnail( 'vw_extra_small' ); ?>
						</a>
					</div>
					<?php endwhile; ?>
				</div>
				<?php
				wp_reset_query();
				endif; ?>
			</li>
		<?php endforeach;
		echo '</ul>';

		wp_reset_postdata();
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['show_count'] = $new_instance['show_count'] ? 1 : 0; // don't change order of line, it's bug with default value replacement.
		// $instance['show_description'] = $new_instance['show_description'] ? 1 : 0; // don't change order of line, it's bug with default value replacement.
		$instance['show_posts'] = $new_instance['show_posts'] ? 1 : 0; // don't change order of line, it's bug with default value replacement.

		$new_instance = wp_parse_args( (array) $new_instance, $this->default );
		$instance['supertitle'] = strip_tags( $new_instance['supertitle'] );
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['selected_cats'] = $new_instance['selected_cats'];

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
		$show_count = $instance['show_count'] ? 'checked="checked"' : '';
		// $show_description = $instance['show_description'] ? 'checked="checked"' : '';
		$show_posts = $instance['show_posts'] ? 'checked="checked"' : '';
		$selected_cats = $instance['selected_cats'];
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

		<!-- show count -->
		<p>
			<input class="checkbox" type="checkbox" <?php echo $show_count; ?> id="<?php echo $this->get_field_id('show_count'); ?>" name="<?php echo $this->get_field_name('show_count'); ?>" />
			<label for="<?php echo $this->get_field_id('show_count'); ?>"><?php _e('Show post counts','envirra-backend'); ?></label>
		</p>

		<!-- show description -->
		<!-- <p>
			<input class="checkbox" type="checkbox" <?php echo $show_description; ?> id="<?php echo $this->get_field_id('show_description'); ?>" name="<?php echo $this->get_field_name('show_description'); ?>" />
			<label for="<?php echo $this->get_field_id('show_description'); ?>"><?php _e('Show description','envirra-backend'); ?></label>
		</p> -->

		<!-- show latest posts -->
		<p>
			<input class="checkbox" type="checkbox" <?php echo $show_posts; ?> id="<?php echo $this->get_field_id('show_posts'); ?>" name="<?php echo $this->get_field_name('show_posts'); ?>" />
			<label for="<?php echo $this->get_field_id('show_posts'); ?>"><?php _e('Show latest posts (Only post contains featured image)','envirra-backend'); ?></label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('selected_cats'); ?>"><?php _e('Select Categories (No select for all):','envirra-backend'); ?></label>
			<ul class="vw-category-checklist" id="<?php echo $this->get_field_id('selected_cats'); ?>" name="<?php echo $this->get_field_name('selected_cats'); ?>">
				<?php
				$walker = new Vw_Walker_Category_Checklist();
				$walker->set_field_name( $this->get_field_name('selected_cats') );

				wp_category_checklist( 0, 0, $selected_cats, false, $walker );
				?>
			</ul>
		</p>

		<?php
	}
}

/* -----------------------------------------------------------------------------
 * Custom Walker, duplicated from Walker_Category_Checklist
 * -------------------------------------------------------------------------- */
class Vw_Walker_Category_Checklist extends Walker  {
	var $tree_type = 'category';
	var $db_fields = array ('parent' => 'parent', 'id' => 'term_id');
	var $field_name = 'post_category';

	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent<ul class='children'>\n";
	}

	function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";
	}

	function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
		extract($args);
		if ( empty($taxonomy) )
			$taxonomy = 'category';

		$name = $this->field_name;

		$class = in_array( $category->term_id, $popular_cats ) ? ' class="popular-category"' : '';
		$output .= "\n<li id='{$taxonomy}-{$category->term_id}'$class>" . '<label class="selectit"><input value="' . $category->term_id . '" type="checkbox" name="'.$name.'[]" id="in-'.$taxonomy.'-' . $category->term_id . '"' . checked( in_array( $category->term_id, $selected_cats ), true, false ) . disabled( empty( $args['disabled'] ), false, false ) . ' /> ' . esc_html( apply_filters('the_category', $category->name )) . '</label>';
	}

	function end_el( &$output, $category, $depth = 0, $args = array() ) {
		$output .= "</li>\n";
	}

	function set_field_name( $field_name ) {
		$this->field_name = $field_name;
	}
}