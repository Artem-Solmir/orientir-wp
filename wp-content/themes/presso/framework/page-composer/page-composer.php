<?php
define( 'VW_CONST_COMPOSER_TRANSLATE_SLUG', 'PRESSO Composer' );

add_action( 'after_setup_theme', 'vw_setup_page_composer' );
function vw_setup_page_composer() {
	add_action( 'admin_enqueue_scripts', 'vwpc_init_sections' );
	add_action( 'admin_enqueue_scripts', 'vwpc_admin_enqueue_scripts', 9 );
	add_action( 'edit_form_after_title', 'vwpc_render_editor' );
	add_action( 'save_post', 'vwpc_save_page' );
}

/* -----------------------------------------------------------------------------
 * Init Available Sections
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vwpc_init_sections' ) ) {
	function vwpc_init_sections() {
		$sections = array(
			'featured_post_slider' => array(
				'title' => __( 'Featured Post Slider', 'envirra-backend' ),
				'options' => array(
					'category' => array(
						'title' => 'Category',
						'description' => __( 'Choose a post category to be shown up', 'envirra-backend' ),
						'field' => 'category_with_all_option',
						'default' => '0',
					),
					'show_posts' => array(
						'title' => 'Show Posts',
						'description' => __( 'Show only posts marked as Featured post or show all posts.', 'envirra-backend' ),
						'field' => 'select',
						'default' => 'only_featured',
						'options' => array(
							'only_featured' => __( 'Only posts marked as Featured post', 'envirra-backend' ),
							'all' => __( 'Show all posts', 'envirra-backend' ),
						),
					),
					'number' => array(
						'title' => 'Number of featured posts',
						'description' => __( 'Enter the number', 'envirra-backend' ),
						'field' => 'number',
						'default' => 5,
					),
					'is_show_headline_posts' => array(
						'title' => 'Show headline Posts',
						'description' => __( 'Enable additional featured posts under post slider (Max. 4 posts)', 'envirra-backend' ),
						'field' => 'select',
						'default' => '1',
						'options' => array(
							'0' => __( 'Do not show', 'envirra-backend' ),
							'1' => __( 'Show', 'envirra-backend' ),
						),
					),
					'sidebar' => array(
						'title' => 'Sidebar',
						'description' => __( 'Choose a sidebar to be shown up', 'envirra-backend' ),
						'field' => 'sidebar',
						'default' => '0',
					),
				),
			),
			
			'latest' => array(
				'title' => __( 'Latest Posts', 'envirra-backend' ),
				'options' => array(
					'super_title' => array(
						'title' => 'Super-Title',
						'description' => __( 'The text over the title', 'envirra-backend' ),
						'field' => 'text',
						'default' => '',
					),
					'title' => array(
						'title' => 'Title',
						'description' => __( 'The section title', 'envirra-backend' ),
						'field' => 'text',
						'default' => '',
					),
					'sub_title' => array(
						'title' => 'Subtitle',
						'description' => __( 'The text under the title', 'envirra-backend' ),
						'field' => 'text',
						'default' => '',
					),
					'number' => array(
						'title' => 'Number of posts',
						'description' => __( 'Enter the number', 'envirra-backend' ),
						'field' => 'number',
						'default' => 5,
					),
					'exclude_categories' => array(
						'title' => 'Exclude Categories',
						'description' => __( 'Choose the post categories to be excluded', 'envirra-backend' ),
						'field' => 'categories',
						'default' => '',
					),
					'thumbnail_style' => array(
						'title' => 'Thumbnail Style',
						'description' => __( 'Choose the post thumbnail style', 'envirra-backend' ),
						'field' => 'select',
						'default' => 'large-small',
						'options' => array(
							'large' => __( 'Large Thumbnail', 'envirra-backend' ),
							'large-small' => __( 'Large & Small Thumbnail', 'envirra-backend' ),
							'classic' => __( 'Classic', 'envirra-backend' ),
							'poster' => __( 'Poster', 'envirra-backend' ),
						),
					),
					'sidebar' => array(
						'title' => 'Sidebar',
						'description' => __( 'Choose a sidebar to be shown up', 'envirra-backend' ),
						'field' => 'sidebar',
						'default' => 'Blog-sidebar',
					),
					'show_pagination' => array(
						'title' => 'Show Pagination',
						'description' => __( 'Show the pagination', 'envirra-backend' ),
						'field' => 'select',
						'default' => '0',
						'options' => array(
							'0' => __( 'Do not show', 'envirra-backend' ),
							'1' => __( 'Show', 'envirra-backend' ),
						),
					),
				),
			),
			
			'latest_reviews' => array(
				'title' => __( 'Latest Reviews', 'envirra-backend' ),
				'options' => array(
					'super_title' => array(
						'title' => 'Super-Title',
						'description' => __( 'The text over the title', 'envirra-backend' ),
						'field' => 'text',
						'default' => '',
					),
					'title' => array(
						'title' => 'Title',
						'description' => __( 'The section title', 'envirra-backend' ),
						'field' => 'text',
						'default' => '',
					),
					'sub_title' => array(
						'title' => 'Subtitle',
						'description' => __( 'The text under the title', 'envirra-backend' ),
						'field' => 'text',
						'default' => '',
					),
					'number' => array(
						'title' => 'Number of posts',
						'description' => __( 'Enter the number', 'envirra-backend' ),
						'field' => 'number',
						'default' => 5,
					),
					'thumbnail_style' => array(
						'title' => 'Thumbnail Style',
						'description' => __( 'Choose a post thumbnail style', 'envirra-backend' ),
						'field' => 'select',
						'default' => 'large-small',
						'options' => array(
							'large' => __( 'Large Thumbnail', 'envirra-backend' ),
							'large-small' => __( 'Large & Small Thumbnail', 'envirra-backend' ),
							'classic' => __( 'Classic', 'envirra-backend' ),
							'poster' => __( 'Poster', 'envirra-backend' ),
						),
					),
					'sidebar' => array(
						'title' => 'Sidebar',
						'description' => __( 'Choose a sidebar to be shown up', 'envirra-backend' ),
						'field' => 'sidebar',
						'default' => 'Blog-sidebar',
					),
					'show_pagination' => array(
						'title' => 'Show Pagination',
						'description' => __( 'Show the pagination', 'envirra-backend' ),
						'field' => 'select',
						'default' => '0',
						'options' => array(
							'0' => __( 'Do not show', 'envirra-backend' ),
							'1' => __( 'Show', 'envirra-backend' ),
						),
					),
				),
			),

			'latest_category' => array(
				'title' => __( 'Latest By Category', 'envirra-backend' ),
				'options' => array(
					'super_title' => array(
						'title' => 'Super-Title',
						'description' => __( 'The text over the title', 'envirra-backend' ),
						'field' => 'text',
						'default' => '',
					),
					'title' => array(
						'title' => 'Title',
						'description' => __( 'The section title', 'envirra-backend' ),
						'field' => 'text',
						'default' => '',
					),
					'sub_title' => array(
						'title' => 'Subtitle',
						'description' => __( 'The text under the title', 'envirra-backend' ),
						'field' => 'text',
						'default' => '',
					),
					'category' => array(
						'title' => 'Category',
						'description' => __( 'Choose a post category to be shown up', 'envirra-backend' ),
						'field' => 'category',
						'default' => '1',
					),
					'number' => array(
						'title' => 'Number of posts',
						'description' => __( 'Enter the number', 'envirra-backend' ),
						'field' => 'number',
						'default' => 5,
					),
					'thumbnail_style' => array(
						'title' => 'Thumbnail Style',
						'description' => __( 'Choose a post thumbnail style', 'envirra-backend' ),
						'field' => 'select',
						'default' => 'large-small',
						'options' => array(
							'large' => __( 'Large Thumbnail', 'envirra-backend' ),
							'large-small' => __( 'Large & Small Thumbnail', 'envirra-backend' ),
							'classic' => __( 'Classic', 'envirra-backend' ),
							'poster' => __( 'Poster', 'envirra-backend' ),
						),
					),
					'sidebar' => array(
						'title' => 'Sidebar',
						'description' => __( 'Choose a sidebar to be shown up', 'envirra-backend' ),
						'field' => 'sidebar',
						'default' => 'Blog-sidebar',
					),
					'show_pagination' => array(
						'title' => 'Show Pagination',
						'description' => __( 'Show the pagination', 'envirra-backend' ),
						'field' => 'select',
						'default' => '0',
						'options' => array(
							'0' => __( 'Do not show', 'envirra-backend' ),
							'1' => __( 'Show', 'envirra-backend' ),
						),
					),
				),
			),

			'latest_tags' => array(
				'title' => __( 'Latest By Tags', 'envirra-backend' ),
				'options' => array(
					'super_title' => array(
						'title' => 'Super-Title',
						'description' => __( 'The text over the title', 'envirra-backend' ),
						'field' => 'text',
						'default' => '',
					),
					'title' => array(
						'title' => 'Title',
						'description' => __( 'The section title', 'envirra-backend' ),
						'field' => 'text',
						'default' => '',
					),
					'sub_title' => array(
						'title' => 'Subtitle',
						'description' => __( 'The text under the title', 'envirra-backend' ),
						'field' => 'text',
						'default' => '',
					),
					'tags' => array(
						'title' => 'Tags',
						'description' => __( 'Enter a tag slug. Multiple slugs can be separated by comma and no space before or after comma.', 'envirra-backend' ),
						'field' => 'text',
						'default' => '',
					),
					'number' => array(
						'title' => 'Number of posts',
						'description' => __( 'Enter the number', 'envirra-backend' ),
						'field' => 'number',
						'default' => 5,
					),
					'thumbnail_style' => array(
						'title' => 'Thumbnail Style',
						'description' => __( 'Choose a post thumbnail style', 'envirra-backend' ),
						'field' => 'select',
						'default' => 'large-small',
						'options' => array(
							'large' => __( 'Large Thumbnail', 'envirra-backend' ),
							'large-small' => __( 'Large & Small Thumbnail', 'envirra-backend' ),
							'classic' => __( 'Classic', 'envirra-backend' ),
							'poster' => __( 'Poster', 'envirra-backend' ),
						),
					),
					'sidebar' => array(
						'title' => 'Sidebar',
						'description' => __( 'Choose a sidebar to be shown up', 'envirra-backend' ),
						'field' => 'sidebar',
						'default' => 'Blog-sidebar',
					),
					'show_pagination' => array(
						'title' => 'Show Pagination',
						'description' => __( 'Show the pagination', 'envirra-backend' ),
						'field' => 'select',
						'default' => '0',
						'options' => array(
							'0' => __( 'Do not show', 'envirra-backend' ),
							'1' => __( 'Show', 'envirra-backend' ),
						),
					),
				),
			),

			'latest_format' => array(
				'title' => __( 'Latest By Format', 'envirra-backend' ),
				'options' => array(
					'super_title' => array(
						'title' => 'Super-Title',
						'description' => __( 'The text over the title', 'envirra-backend' ),
						'field' => 'text',
						'default' => '',
					),
					'title' => array(
						'title' => 'Title',
						'description' => __( 'The section title', 'envirra-backend' ),
						'field' => 'text',
						'default' => '',
					),
					'sub_title' => array(
						'title' => 'Subtitle',
						'description' => __( 'The text under the title', 'envirra-backend' ),
						'field' => 'text',
						'default' => '',
					),
					'format' => array(
						'title' => 'Post Format',
						'description' => __( 'Choose a post format to be shown up', 'envirra-backend' ),
						'field' => 'select',
						'default' => 'standard',
						'options' => array(
							'standard' => __( 'Standard', 'envirra-backend' ),
							'audio' => __( 'Audio', 'envirra-backend' ),
							'video' => __( 'Video', 'envirra-backend' ),
							'gallery' => __( 'Gallery', 'envirra-backend' ),
						),
					),
					'number' => array(
						'title' => 'Number of posts',
						'description' => __( 'Enter the number', 'envirra-backend' ),
						'field' => 'number',
						'default' => 5,
					),
					'thumbnail_style' => array(
						'title' => 'Thumbnail Style',
						'description' => __( 'Choose a post thumbnail style', 'envirra-backend' ),
						'field' => 'select',
						'default' => 'large-small',
						'options' => array(
							'large' => __( 'Large Thumbnail', 'envirra-backend' ),
							'large-small' => __( 'Large & Small Thumbnail', 'envirra-backend' ),
							'classic' => __( 'Classic', 'envirra-backend' ),
							'poster' => __( 'Poster', 'envirra-backend' ),
						),
					),
					'sidebar' => array(
						'title' => 'Sidebar',
						'description' => __( 'Choose a sidebar to be show up', 'envirra-backend' ),
						'field' => 'sidebar',
						'default' => 'Blog-sidebar',
					),
					'show_pagination' => array(
						'title' => 'Show Pagination',
						'description' => __( 'Show the pagination', 'envirra-backend' ),
						'field' => 'select',
						'default' => '0',
						'options' => array(
							'0' => __( 'Do not show', 'envirra-backend' ),
							'1' => __( 'Show', 'envirra-backend' ),
						),
					),
				),
			),

			'3_sidebars' => array(
				'title' => __( '3 Sidebars', 'envirra-backend' ),
				'options' => array(
					'sidebar_1' => array(
						'title' => 'Sidebar 1',
						'description' => __( 'Choose a sidebar to be show up', 'envirra-backend' ),
						'field' => 'sidebar',
						'default' => 'Blog-sidebar',
					),
					'sidebar_2' => array(
						'title' => 'Sidebar 2',
						'description' => __( 'Choose a sidebar to be show up', 'envirra-backend' ),
						'field' => 'sidebar',
						'default' => 'Blog-sidebar',
					),
					'sidebar_3' => array(
						'title' => 'Sidebar 3',
						'description' => __( 'Choose a sidebar to be show up', 'envirra-backend' ),
						'field' => 'sidebar',
						'default' => 'Blog-sidebar',
					),
				),
			),

			'custom_content' => array(
				'title' => __( 'Custom Content', 'envirra-backend' ),
				'options' => array(
					'super_title' => array(
						'title' => 'Super-Title',
						'description' => __( 'The text over the title', 'envirra-backend' ),
						'field' => 'text',
						'default' => '',
					),
					'title' => array(
						'title' => 'Title',
						'description' => __( 'The section title', 'envirra-backend' ),
						'field' => 'text',
						'default' => '',
					),
					'sub_title' => array(
						'title' => 'Subtitle',
						'description' => __( 'The text under the title', 'envirra-backend' ),
						'field' => 'text',
						'default' => '',
					),
					'content' => array(
						'title' => 'Content',
						'description' => __( 'Enter the content (HTML is allowance)', 'envirra-backend' ),
						'field' => 'html',
						'default' => '',
					),
					'sidebar' => array(
						'title' => 'Sidebar',
						'description' => __( 'Choose a sidebar to be shown up', 'envirra-backend' ),
						'field' => 'sidebar',
						'default' => '0',
					),
				),
			),

		);
		wp_localize_script( 'vwpcjs-page-composer', 'vwpc_sections', $sections );
	}
}


/* -----------------------------------------------------------------------------
 * Init
 * -------------------------------------------------------------------------- */

