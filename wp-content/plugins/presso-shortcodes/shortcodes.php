<?php
/* -----------------------------------------------------------------------------
 * Accordion
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vw_shortcode_accordion' ) ) {
	function vw_shortcode_accordion( $atts, $content = null ) {
		$defaults = array(
			'title' => 'Accordion Title',
			'icon' => '',
			'open' => 'false',
		);
		
		extract( shortcode_atts( $defaults, $atts) );

		$icon_html = '';
		if ( ! empty( $icon ) ) {
			$icon_html = "<i class='icon-".esc_attr( $icon )."'></i> ";
		}

		ob_start(); // no enter to prevent invalid p tag
		?><div class="accordion" data-open="<?php echo esc_attr( $open ); ?>"><h4 class="accordion-header"><span class="accordion-header-text"><?php echo $icon_html.$title; ?></span></h4>
			<div class="accordion-content"><?php echo do_shortcode( $content ); ?></div></div><?php
		return ob_get_clean();
	}
}

/* -----------------------------------------------------------------------------
 * Button
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vw_shortcode_button' ) ) {
	function vw_shortcode_button( $atts, $content = 'Submit' ) {
		$defaults = array(
			'style' => '',
			'icon' => '',
			'target' => '_self',
			'url' => '#',
			'fullwidth' => '' // true, ''
		);
		
		extract( shortcode_atts( $defaults, $atts) );

		$icon_html = '';
		if ( ! empty( $icon ) ) {
			$icon_html = " ";
			$icon_html = sprintf( '<i class="icon-%s"></i>', esc_attr( $icon ) );
		}

		if ( $url == 'home' ) $url = get_home_url();

		$classes = '';
		if( $fullwidth == 'true' ) $classes .= ' button-full-width';

		return "<a class='".esc_attr( "button button-{$style} {$classes}" )."' href='".esc_url( $url )."' target='".esc_attr( $target )."'>{$icon_html}{$content}</a>";
	}
}

/* -----------------------------------------------------------------------------
 * Columns
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vw_shortcode_one_half' ) ) {
	function vw_shortcode_one_half(  $atts, $content = null ) {
		return "<div class='one_half column_shortcode'>" . do_shortcode( $content ). "</div>";
	}
}

if ( ! function_exists( 'vw_shortcode_one_half_last' ) ) {
	function vw_shortcode_one_half_last(  $atts, $content = null ) {
		return "<div class='one_half last column_shortcode'>" . do_shortcode( $content ). "</div><div class='clearfix'></div>";
	}
}

if ( ! function_exists( 'vw_shortcode_one_third' ) ) {
	function vw_shortcode_one_third(  $atts, $content = null ) {
		return "<div class='one_third column_shortcode'>" . do_shortcode( $content ). "</div>";
	}
}

if ( ! function_exists( 'vw_shortcode_one_third_last' ) ) {
	function vw_shortcode_one_third_last(  $atts, $content = null ) {
		return "<div class='one_third last column_shortcode'>" . do_shortcode( $content ). "</div><div class='clearfix'></div>";
	}
}

if ( ! function_exists( 'vw_shortcode_two_third' ) ) {
	function vw_shortcode_two_third(  $atts, $content = null ) {
		return "<div class='two_third column_shortcode'>" . do_shortcode( $content ). "</div>";
	}
}

if ( ! function_exists( 'vw_shortcode_two_third_last' ) ) {
	function vw_shortcode_two_third_last(  $atts, $content = null ) {
		return "<div class='two_third last column_shortcode'>" . do_shortcode( $content ). "</div><div class='clearfix'></div>";
	}
}

if ( ! function_exists( 'vw_shortcode_one_fourth' ) ) {
	function vw_shortcode_one_fourth(  $atts, $content = null ) {
		return "<div class='one_fourth column_shortcode'>" . do_shortcode( $content ). "</div>";
	}
}

if ( ! function_exists( 'vw_shortcode_one_fourth_last' ) ) {
	function vw_shortcode_one_fourth_last(  $atts, $content = null ) {
		return "<div class='one_fourth last column_shortcode'>" . do_shortcode( $content ). "</div><div class='clearfix'></div>";
	}
}

if ( ! function_exists( 'vw_shortcode_three_fourth' ) ) {
	function vw_shortcode_three_fourth(  $atts, $content = null ) {
		return "<div class='three_fourth column_shortcode'>" . do_shortcode( $content ). "</div>";
	}
}

if ( ! function_exists( 'vw_shortcode_three_fourth_last' ) ) {
	function vw_shortcode_three_fourth_last(  $atts, $content = null ) {
		return "<div class='three_fourth last column_shortcode'>" . do_shortcode( $content ). "</div><div class='clearfix'></div>";
	}
}

if ( ! function_exists( 'vw_shortcode_one_fifth' ) ) {
	function vw_shortcode_one_fifth(  $atts, $content = null ) {
		return "<div class='one_fifth column_shortcode'>" . do_shortcode( $content ). "</div>";
	}
}

if ( ! function_exists( 'vw_shortcode_one_fifth_last' ) ) {
	function vw_shortcode_one_fifth_last(  $atts, $content = null ) {
		return "<div class='one_fifth last column_shortcode'>" . do_shortcode( $content ). "</div><div class='clearfix'></div>";
	}
}

if ( ! function_exists( 'vw_shortcode_two_fifth' ) ) {
	function vw_shortcode_two_fifth(  $atts, $content = null ) {
		return "<div class='two_fifth column_shortcode'>" . do_shortcode( $content ). "</div>";
	}
}

if ( ! function_exists( 'vw_shortcode_two_fifth_last' ) ) {
	function vw_shortcode_two_fifth_last(  $atts, $content = null ) {
		return "<div class='two_fifth last column_shortcode'>" . do_shortcode( $content ). "</div><div class='clearfix'></div>";
	}
}

if ( ! function_exists( 'vw_shortcode_three_fifth' ) ) {
	function vw_shortcode_three_fifth(  $atts, $content = null ) {
		return "<div class='three_fifth column_shortcode'>" . do_shortcode( $content ). "</div>";
	}
}

if ( ! function_exists( 'vw_shortcode_three_fifth_last' ) ) {
	function vw_shortcode_three_fifth_last(  $atts, $content = null ) {
		return "<div class='three_fifth last column_shortcode'>" . do_shortcode( $content ). "</div><div class='clearfix'></div>";
	}
}

/* -----------------------------------------------------------------------------
 * Dropcap
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vw_shortcode_dropcap' ) ) {
	function vw_shortcode_dropcap(  $atts, $content = null ) {
		$defaults = array(
			'style' => 'standard', // standard, circle, box, blackbox, book, shadow
		);
		
		extract( shortcode_atts( $defaults, $atts) );

		return "<span class='dropcap ".esc_attr( $style )."'>{$content}</span>";
	}
}

/* -----------------------------------------------------------------------------
 * Infobox
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vw_shortcode_infobox' ) ) {
	function vw_shortcode_infobox(  $atts, $content = null ) {
		$defaults = array(
			'title' => 'INFORMATION',
		);
		
		extract( shortcode_atts( $defaults, $atts) );

		$content_html = '';
		if ( ! empty( $content ) ) {
			$content_html = '<div class="infobox-content">'.do_shortcode( $content ).'</div>';
		}

		$html = '<div class="infobox"><div class="infobox-inner">';
		$html .= '<h2 class="infobox-title">'.$title.'</h2>';
		$html .= $content_html;
		$html .= '</div></div>';
		return $html;
	}
}

/* -----------------------------------------------------------------------------
 * List
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vw_shortcode_list' ) ) {
	function vw_shortcode_list( $atts, $content = null ) {
		$list_html = '';

		if ( preg_match_all( '|\[list_item.*\].*\[\/list_item\]|Uims', $content, $items, PREG_SET_ORDER ) ) {
			foreach ( $items as $item ) {
				$list_html .= do_shortcode( $item[0] );
			}
		}

		return '<ul class="list">'.$list_html.'</ul>';
	}
}

/* -----------------------------------------------------------------------------
 * List Item
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vw_shortcode_list_item' ) ) {
	function vw_shortcode_list_item( $atts, $content = null ) {
		$defaults = array(
			'icon' => '',
		);
		
		extract( shortcode_atts( $defaults, $atts) );

		$icon_html = '';
		if ( ! empty( $icon ) ) {
			$icon_html = "<i class='icon-".esc_attr( $icon )."'></i> ";
		}

		return "<li>{$icon_html}".do_shortcode( $content )."</li>";
	}
}

/* -----------------------------------------------------------------------------
 * Mark
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vw_shortcode_mark' ) ) {
	function vw_shortcode_mark( $atts, $content = null ) {
		$defaults = array(
			'style' => 'yellow', // grey, dark, yellow
		);
		
		extract( shortcode_atts( $defaults, $atts) );

		return "<mark class='mark mark-".esc_attr( $style )."'>".do_shortcode( $content )."</mark>";
	}
}

/* -----------------------------------------------------------------------------
 * Quote
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vw_shortcode_quote' ) ) {
	function vw_shortcode_quote(  $atts, $content = null ) {
		$defaults = array(
			'align' => 'left', // left, right
		);
		
		extract( shortcode_atts( $defaults, $atts) );

		$align = esc_attr( $align );

		return "<span class='quote quote-{$align} header-font'>".do_shortcode( $content )."</span>";
	}
}

/* -----------------------------------------------------------------------------
 * Tabs
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vw_shortcode_tabs' ) ) {
	function vw_shortcode_tabs( $atts, $content = null ) {
		$defaults = array(
			'style' => 'top-tab', // top-tab, left-tab
			'align' => 'left', // Only style=sidebar / left, right
		);
		
		extract( shortcode_atts( $defaults, $atts) );

		$tabs_html = '';

		if ( preg_match_all( '|\[tab.*\].*\[\/tab\]|Uims', $content, $tabs, PREG_SET_ORDER ) ) {
			$GLOBALS['vw_tab_headers'] = '';
			$GLOBALS['vw_tab_contents'] = '';

			foreach ( $tabs as $item ) {
				do_shortcode( $item[0] );
			}
		}

		return '<div class="tabs style-'.esc_attr( $style ).'"><div class="tab-titles hidden-xs clearfix">'.$GLOBALS['vw_tab_headers'].'</div>'.$GLOBALS['vw_tab_contents'].'<div class="clearfix"></div></div>';
	}
}

/* -----------------------------------------------------------------------------
 * Tab Item
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vw_shortcode_tab_item' ) ) {
	function vw_shortcode_tab_item( $atts, $content = null ) {
		$defaults = array(
			'title' => 'Tab',
			'icon' => '',
		);
		
		extract( shortcode_atts( $defaults, $atts) );

		$icon_html = '';
		if ( ! empty( $icon ) ) {
			$icon_html = "<i class='icon-".esc_attr( $icon )."'></i> ";
		}

		if ( ! isset( $GLOBALS['vw_tab_id'] ) ) {
			$GLOBALS['vw_tab_id'] = 0;
		}
		$GLOBALS['vw_tab_id']++;

		$tab_inner_html = "<a href='#tabpanel{$GLOBALS['vw_tab_id']}'>".$icon_html.$title."</a>";
		$tab_inner_html = $icon_html.$title;

		$GLOBALS['vw_tab_headers'] .= "<a href='#tabpanel{$GLOBALS['vw_tab_id']}' class='tab-title header-font tab-id-{$GLOBALS['vw_tab_id']}' data-tab-id='{$GLOBALS['vw_tab_id']}'>{$tab_inner_html}</a>";
		$GLOBALS['vw_tab_contents'] .= "<h6 class='tab-title header-font full-tab visible-xs tab-id-{$GLOBALS['vw_tab_id']}' data-tab-id='{$GLOBALS['vw_tab_id']}'>{$tab_inner_html}</h6>";
		$GLOBALS['vw_tab_contents'] .= "<div id='tabpanel{$GLOBALS['vw_tab_id']}' class='tab-content'>".do_shortcode( $content )."</div>";
	}
}

/* -----------------------------------------------------------------------------
 * Fix the smk sidebar (using return instead echo)
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vw_smk_sidebar_shortcode' ) ) {
	function vw_smk_sidebar_shortcode( $atts ) {
		extract( shortcode_atts( array(
			'name' => 'Default Sidebar',
		), $atts ) );
		ob_start();
		smk_custom_dynamic_sidebar($name);
		return ob_get_clean();
	}
	remove_shortcode( 'smk_sidebar' );
	add_shortcode( 'smk_sidebar', 'vw_smk_sidebar_shortcode' );
	add_shortcode( 'sidebar', 'vw_smk_sidebar_shortcode' );
}

/* -----------------------------------------------------------------------------
 * Force default width for flexmap
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vw_flexmap_shortcode_attrs' ) ) {
	function vw_flexmap_shortcode_attrs($attrs) {
		if ( ! isset( $attrs['width'] ) )
			$attrs['width'] = '100%';
		
		return $attrs;
	}
	add_filter('flexmap_shortcode_attrs', 'vw_flexmap_shortcode_attrs');
}

/* -----------------------------------------------------------------------------
 * Pre-Process Shortcoedes
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vw_pre_process_shortcode' ) ) {
	function vw_pre_process_shortcode( $content ) {
		// Register Theme's Shortcode
		add_shortcode( 'accordion', 'vw_shortcode_accordion' );
		add_shortcode( 'button', 'vw_shortcode_button' );
		add_shortcode( 'one_half', 'vw_shortcode_one_half' );
		add_shortcode( 'one_half_last', 'vw_shortcode_one_half_last' );
		add_shortcode( 'one_third', 'vw_shortcode_one_third' );
		add_shortcode( 'one_third_last', 'vw_shortcode_one_third_last' );
		add_shortcode( 'two_third', 'vw_shortcode_two_third' );
		add_shortcode( 'two_third_last', 'vw_shortcode_two_third_last' );
		add_shortcode( 'one_fourth', 'vw_shortcode_one_fourth' );
		add_shortcode( 'one_fourth_last', 'vw_shortcode_one_fourth_last' );
		add_shortcode( 'three_fourth', 'vw_shortcode_three_fourth' );
		add_shortcode( 'three_fourth_last', 'vw_shortcode_three_fourth_last' );
		add_shortcode( 'one_fifth', 'vw_shortcode_one_fifth' );
		add_shortcode( 'one_fifth_last', 'vw_shortcode_one_fifth_last' );
		add_shortcode( 'two_fifth', 'vw_shortcode_two_fifth' );
		add_shortcode( 'two_fifth_last', 'vw_shortcode_two_fifth_last' );
		add_shortcode( 'three_fifth', 'vw_shortcode_three_fifth' );
		add_shortcode( 'three_fifth_last', 'vw_shortcode_three_fifth_last' );
		add_shortcode( 'dropcap', 'vw_shortcode_dropcap' );
		add_shortcode( 'infobox', 'vw_shortcode_infobox' );
		add_shortcode( 'list', 'vw_shortcode_list' );
		add_shortcode( 'list_item', 'vw_shortcode_list_item' );
		add_shortcode( 'map', 'flexmap_show_map' );
		add_shortcode( 'mark', 'vw_shortcode_mark' );
		add_shortcode( 'quote', 'vw_shortcode_quote' );
		add_shortcode( 'tabs', 'vw_shortcode_tabs' );
		add_shortcode( 'tab', 'vw_shortcode_tab_item' );
	}

	add_action( 'after_setup_theme', 'vw_pre_process_shortcode' );
	
	// Allow Shortcodes in Widgets
	add_filter( 'widget_text', 'do_shortcode', 10, 2 );
}

/* -----------------------------------------------------------------------------
 * Configure Shortcode Editor
 * -------------------------------------------------------------------------- */

