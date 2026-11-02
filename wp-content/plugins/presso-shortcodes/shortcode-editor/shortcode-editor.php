<?php
/* -----------------------------------------------------------------------------
 * Shortcode Editor Class
 * -------------------------------------------------------------------------- */
class Vw_Shortcode_Editor {
	public $shortcodes = array();
	public $original_shortcodes;
	public $main_tag;

	public function __construct() {
		add_action( 'wp_ajax_vwsce_build_editor', array( $this, 'ajax_build_editor' ) );
	}

	public function init_editor() {
		do_action( 'vwsce_editor_init' );
		$this->remove_existing_shortcodes();
	}

	private function remove_existing_shortcodes() {
		global $shortcode_tags;
		$this->original_shortcodes = $shortcode_tags;
		remove_all_shortcodes();
	}

	private function restore_original_shortcodes() {
		global $shortcode_tags;
		$shortcode_tags = $this->original_shortcodes;
	}

	public function register_shortcodes( $shortcodes = array() ) {
		if ( empty( $shortcodes ) ) return;

		$this->shortcodes = array_merge( $this->shortcodes, $shortcodes );
	}

	public function ajax_build_editor() {
		$this->init_editor();

		$content = stripslashes( trim( $_POST['content'] ) );

		if ( preg_match( '|\[([^\] ]+)|', $content, $match ) ) {
			$this->main_tag = $match[1];
			add_shortcode( $this->main_tag, array( $this, 'shortcode_mock' ) );

			printf( '<input id="main_tag" type="hidden" value="%s">', $this->main_tag );
			do_shortcode( $content );
		}

		do_action( 'vwsce_after_build_editor' );

		die();
	}

	public function shortcode_mock( $atts, $content, $tag ) {
		$defaults = array();
		foreach( $this->shortcodes[$tag][ 'atts' ] as $attr => $setting ) {
			if ( isset( $setting[ 'default' ] ) ) {
				$defaults[ $attr ] = $setting[ 'default' ];
			}
		}

		$atts = shortcode_atts( $defaults, $atts);
		foreach ( $this->shortcodes[$tag][ 'atts' ] as $attr => $field ) {
			if ( isset( $field['render_as'] ) && 'content' == $field['render_as'] ) {
				do_action( 'vwsce_render_field_'.$field['type'], $attr, $field, $content );
			} else {
				do_action( 'vwsce_render_field_'.$field['type'], $attr, $field, $atts[$attr] );
			}
		}
	}
}

/* -----------------------------------------------------------------------------
 * Init
 * -------------------------------------------------------------------------- */
global $vwsce;

if ( ! function_exists( 'vwsce_shortcode_editor_init' ) ) {
	function vwsce_shortcode_editor_init() {
		global $vwsce;
		$vwsce = new Vw_Shortcode_Editor();
	}

	add_action( 'admin_init', 'vwsce_shortcode_editor_init' );
}