if ( ! function_exists( 'vwpc_admin_enqueue_scripts' ) ) {
	function vwpc_admin_enqueue_scripts() {
		wp_enqueue_script( 'vwpcjs-page-composer', get_template_directory_uri().'/framework/page-composer/js/page-composer.js', array( 'jquery' ), VW_THEME_VERSION, true );
		wp_localize_script( 'vwpcjs-page-composer', 'vwpc_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	}
}

/* -----------------------------------------------------------------------------
 * Render Editor
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vwpc_render_editor' ) ) {
	function vwpc_render_editor() {
		// Show / Hide the editor on loading
		global $post;

		// if ( ! is_page() ) return;

		if ( isset( $post->ID ) && 'page_composer.php' == get_post_meta( $post->ID,'_wp_page_template',TRUE ) ) : ?>
			<style>#postdivrich{ display:none; }</style>
		<?php else : ?>
			<style>#vwpc-container{ display:none; }</style>
		<?php endif; ?>

		<div id="vwpc-container">
			<input type="hidden" name="vwpc_is_enabled" value="1">

			<div class="vwpc-toolbox">
				<div class="dropdown">
					<button class="button button-primary button-large dropdown-toggle" type="button" id="add-section-button" data-toggle="dropdown">
						<?php _e( 'Add Section', 'envirra-backend' ) ?>
						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu" role="menu" aria-labelledby="add-section-button"></ul>
				</div>
			</div>

			<div class="vwpc-sections">
				<div class="vwpc-section-empty"><?php _e( 'Click <strong>Add Section</strong> button to add new section.', 'envirra' ) ?></div>
				<div class="vwpc-section-loading"><i class="icon-entypo-arrows-ccw"></i> <?php _e( 'Loading ...', 'envirra-backend' ) ?></div>
			</div>

			<!-- Section Template -->
			<script id="vwpc-template-section" type="text/template">
				<div class="vwpc-section">
					<input type="hidden" class="vwpc-section-order" name="vwpc_section_order[]">
					<input type="hidden" class="vwpc-section-type">
					<div class="vwpc-section-bar">
						<div class="vwpc-section-toolbox">
							<a class="vwpc-section-open-option" href="#"><i class="icon-entypo-cog"></i></a>
							<a class="vwpc-section-delete-section" href="#"><i class="icon-entypo-cancel"></i></a>
						</div>
						<i class="vwpc-section-handle icon-entypo-arrow-combo"></i>
						<div class="vwpc-section-label"></div>
					</div>
					<div class="vwpc-section-options hidden"></div>
				</div>
			</script>

			<script id="vwpc-template-section-option" type="text/template">
				<div class="vwpc-section-option vwpc-section-option-2-columns">
					<div class="vwpc-section-option-label-wrapper">
						<label class="vwpc-section-option-label"></label>
						<div class="vwpc-section-option-description"></div>
					</div>
					<div class="vwpc-section-option-field-wrapper"></div>
				</div>
			</script>

			<!-- Fields Template -->
			<script id="vwpc-template-field-text" type="text/template">
				<input class="vwpc-field" type="text">
			</script>

			<script id="vwpc-template-field-number" type="text/template">
				<input class="vwpc-field" type="number" name="quantity" min="1">
			</script>

			<script id="vwpc-template-field-checkbox" type="text/template">
				<input class="vwpc-field" type="hidden">
				<label>
					<input class="vwpc-field" type="checkbox">
					<span></span>
				</label>
			</script>

			<script id="vwpc-template-field-select" type="text/template">
				<select class="vwpc-field"></select>
			</script>

			<script id="vwpc-template-field-category" type="text/template">
				<?php wp_dropdown_categories( array(
					'hide_empty' => 0,
					'class' => 'vwpc-field',
					'hierarchical' => true )
				); ?>
			</script>

			<script id="vwpc-template-field-category_with_all_option" type="text/template">
				<?php wp_dropdown_categories( array(
					'show_option_all' => __( 'All', 'envirra' ),
					'hide_empty' => 0,
					'class' => 'vwpc-field',
					'hierarchical' => true )
				); ?>
			</script>

			<script id="vwpc-template-field-categories" type="text/template">
				<ul class="vw-category-checklist vwpc-field">
					<?php
					$walker = new Vw_Walker_Category_Checklist();
					$walker->set_field_name( 'selected_cats' );

					wp_category_checklist( 0, 0, array(), false, $walker );
					?>
				</ul>
			</script>

			<script id="vwpc-template-field-sidebar" type="text/template">
				<select>
					<option value="0"><?php echo __( 'None', 'envirra-backend' ); ?></option>
				<?php foreach ( $GLOBALS['wp_registered_sidebars'] as $sidebar ) : ?>
					<option value="<?php echo esc_attr( ucwords( $sidebar['id'] ) ); ?>">
						<?php echo ucwords( $sidebar['name'] ); ?>
					</option>
				<?php endforeach; ?>
				</select>
			</script>

			<script id="vwpc-template-field-html" type="text/template">
				<textarea class="vwpc-field"></textarea>
			</script>
		</div>
		<?php
	}
}

/* -----------------------------------------------------------------------------
 * Save Page Composer
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vwpc_save_page' ) ) {
	function vwpc_save_page( $post_id ) {
		if ( 'page' != get_post_type( $post_id ) || ! isset( $_POST['vwpc_is_enabled'] ) ) return;

		$counter = 1;
		if ( isset( $_POST['vwpc_section_order'] ) && ! empty( $_POST['vwpc_section_order'] ) ) {
			foreach ( $_POST['vwpc_section_order'] as $id ) {
				$field_prefix = 'vw_composer_'.$counter;

				vwpc_delete_section( $post_id, $field_prefix );
				update_post_meta( $post_id, $field_prefix, $_POST[ 'vwpc_sections' ][ $id ]['_type'] );

				foreach ( array_keys( $_POST[ 'vwpc_sections' ][ $id ] ) as $field ) {
					if ( '_type' == $field ) continue;

					$field_value = $_POST[ 'vwpc_sections' ][ $id ][ $field ];
					if ( is_array( $field_value ) ) {
						$field_value = implode( ',', array_filter( $field_value ) );
					}

					update_post_meta( $post_id, $field_prefix.'_'.$field, $field_value );

					if ( function_exists( 'icl_register_string' ) ) {

						if ( 'super_title' == $field ) {
							icl_register_string( VW_CONST_COMPOSER_TRANSLATE_SLUG, sprintf( 'page_%1s_%1s_super_title', $post_id, $counter ), $field_value );
						}

						if ( 'title' == $field ) {
							icl_register_string( VW_CONST_COMPOSER_TRANSLATE_SLUG, sprintf( 'page_%1s_%1s_title', $post_id, $counter ), $field_value );
						}

						if ( 'sub_title' == $field ) {
							icl_register_string( VW_CONST_COMPOSER_TRANSLATE_SLUG, sprintf( 'page_%1s_%1s_sub_title', $post_id, $counter ), $field_value );
						}

					}
				}

				$counter++;
			}
		}

		// Delete the next section
		$field_prefix = 'vw_composer_'.$counter;
		vwpc_delete_section( $post_id, $field_prefix );
	}
}

/* -----------------------------------------------------------------------------
 * Delete Composer Section
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vwpc_delete_section' ) ) {
	function vwpc_delete_section( $post_id, $target_field ) {
		$custom_fields = get_post_custom_keys( $post_id );
		foreach ( $custom_fields as $custom_field ) {
			if ( strpos( $custom_field, $target_field ) === 0 ) {
				delete_post_meta( $post_id, $custom_field );
			}
		}
	}
}

/* -----------------------------------------------------------------------------
 * Render Sections
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vwpc_render' ) ) {
	function vwpc_render( $args=array() ) {
		$defaults = array(
			'before' => '',
			'after' => '',
			'before_section' => '<div class="row vwpc-row">',
			'after_section' => '</div>',
		);
		$args = wp_parse_args( $args, $defaults );
		$page_id = get_queried_object_id();

		echo $args['before'];

		for ( $counter=1; $counter < 50; $counter++ ) { 
			$field_prefix = 'vw_composer_'.$counter;
			$section_type = get_post_meta( $page_id, $field_prefix, true );

			if ( ! $section_type ) break;

			echo $args['before_section'];
			call_user_func( 'vwpc_render_section_'.$section_type, $page_id, $field_prefix );
			echo $args['after_section'];
		}

		echo $args['after'];
	}
}

/* -----------------------------------------------------------------------------
 * Render Section: Latest Posts
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vwpc_render_section_latest' ) ) {
	function vwpc_render_section_latest( $page_id, $field_prefix ) {
		$number = get_post_meta( $page_id, $field_prefix.'_number', true );
		$show_pagination = get_post_meta( $page_id, $field_prefix.'_show_pagination', true );
		$exclude_categories = get_post_meta( $page_id, $field_prefix.'_exclude_categories', true );
		$exclude_categories = explode( ',', $exclude_categories );
		$paged = 1;
		if ( '1' == $show_pagination ) $paged = vwpc_get_paged();

		$args = array(
			'post_type' => 'post',
			'orderby' => 'post_date',
			'ignore_sticky_posts' => true,
			'posts_per_page' => $number,
			'paged' => $paged,
			'category__not_in' => $exclude_categories,
			// 'meta_key' => '_thumbnail_id', //Only posts that have featured image
		);

		$the_query = new WP_Query( $args );

		echo '<div class="vwpc-section-latest">';
		vwpc_render_post_section( $page_id, $field_prefix, $the_query );
		echo '</div>';
	}
}

/* -----------------------------------------------------------------------------
 * Render Section: Latest By Category
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vwpc_render_section_latest_category' ) ) {
	function vwpc_render_section_latest_category( $page_id, $field_prefix ) {
		$number = get_post_meta( $page_id, $field_prefix.'_number', true );
		$show_pagination = get_post_meta( $page_id, $field_prefix.'_show_pagination', true );
		$paged = 1;
		if ( '1' == $show_pagination ) $paged = vwpc_get_paged();

		$args = array(
			'post_type' => 'post',
			'orderby' => 'post_date',
			'ignore_sticky_posts' => true,
			'posts_per_page' => $number,
			'paged' => $paged,
			// 'meta_key' => '_thumbnail_id', //Only posts that have featured image
		);

		$category = get_post_meta( $page_id, $field_prefix.'_category', true );
		if ( $category >= 1 ) {
			$args[ 'cat' ] = $category;
		}

		$the_query = new WP_Query( $args );

		echo '<div class="vwpc-section-latest_category">';
		vwpc_render_post_section( $page_id, $field_prefix, $the_query );
		echo '</div>';
	}
}

/* -----------------------------------------------------------------------------
 * Render Section: Latest By Tags
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vwpc_render_section_latest_tags' ) ) {
	function vwpc_render_section_latest_tags( $page_id, $field_prefix ) {
		$number = get_post_meta( $page_id, $field_prefix.'_number', true );
		$show_pagination = get_post_meta( $page_id, $field_prefix.'_show_pagination', true );
		$paged = 1;
		if ( '1' == $show_pagination ) $paged = vwpc_get_paged();

		$args = array(
			'post_type' => 'post',
			'orderby' => 'post_date',
			'ignore_sticky_posts' => true,
			'posts_per_page' => $number,
			'paged' => $paged,
			// 'meta_key' => '_thumbnail_id', //Only posts that have featured image
		);

		$tags = get_post_meta( $page_id, $field_prefix.'_tags', true );
		if ( ! empty( $tags ) ) {
			$args[ 'tag' ] = $tags;
		}

		$the_query = new WP_Query( $args );

		echo '<div class="vwpc-section-latest_tags">';
		vwpc_render_post_section( $page_id, $field_prefix, $the_query );
		echo '</div>';
	}
}

/* -----------------------------------------------------------------------------
 * Render Section: Latest By Format
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vwpc_render_section_latest_format' ) ) {
	function vwpc_render_section_latest_format( $page_id, $field_prefix ) {
		$number = get_post_meta( $page_id, $field_prefix.'_number', true );
		$show_pagination = get_post_meta( $page_id, $field_prefix.'_show_pagination', true );
		$paged = 1;
		if ( '1' == $show_pagination ) $paged = vwpc_get_paged();

		$args = array(
			'post_type' => 'post',
			'orderby' => 'post_date',
			'ignore_sticky_posts' => true,
			'posts_per_page' => $number,
			'paged' => $paged,
			// 'meta_key' => '_thumbnail_id', //Only posts that have featured image
		);

		$format = get_post_meta( $page_id, $field_prefix.'_format', true );
		if ( 'standard' == $format ) {
			$args[ 'tax_query' ] = array(
				array(
					'taxonomy' => 'post_format',
					'field' => 'slug',
					'terms' =>  array( 'post-format-video', 'post-format-gallery', 'post-format-audio' ),
					'operator' => 'NOT IN',
				)
			);
		} else {
			$args[ 'tax_query' ] = array(
				array(
					'taxonomy' => 'post_format',
					'field' => 'slug',
					'terms' =>  array( 'post-format-' . $format ),
				)
			);
		}

		$the_query = new WP_Query( $args );

		echo '<div class="vwpc-section-latest_format">';
		vwpc_render_post_section( $page_id, $field_prefix, $the_query );
		echo '</div>';
	}
}

/* -----------------------------------------------------------------------------
 * Render Section: Post Slider
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vwpc_render_section_featured_post_slider' ) ) {
	function vwpc_render_section_featured_post_slider( $page_id, $field_prefix ) {
		$option_classes = '';
		$category = get_post_meta( $page_id, $field_prefix.'_category', true );
		$show_posts = get_post_meta( $page_id, $field_prefix.'_show_posts', true );
		$number = get_post_meta( $page_id, $field_prefix.'_number', true );
		$sidebar = get_post_meta( $page_id, $field_prefix.'_sidebar', true );
		$has_sidebar = ! empty( $sidebar );

		if ( $has_sidebar ) $option_classes .= ' has-sidebar ';

		$args = array(
			'post_type' => 'post',
			'orderby' => 'post_date',
			'ignore_sticky_posts' => true,
			'posts_per_page' => $number,
		);

		if ( $show_posts != 'all' ) {
			$args[ 'meta_query' ] = array(
				array(
					'key' => 'vw_post_featured', //Only posts that have review
					'value' => '1',
					'compare' => '=',
				)
			);
		}

		if ( $category > 0 ) {
			$args[ 'cat' ] = $category;
		}

		query_posts( $args );
		?>
		<div class="vwpc-section-featured_post_slider <?php echo $option_classes; ?>">
			<?php if ( $has_sidebar ) : ?>
			<div class="col-sm-7 col-md-8">
			<?php else : ?>
			<div class="col-sm-12">
			<?php endif; ?>
				<hr class="section-hr">
				<?php get_template_part( 'templates/post-box/post-slider' ); ?>
			
			<?php
			wp_reset_query();

			if ( get_post_meta( $page_id, $field_prefix.'_is_show_headline_posts', true ) ) :
				$args['offset'] = $number;
				if ( $has_sidebar ) {
					$args['posts_per_page'] = 3;
					$column_classes = 'col-sm-6 col-md-4';
				} else {
					$args['posts_per_page'] = 4;
					$column_classes = 'col-sm-4 col-md-3';
				}
				$the_query = new WP_Query( $args );

				if ( $the_query->have_posts() ) :
				?>
					<div class="vwpc-section-featured_post_slider-headline row">
					<?php while ( $the_query->have_posts() ): $the_query->the_post();
						$hidden_class = '';
						if ( $the_query->current_post == $the_query->post_count-1 ) {
							$hidden_class = 'hidden-sm';
						}
					?>
						<div class="post-box-wrapper <?php echo $column_classes; ?> <?php echo $hidden_class; ?>"><?php get_template_part( 'templates/post-box/headline', get_post_format() ); ?></div>
					<?php endwhile; ?>
					</div>

				<?php endif;
				wp_reset_postdata();
			endif;
			?>
			</div>
			<?php if ( $has_sidebar ) : ?>
			<div class="col-sm-5 col-md-4">
				<aside class="sidebar-wrapper">
					<div class="sidebar-inner">
						<hr class="section-hr">
						<?php dynamic_sidebar( $sidebar ); ?>
					</div>
				</aside>
			</div>
			<?php endif; ?>
		</div>
		<?php
	}
}

/* -----------------------------------------------------------------------------
 * Render Section: Latest Reviews
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vwpc_render_section_latest_reviews' ) ) {
	function vwpc_render_section_latest_reviews( $page_id, $field_prefix ) {
		$number = get_post_meta( $page_id, $field_prefix.'_number', true );
		$show_pagination = get_post_meta( $page_id, $field_prefix.'_show_pagination', true );
		$paged = 1;
		if ( '1' == $show_pagination ) $paged = vwpc_get_paged();

		$the_query = new WP_Query( array(
			'post_type' => 'post',
			'orderby' => 'post_date',
			'ignore_sticky_posts' => true,
			'posts_per_page' => $number,
			'paged' => $paged,
			'meta_query' => array(
				array(
					'key' => 'vw_enable_review', //Only posts that have review
					'value' => '1',
					'compare' => '=',
				)
			),
		) );

		echo '<div class="vwpc-section-latest_reviews">';
		vwpc_render_post_section( $page_id, $field_prefix, $the_query );
		echo '</div>';
	}
}

/* -----------------------------------------------------------------------------
 * Render Section: 3 Sidebars
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vwpc_render_section_3_sidebars' ) ) {
	function vwpc_render_section_3_sidebars( $page_id, $field_prefix ) {
		$sidebar_1 = get_post_meta( $page_id, $field_prefix.'_sidebar_1', true );
		$sidebar_2 = get_post_meta( $page_id, $field_prefix.'_sidebar_2', true );
		$sidebar_3 = get_post_meta( $page_id, $field_prefix.'_sidebar_3', true );

		echo '<div class="vwpc-section-3_sidebars">';

		// Sidebar 1
		echo '<div class="col-sm-4">';
		echo '<hr class="section-hr">';
		dynamic_sidebar( $sidebar_1 );
		echo '</div>';

		// Sidebar 2
		echo '<div class="col-sm-4">';
		echo '<hr class="section-hr">';
		dynamic_sidebar( $sidebar_2 );
		echo '</div>';

		// Sidebar 3
		echo '<div class="col-sm-4">';
		echo '<hr class="section-hr">';
		dynamic_sidebar( $sidebar_3 );
		echo '</div>';

		echo '</div>';
	}
}

/* -----------------------------------------------------------------------------
 * Render Section: Custom Content
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vwpc_render_section_custom_content' ) ) {
	function vwpc_render_section_custom_content( $page_id, $field_prefix ) {
		$title = get_post_meta( $page_id, $field_prefix.'_title', true );
		$super_title = get_post_meta( $page_id, $field_prefix.'_super_title', true );
		$sub_title = get_post_meta( $page_id, $field_prefix.'_sub_title', true );
		$sidebar = get_post_meta( $page_id, $field_prefix.'_sidebar', true );

		
		if ( function_exists( 'icl_t' ) ) {
			//page_%1s_%1s_super_title
			$counter = str_replace( 'vw_composer_', '', $field_prefix );
			$translate_prefix = sprintf( 'page_%1s_%1s', $page_id, $counter );
			
			$title = icl_t( VW_CONST_COMPOSER_TRANSLATE_SLUG, $translate_prefix.'_title', $title );
			$super_title = icl_t( VW_CONST_COMPOSER_TRANSLATE_SLUG, $translate_prefix.'_super_title', $super_title );
			$sub_title = icl_t( VW_CONST_COMPOSER_TRANSLATE_SLUG, $translate_prefix.'_sub_title', $sub_title );
		}

		echo '<div class="vwpc-section-custom_content">';

		if ( '0' == $sidebar ) :
		echo '<div class="col-sm-12">';
		else :
		echo '<div class="col-sm-7 col-md-8">';
		endif;

		echo '<hr class="section-hr">';

		vwpc_render_section_title( $title, $super_title, $sub_title );

		echo apply_filters( 'the_content', get_post_meta( $page_id, $field_prefix.'_content', true ) );
		echo '</div>';

		if ( '0' != $sidebar ) : ?>
			<div class="col-sm-5 col-md-4">
				<aside class="sidebar-wrapper">
					<div class="sidebar-inner">
						<hr class="section-hr">
						<?php dynamic_sidebar( $sidebar ); ?>
					</div>
				</aside>
			</div>
		<?php
		endif;

		echo '</div>';
	}
}

/* -----------------------------------------------------------------------------
 * Render Post Section
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vwpc_render_post_section' ) ) {
	function vwpc_render_post_section( $page_id, $field_prefix, $the_query ) {
		$title = get_post_meta( $page_id, $field_prefix.'_title', true );
		$super_title = get_post_meta( $page_id, $field_prefix.'_super_title', true );
		$sub_title = get_post_meta( $page_id, $field_prefix.'_sub_title', true );

		if ( function_exists( 'icl_t' ) ) {
			//page_%1s_%1s_super_title
			$counter = str_replace( 'vw_composer_', '', $field_prefix );
			$translate_prefix = sprintf( 'page_%1s_%1s', $page_id, $counter );
			
			$title = icl_t( VW_CONST_COMPOSER_TRANSLATE_SLUG, $translate_prefix.'_title', $title );
			$super_title = icl_t( VW_CONST_COMPOSER_TRANSLATE_SLUG, $translate_prefix.'_super_title', $super_title );
			$sub_title = icl_t( VW_CONST_COMPOSER_TRANSLATE_SLUG, $translate_prefix.'_sub_title', $sub_title );
		}

		$thumbnail_style = get_post_meta( $page_id, $field_prefix.'_thumbnail_style', true );
		$sidebar = get_post_meta( $page_id, $field_prefix.'_sidebar', true );
		$show_pagination = get_post_meta( $page_id, $field_prefix.'_show_pagination', true );

		// Render pagination
		$pagination_html = '';
		if ( '1' == $show_pagination ) {
			ob_start();
			global $wp_query;
			$main_query = $wp_query;
			$wp_query = $the_query;
			get_template_part( 'templates/pagination' );
			$wp_query = $main_query;
			$pagination_html = ob_get_clean();
		}

		// Render posts
		if ( $the_query->have_posts() ) : ?>

			<?php if ( 'large' == $thumbnail_style ) : ?>
				<?php if ( '0' == $sidebar ) : ?>
					<div class="col-sm-12">
						<hr class="section-hr">
						<?php vwpc_render_section_title( $title, $super_title, $sub_title ); ?>

						<div class="row vw-isotope post-box-list">
							<?php while ( $the_query->have_posts() ): $the_query->the_post(); ?>
								<div class="post-box-wrapper col-sm-4"><?php get_template_part( 'templates/post-box/large-thumbnail', get_post_format() ); ?></div>
							<?php endwhile; ?>
						</div>

						<?php echo $pagination_html; ?>
					</div>
				<?php else : ?>
					<div class="col-sm-7 col-md-8">
						<hr class="section-hr">
						<?php vwpc_render_section_title( $title, $super_title, $sub_title ); ?>

						<div class="row vw-isotope post-box-list">
							<?php while ( $the_query->have_posts() ): $the_query->the_post(); ?>
								<div class="post-box-wrapper col-sm-6"><?php get_template_part( 'templates/post-box/large-thumbnail', get_post_format() ); ?></div>
							<?php endwhile; ?>
						</div>

						<?php echo $pagination_html; ?>
					</div>
					<div class="col-sm-5 col-md-4">
						<aside class="sidebar-wrapper">
							<div class="sidebar-inner">
								<hr class="section-hr">
								<?php dynamic_sidebar( $sidebar ); ?>
							</div>
						</aside>
					</div>
				<?php endif; ?>

			<?php elseif ( 'poster' == $thumbnail_style ) : ?>
				<?php if ( '0' == $sidebar ) : ?>
					<div class="col-sm-12">
						<hr class="section-hr">
						<?php vwpc_render_section_title( $title, $super_title, $sub_title ); ?>

						<div class="row vw-isotope post-box-list">
							<?php while ( $the_query->have_posts() ): $the_query->the_post(); ?>
								<div class="post-box-wrapper col-sm-4"><?php get_template_part( 'templates/post-box/poster', get_post_format() ); ?></div>
							<?php endwhile; ?>
						</div>

						<?php echo $pagination_html; ?>
					</div>
				<?php else : ?>
					<div class="col-sm-7 col-md-8">
						<hr class="section-hr">
						<?php vwpc_render_section_title( $title, $super_title, $sub_title ); ?>

						<div class="row vw-isotope post-box-list">
							<?php while ( $the_query->have_posts() ): $the_query->the_post(); ?>
								<div class="post-box-wrapper col-sm-6"><?php get_template_part( 'templates/post-box/poster', get_post_format() ); ?></div>
							<?php endwhile; ?>
						</div>

						<?php echo $pagination_html; ?>
					</div>
					<div class="col-sm-5 col-md-4">
						<aside class="sidebar-wrapper">
							<div class="sidebar-inner">
								<hr class="section-hr">
								<?php dynamic_sidebar( $sidebar ); ?>
							</div>
						</aside>
					</div>
				<?php endif; ?>

			<?php elseif ( 'classic' == $thumbnail_style ) : ?>
				<?php if ( '0' == $sidebar ) : ?>
					<div class="col-sm-12">
						<hr class="section-hr">
						<?php vwpc_render_section_title( $title, $super_title, $sub_title ); ?>

						<div class="row post-box-list">
							<?php while ( $the_query->have_posts() ): $the_query->the_post(); ?>
								<div class="post-box-wrapper col-sm-12"><?php get_template_part( 'templates/post-box/classic', get_post_format() ); ?></div>
							<?php endwhile; ?>
						</div>

						<?php echo $pagination_html; ?>
					</div>
				<?php else : ?>
					<div class="col-sm-7 col-md-8">
						<hr class="section-hr">
						<?php vwpc_render_section_title( $title, $super_title, $sub_title ); ?>

						<div class="row post-box-list">
							<?php while ( $the_query->have_posts() ): $the_query->the_post(); ?>
								<div class="post-box-wrapper col-sm-12"><?php get_template_part( 'templates/post-box/classic', get_post_format() ); ?></div>
							<?php endwhile; ?>
						</div>

						<?php echo $pagination_html; ?>
					</div>
					<div class="col-sm-5 col-md-4">
						<aside class="sidebar-wrapper">
							<div class="sidebar-inner">
								<hr class="section-hr">
								<?php dynamic_sidebar( $sidebar ); ?>
							</div>
						</aside>
					</div>
				<?php endif; ?>

			<?php else : ?>
				<?php if ( '0' == $sidebar ) : ?>
					<div class="col-sm-12">
						<hr class="section-hr">
						<?php vwpc_render_section_title( $title, $super_title, $sub_title ); ?>
						
						<div class="row vw-isotope post-box-list">
							<?php foreach( range( 1, 3 ) as $i ): ?>
								<?php if ( $the_query->have_posts() ): $the_query->the_post(); ?>
									<div class="post-box-wrapper col-sm-4">
										<?php get_template_part( 'templates/post-box/large-thumbnail', get_post_format() ); ?>
									</div>
								<?php endif; ?>
							<?php endforeach; ?>

							<?php while ( $the_query->have_posts() ): $the_query->the_post(); ?>
								<div class="post-box-wrapper col-sm-4"><?php get_template_part( 'templates/post-box/small-thumbnail', get_post_format() ); ?></div>
							<?php endwhile; ?>
						</div>

						<?php echo $pagination_html; ?>
					</div>

				<?php else : ?>
					<div class="col-sm-7 col-md-8">
						<hr class="section-hr">
						<?php vwpc_render_section_title( $title, $super_title, $sub_title ); ?>

						<div class="row vw-isotope post-box-list">
							<?php foreach( range( 1, 2 ) as $i ): ?>
								<?php if ( $the_query->have_posts() ): $the_query->the_post(); ?>
									<div class="post-box-wrapper col-sm-6">
										<?php get_template_part( 'templates/post-box/large-thumbnail', get_post_format() ); ?>
									</div>
								<?php endif; ?>
							<?php endforeach; ?>

							<?php while ( $the_query->have_posts() ): $the_query->the_post(); ?>
								<div class="post-box-wrapper col-sm-6">
									<?php get_template_part( 'templates/post-box/small-thumbnail', get_post_format() ); ?>
								</div>
							<?php endwhile; ?>
						</div>

						<?php echo $pagination_html; ?>
					</div>
					<div class="col-sm-5 col-md-4">
						<aside class="sidebar-wrapper">
							<div class="sidebar-inner">
								<hr class="section-hr">
								<?php dynamic_sidebar( $sidebar ); ?>
							</div>
						</aside>
					</div>
				<?php endif; ?>
			<?php
			endif;
		endif;

		wp_reset_postdata();
	}
}

/* -----------------------------------------------------------------------------
 * Render Section Title
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vwpc_render_section_title' ) ) {
	function vwpc_render_section_title( $title, $super_title='', $sub_title='' ) {
		if ( ! empty( $super_title ) ) $super_title = sprintf( '<span class="super-title">%s</span>', $super_title );

		if ( ! empty( $sub_title ) ) $sub_title = sprintf( '<p class="section-description">%s</p>', $sub_title );

		if ( ! empty( $title ) ) : ?>
			<h1 class="section-title title title-large">
				<?php echo $super_title . $title; ?>
			</h1>
			<?php echo $sub_title; ?>
		<?php endif;
	}
}

/* -----------------------------------------------------------------------------
 * Utility functions
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vwpc_get_paged' ) ) {
	function vwpc_get_paged() {
		$paged = 1;
		if ( get_query_var('paged') ) $paged = get_query_var('paged');
		if ( get_query_var('page') ) $paged = get_query_var('page');

		return $paged;
	}
}