function vw_shortcode_editor_init() {
	$button_style_options = array(
		'primary' => __( 'Primary', 'envirra-backend' ),
		'black' => __( 'Black', 'envirra-backend' ),
		'orange' => __( 'Orange', 'envirra-backend' ),
		'red' => __( 'Red', 'envirra-backend' ),
		'yellow' => __( 'Yellow', 'envirra-backend' ),
		'blue' => __( 'Blue', 'envirra-backend' ),
		'green' => __( 'Green', 'envirra-backend' ),
		'purple' => __( 'Purple', 'envirra-backend' ),
		'pink' => __( 'Pink', 'envirra-backend' ),
	);

	$shortcodes = array();

	$shortcodes[ 'accordion' ] = array(
		'atts' => array(
			'title' => array(
				'title' => __( 'Title', 'envirra-backend' ),
				'desc' => '',
				'default' => '',
				'type' => 'text',
			),
			'open' => array(
				'title' => __( 'Open', 'envirra-backend' ),
				'desc' => __( 'Open this toggle by default', 'envirra-backend' ),
				'default' => 'false',
				'type' => 'dropdown',
				'options' => array(
					'false' => __( 'False', 'envirra-backend' ),
					'true' => __( 'True', 'envirra-backend' ),
				),
			),
			'content' => array(
				'title' => __( 'Content', 'envirra-backend' ),
				'desc' => '',
				'default' => 'Lorem ipsum',
				'type' => 'html',
				'render_as' => 'content',
			),
		),
	);

	$shortcodes[ 'button' ] = array(
		'atts' => array(
			'label' => array(
				'title' => __( 'Label', 'envirra-backend' ),
				'desc' => '',
				'default' => '',
				'type' => 'text',
				'render_as' => 'content',
			),
			'style' => array(
				'title' => __( 'Style', 'envirra-backend' ),
				'desc' => '',
				'default' => 'primary',
				'type' => 'dropdown',
				'options' => $button_style_options,
			),
			'url' => array(
				'title' => __( 'Link to Url', 'envirra-backend' ),
				'desc' => '',
				'default' => '',
				'type' => 'text',
			),
			'target' => array(
				'title' => __( 'Link Target', 'envirra-backend' ),
				'desc' => __( 'The location to open link, enter "_blank" for open the link in new window', 'envirra-backend' ),
				'default' => '_self',
				'type' => 'text',
			),
			'icon' => array(
				'title' => __( 'Icon', 'envirra-backend' ),
				'desc' => '',
				'default' => '',
				'type' => 'icon',
			),
			'fullwidth' => array(
				'title' => __( 'Full Width', 'envirra-backend' ),
				'desc' => __( 'Expand button to fit the container', 'envirra-backend' ),
				'default' => 'false',
				'type' => 'dropdown',
				'options' => array(
					'false' => __( 'False', 'envirra-backend' ),
					'true' => __( 'True', 'envirra-backend' ),
				),
			),
		),
	);

	$shortcodes[ 'dropcap' ] = array(
		'atts' => array(
			'text' => array(
				'title' => __( 'Character', 'envirra-backend' ),
				'desc' => __( 'The character to be a dropcap', 'envirra-backend' ),
				'default' => '',
				'type' => 'text',
				'render_as' => 'content',
			),
		),
	);

	$shortcodes[ 'infobox' ] = array(
		'atts' => array(
			'title' => array(
				'title' => __( 'Title', 'envirra-backend' ),
				'desc' => '',
				'default' => '',
				'type' => 'text',
			),
			'text' => array(
				'title' => __( 'Content', 'envirra-backend' ),
				'desc' => '',
				'default' => '',
				'type' => 'html',
				'render_as' => 'content',
			),
		),
	);

	$shortcodes[ 'mark' ] = array(
		'atts' => array(
			'text' => array(
				'title' => __( 'Text', 'envirra-backend' ),
				'desc' => __( 'the text to be marked', 'envirra-backend' ),
				'default' => '',
				'type' => 'text',
				'render_as' => 'content',
			),
		),
	);

	$shortcodes[ 'quote' ] = array(
		'atts' => array(
			'text' => array(
				'title' => __( 'Text', 'envirra-backend' ),
				'desc' => '',
				'default' => '',
				'type' => 'text',
				'render_as' => 'content',
			),
			'align' => array(
				'title' => __( 'Align', 'envirra-backend' ),
				'desc' => '',
				'default' => 'none',
				'type' => 'dropdown',
				'options' => array(
					'none' => __( 'None', 'envirra-backend' ),
					'left' => __( 'Left', 'envirra-backend' ),
					'right' => __( 'Right', 'envirra-backend' ),
				),
			),
		),
	);

	global $vwsce;
	$vwsce->register_shortcodes( $shortcodes );
}
add_action( 'vwsce_editor_init', 'vw_shortcode_editor_init' );