if ( ! function_exists( 'vwsce_admin_enqueue_scripts' ) ) {
	function vwsce_admin_enqueue_scripts() {
		wp_enqueue_script( 'shortcode-editor', VWSC_URI.'/shortcode-editor/js/shortcode-editor.js', array( 'jquery' ), null, true );
		wp_localize_script( 'shortcode-editor', 'vwsce_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	}
	add_action( 'admin_enqueue_scripts', 'vwsce_admin_enqueue_scripts' );
}

/* -----------------------------------------------------------------------------
 * Field Renderer
 * -------------------------------------------------------------------------- */

if ( ! function_exists( 'vwsce_render_field_layout' ) ) {
	function vwsce_render_field_layout( $field, $title, $desc='', $field_class='' ) {
		?>

		<div class="<?php vwsce_field_class( $field_class ); ?>">
			<div class="vwsce-field-description-container">
				<div class="vwsce-field-title"><?php echo $title; ?></div>
				<div class="vwsce-field-description"><?php echo $desc; ?></div>
			</div>
			<div class="vwsce-field-body"><?php echo $field; ?></div>
		</div>

		<?php
	}
}

if ( ! function_exists( 'vwsce_render_field_dropdown' ) ) {
	function vwsce_render_field_dropdown( $attr, $field, $value ) {
		$option_html = '';
		foreach ( $field[ 'options' ] as $opt_value => $opt_name ) {
			$option_html .= sprintf( '<option value="%2$s" %3$s>%1$s</option>', $opt_name, $opt_value, selected( $opt_value, $value, false ) );
		}
		$field_html = sprintf( '<select name="%1$s" class="vwsce-attr" data-vwsce-attr-default="%3$s">%2$s</select>', esc_attr( $attr ), $option_html, esc_attr( $field['default'] ) );

		vwsce_render_field_layout( $field_html, $field['title'], $field['desc'], 'two-column-field' );
	}
	add_action( 'vwsce_render_field_dropdown', 'vwsce_render_field_dropdown', 10, 3 );
}

if ( ! function_exists( 'vwsce_render_field_text' ) ) {
	function vwsce_render_field_text( $attr, $field, $value ) {
		$classes = '';
		if ( isset( $field['render_as'] ) ) $classes .= ' vwsce-render-as-'.$field['render_as'];

		$field_html = sprintf( '<input type="text" class="vwsce-attr %4$s" name="%1$s" value="%2$s" data-vwsce-attr-default="%3$s">', esc_attr( $attr ), $value, esc_attr( $field['default'] ), $classes );

		vwsce_render_field_layout( $field_html, $field['title'], $field['desc'], 'two-column-field' );
	}
	add_action( 'vwsce_render_field_text', 'vwsce_render_field_text', 10, 3 );
}

if ( ! function_exists( 'vwsce_render_field_color' ) ) {
	function vwsce_render_field_color( $attr, $field, $value ) {
		$classes = '';
		if ( isset( $field['render_as'] ) ) $classes .= ' vwsce-render-as-'.$field['render_as'];

		$field_html = sprintf( '<input type="text" class="vwsce-attr color-picker %4$s" name="%1$s" value="%2$s" data-vwsce-attr-default="%3$s">', esc_attr( $attr ), $value, esc_attr( $field['default'] ), $classes );

		vwsce_render_field_layout( $field_html, $field['title'], $field['desc'], 'two-column-field' );
	}
	add_action( 'vwsce_render_field_color', 'vwsce_render_field_color', 10, 3 );
}

if ( ! function_exists( 'vwsce_render_field_textarea' ) ) {
	function vwsce_render_field_textarea( $attr, $field, $value ) {
		$classes = '';
		if ( isset( $field['render_as'] ) ) $classes .= ' vwsce-render-as-'.$field['render_as'];

		$field_html = sprintf( '<textarea name="%1$s" class="vwsce-attr %4$s" data-vwsce-attr-default="%3$s">%2$s</textarea>', esc_attr( $attr ), $value, esc_attr( $field['default'] ), $classes );

		vwsce_render_field_layout( $field_html, $field['title'], $field['desc'], 'two-column-field' );
	}
	add_action( 'vwsce_render_field_textarea', 'vwsce_render_field_textarea', 10, 3 );
}

if ( ! function_exists( 'vwsce_render_field_image' ) ) {
	function vwsce_render_field_image( $attr, $field, $value ) {
		$field_html = '<div class="vwsce-field-image">';
		$field_html .= '<a href="#" class="button vwsce-insert-media" title="Add Media"><span class="wp-media-buttons-icon"></span> Add Media</a>';
		$field_html .= '<a href="#" class="button vwsce-remove-media" title="Remove Media"><span class="wp-media-buttons-icon"></span> Remove Media</a>';
		$field_html .= sprintf( '<img src="%2$s" class="placeholder hidden vwsce-attr" name="%1$s" data-vwsce-attr-default="">', esc_attr( $attr ), $value );
		$field_html .= '</div>';

		vwsce_render_field_layout( $field_html, $field['title'], $field['desc'], 'two-column-field' );
	}
	add_action( 'vwsce_render_field_image', 'vwsce_render_field_image', 10, 3 );
}

if ( ! function_exists( 'vwsce_render_field_icon' ) ) {
	function vwsce_render_field_icon( $attr, $field, $value ) {
		$icons = apply_filters( 'vwsce_icon_list', array() );
		$icons_html = '';
		foreach ( $icons as $icon ) {
			$classes = $icon;
			if ( $value == $icon ) {
				$classes .= ' selected-icon';
			}

			$icons_html .= '<i class="'.$classes.'" data-vwsce-icon-name="'.$icon.'"></i>';
		}
		$icons_html = '<div class="vwsce-available-icons">'.$icons_html.'</div>';
		$value_field = '<input name="icon" type="hidden" class="vwsce-attr" value="'.$value.'" data-vwsce-attr-default="'.esc_attr( $field['default'] ).'">';

		$field_html = '<div class="vwsce-field-icon">'.$value_field.$icons_html.'</div>';

		vwsce_render_field_layout( $field_html, $field['title'], $field['desc'], 'one-column-field' );
	}
	add_action( 'vwsce_render_field_icon', 'vwsce_render_field_icon', 10, 3 );
}

if ( ! function_exists( 'vwsce_render_field_html' ) ) {
	function vwsce_render_field_html( $attr, $field, $value ) {
		ob_start();
        wp_editor( $value, 'vwsce_tinymce_'.$attr, array(
        	'editor_class' => 'vwsce-attr vwsce-render-as-'.$field['render_as'],
        	'textarea_rows' => '11',
        ) );

        $field_html = ob_get_clean();

        vwsce_render_field_layout( $field_html, $field['title'], $field['desc'], 'one-column-field' );
	}
	add_action( 'vwsce_render_field_html', 'vwsce_render_field_html', 10, 3 );
}

/* -----------------------------------------------------------------------------
 * Field Classes
 * -------------------------------------------------------------------------- */

if ( ! function_exists( 'vwsce_field_class' ) ) {
	function vwsce_field_class( $classes = '' ) {
		echo join( ' ', apply_filters( 'vwsce_field_class', array( $classes ) ) );
	}
}

if ( ! function_exists( 'vwsce_default_field_class' ) ) {
	function vwsce_default_field_class( $classes ) {
		$classes[] = 'vwsce-field clearfix';
		return $classes;
	}
	add_filter( 'vwsce_field_class', 'vwsce_default_field_class' );
}