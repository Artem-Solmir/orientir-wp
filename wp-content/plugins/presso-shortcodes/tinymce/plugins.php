<?php

add_action('admin_head', 'vw_shortcode_editor_settings');
function vw_shortcode_editor_settings(){
	?>
	<script type="text/javascript">
		var vw_theme_shortcodes = [
			{
				title: 'Accordion',
				shortcode: '[accordion]',
			},
			{
				title: 'Button',
				shortcode: '[button]',
			},
			{
				title: 'Columns',
				submenu: [
					{
						title: '[1/2] + [1/2]',
						instant_shortcode: "<p>[one_half][/one_half]</p><p>[one_half_last][/one_half_last]</p>",
					},
					{
						title: '[1/3] + [1/3] + [1/3]',
						instant_shortcode: "<p>[one_third][/one_third]</p><p>[one_third][/one_third]</p><p>[one_third_last][/one_third_last]</p>",
					},
					{
						title: '[2/3] + [1/3]',
						instant_shortcode: "<p>[two_third][/two_third]</p><p>[one_third_last][/one_third_last]</p>",
					},
					{
						title: '[1/4] + [1/4] + [1/4] + [1/4]',
						instant_shortcode: "<p>[one_fourth][/one_fourth]</p><p>[one_fourth][/one_fourth]</p><p>[one_fourth][/one_fourth]</p><p>[one_fourth_last][/one_fourth_last]</p>",
					},
					{
						title: '[3/4] + [1/4]',
						instant_shortcode: "<p>[three_fourth][/three_fourth]</p><p>[one_fourth_last][/one_fourth_last]</p>",
					},
				],
			},
			{
				title: 'Info Box',
				shortcode: '[infobox]',
			},
			{
				title: 'List',
				instant_shortcode: '<p>[list]</p>'
									+'<p>[list_item icon="entypo-info"][/list_item]</p>'
									+'<p>[/list]</p>'
			},
			{
				title: 'Map',
				instant_shortcode: '<p>[flexiblemap address="Bangkok, Thailand" title="Label Here"]</p>'
			},
			{
				title: 'Tabs',
				instant_shortcode: '<p>[tabs]</p>'
									+ '<p>[tab title="Responsive" icon="entypo-book"]CONTENT HERE[/tab]</p>'
									+ '<p>[/tabs]</p>'
			},
			{
				title: 'Typography',
				submenu: [
					{
						title: 'Dropcap',
						shortcode: '[dropcap]',
					},
					{
						title: 'Mark',
						shortcode: '[mark]',
					},
					{
						title: 'Quote',
						shortcode: '[quote]',
					},
				]
			},
		];
	</script>
	<?php
}

add_action('admin_head', 'vw_init_shortcode_editor');
function vw_init_shortcode_editor() {
	add_filter('mce_external_plugins', 'vw_add_shortcode_editor_plugins');
	add_filter('mce_buttons', 'vw_add_shortcode_editor_buttons');
}

function vw_add_shortcode_editor_plugins($plugin_array) {
	$plugin_array['vw_shortcodes'] = VWSC_URI.'/tinymce/plugins.js?'.VW_THEME_VERSION;

	return $plugin_array;
}
 
function vw_add_shortcode_editor_buttons($buttons) {
	array_push($buttons, 'vw_shortcodes');
	return $buttons;
}