if ( ! function_exists( 'vwsce_theme_icon_entypo' ) ) {
	function vwsce_theme_icon_entypo( $icons ) {
		return array_merge( $icons, array(
			'icon-entypo-plus', 'icon-entypo-minus', 'icon-entypo-info', 'icon-entypo-left-thin',
			'icon-entypo-up-thin', 'icon-entypo-right-thin', 'icon-entypo-down-thin', 'icon-entypo-level-up',
			'icon-entypo-level-down', 'icon-entypo-switch', 'icon-entypo-infinity', 'icon-entypo-plus-squared',
			'icon-entypo-minus-squared', 'icon-entypo-home', 'icon-entypo-keyboard', 'icon-entypo-erase',
			'icon-entypo-pause', 'icon-entypo-fast-forward', 'icon-entypo-fast-backward', 'icon-entypo-to-end',
			'icon-entypo-to-start', 'icon-entypo-hourglass', 'icon-entypo-stop', 'icon-entypo-up-dir',
			'icon-entypo-play', 'icon-entypo-right-dir', 'icon-entypo-down-dir', 'icon-entypo-left-dir',
			'icon-entypo-adjust', 'icon-entypo-cloud', 'icon-entypo-star', 'icon-entypo-star-empty',
			'icon-entypo-cup', 'icon-entypo-menu', 'icon-entypo-moon', 'icon-entypo-heart-empty',
			'icon-entypo-heart', 'icon-entypo-note', 'icon-entypo-note-beamed', 'icon-entypo-layout',
			'icon-entypo-flag', 'icon-entypo-tools', 'icon-entypo-cog', 'icon-entypo-attention',
			'icon-entypo-flash', 'icon-entypo-record', 'icon-entypo-cloud-thunder', 'icon-entypo-tape',
			'icon-entypo-flight', 'icon-entypo-mail', 'icon-entypo-pencil', 'icon-entypo-feather',
			'icon-entypo-check', 'icon-entypo-cancel', 'icon-entypo-cancel-circled', 'icon-entypo-cancel-squared',
			'icon-entypo-help', 'icon-entypo-quote', 'icon-entypo-plus-circled', 'icon-entypo-minus-circled',
			'icon-entypo-right', 'icon-entypo-direction', 'icon-entypo-forward', 'icon-entypo-ccw',
			'icon-entypo-cw', 'icon-entypo-left', 'icon-entypo-up', 'icon-entypo-down',
			'icon-entypo-list-add', 'icon-entypo-list', 'icon-entypo-left-bold', 'icon-entypo-right-bold',
			'icon-entypo-up-bold', 'icon-entypo-down-bold', 'icon-entypo-user-add', 'icon-entypo-help-circled',
			'icon-entypo-info-circled', 'icon-entypo-eye', 'icon-entypo-tag', 'icon-entypo-upload-cloud',
			'icon-entypo-reply', 'icon-entypo-reply-all', 'icon-entypo-code', 'icon-entypo-export',
			'icon-entypo-print', 'icon-entypo-retweet', 'icon-entypo-comment', 'icon-entypo-chat',
			'icon-entypo-vcard', 'icon-entypo-address', 'icon-entypo-location', 'icon-entypo-map',
			'icon-entypo-compass', 'icon-entypo-trash', 'icon-entypo-doc', 'icon-entypo-doc-text-inv',
			'icon-entypo-docs', 'icon-entypo-doc-landscape', 'icon-entypo-archive', 'icon-entypo-rss',
			'icon-entypo-share', 'icon-entypo-basket', 'icon-entypo-shareable', 'icon-entypo-login',
			'icon-entypo-logout', 'icon-entypo-volume', 'icon-entypo-resize-full', 'icon-entypo-resize-small',
			'icon-entypo-popup', 'icon-entypo-publish', 'icon-entypo-window', 'icon-entypo-arrow-combo',
			'icon-entypo-chart-pie', 'icon-entypo-language', 'icon-entypo-air', 'icon-entypo-database',
			'icon-entypo-drive', 'icon-entypo-bucket', 'icon-entypo-thermometer', 'icon-entypo-down-circled',
			'icon-entypo-left-circled', 'icon-entypo-right-circled', 'icon-entypo-up-circled', 'icon-entypo-down-open',
			'icon-entypo-left-open', 'icon-entypo-right-open', 'icon-entypo-up-open', 'icon-entypo-down-open-mini',
			'icon-entypo-left-open-mini', 'icon-entypo-right-open-mini', 'icon-entypo-up-open-mini', 'icon-entypo-down-open-big',
			'icon-entypo-left-open-big', 'icon-entypo-right-open-big', 'icon-entypo-up-open-big', 'icon-entypo-progress-0',
			'icon-entypo-progress-1', 'icon-entypo-progress-2', 'icon-entypo-progress-3', 'icon-entypo-back-in-time',
			'icon-entypo-network', 'icon-entypo-inbox', 'icon-entypo-install', 'icon-entypo-lifebuoy',
			'icon-entypo-mouse', 'icon-entypo-dot', 'icon-entypo-dot-2', 'icon-entypo-dot-3',
			'icon-entypo-suitcase', 'icon-entypo-flow-cascade', 'icon-entypo-flow-branch', 'icon-entypo-flow-tree',
			'icon-entypo-flow-line', 'icon-entypo-flow-parallel', 'icon-entypo-brush', 'icon-entypo-paper-plane',
			'icon-entypo-magnet', 'icon-entypo-gauge', 'icon-entypo-traffic-cone', 'icon-entypo-cc',
			'icon-entypo-cc-by', 'icon-entypo-cc-nc', 'icon-entypo-cc-nc-eu', 'icon-entypo-cc-nc-jp',
			'icon-entypo-cc-sa', 'icon-entypo-cc-nd', 'icon-entypo-cc-pd', 'icon-entypo-cc-zero',
			'icon-entypo-cc-share', 'icon-entypo-picture', 'icon-entypo-globe', 'icon-entypo-leaf',
			'icon-entypo-graduation-cap', 'icon-entypo-mic', 'icon-entypo-palette', 'icon-entypo-ticket',
			'icon-entypo-video', 'icon-entypo-target', 'icon-entypo-music', 'icon-entypo-trophy',
			'icon-entypo-thumbs-up', 'icon-entypo-thumbs-down', 'icon-entypo-bag', 'icon-entypo-user',
			'icon-entypo-users', 'icon-entypo-lamp', 'icon-entypo-alert', 'icon-entypo-water',
			'icon-entypo-droplet', 'icon-entypo-credit-card', 'icon-entypo-monitor', 'icon-entypo-briefcase',
			'icon-entypo-floppy', 'icon-entypo-cd', 'icon-entypo-folder', 'icon-entypo-doc-text',
			'icon-entypo-calendar', 'icon-entypo-chart-line', 'icon-entypo-chart-bar', 'icon-entypo-clipboard',
			'icon-entypo-attach', 'icon-entypo-bookmarks', 'icon-entypo-book', 'icon-entypo-book-open',
			'icon-entypo-phone', 'icon-entypo-megaphone', 'icon-entypo-upload', 'icon-entypo-download',
			'icon-entypo-box', 'icon-entypo-newspaper', 'icon-entypo-mobile', 'icon-entypo-signal',
			'icon-entypo-camera', 'icon-entypo-shuffle', 'icon-entypo-loop', 'icon-entypo-arrows-ccw',
			'icon-entypo-light-down', 'icon-entypo-light-up', 'icon-entypo-mute', 'icon-entypo-sound',
			'icon-entypo-battery', 'icon-entypo-search', 'icon-entypo-key', 'icon-entypo-lock',
			'icon-entypo-lock-open', 'icon-entypo-bell', 'icon-entypo-bookmark', 'icon-entypo-link',
			'icon-entypo-back', 'icon-entypo-flashlight', 'icon-entypo-chart-area', 'icon-entypo-clock',
			'icon-entypo-rocket', 'icon-entypo-block',
		) );
	}

	add_filter( 'vwsce_icon_list', 'vwsce_theme_icon_entypo' );
}

if ( ! function_exists( 'vwsce_field_render_icon' ) ) {
	function vwsce_field_render_icon() {
		?>
		<link rel="stylesheet" type="text/css" href="/wp-content/themes/reventon/framework/font-icons/entypo/css/entypo.css">
		<?php
	}
	add_action( 'vwsce_after_build_editor', 'vwsce_field_render_icon' );
}