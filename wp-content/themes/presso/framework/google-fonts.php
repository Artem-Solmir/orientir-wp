<?php

global $Redux_Options;

$default_fonts = array(
	"'Custom Font 1'",
	"'Custom Font 2'",
	"Arial, 'Helvetica Neue', Helvetica",
	"'Arial Narrow', Arial",
	"'Century Gothic', CenturyGothic, AppleGothic",
	"'Helvetica Neue', Helvetica, Arial",
	"Georgia, Times, 'Times New Roman', serif",
	"Palatino, 'Palatino Linotype', 'Palatino LT STD', 'Book Antiqua', Georgia, serif",
	"Verdana, Geneva",
	"Tahoma, Verdana, Segoe",
	"TimesNewRoman, 'Times New Roman', Times, Baskerville, Georgia, serif",
	"'Trebuchet MS', 'Lucida Grande', 'Lucida Sans Unicode', 'Lucida Sans', Tahoma, sans-serif",
);

$google_fonts = array(
	$Redux_Options->options['h1'],
	$Redux_Options->options['body'],
);

$font_list = array();
foreach ( $google_fonts as $font ) {
	if ( ! in_array( $font['family'], $default_fonts ) ) {
		$font_name = str_replace(' ', '+', $font['family']);

		if ( ! isset( $font_list[ $font_name ] ) ) {
			$font_list[ $font_name ] = $font_name. ':400,400italic,700,700italic';
		}

		if ( isset( $font['weight'] ) ) {
			$extra_weight = sprintf( ',%1$s,%1$sitalic', $font['weight'] );
			$font_list[ $font_name ] .= $extra_weight;
		}
	}
}

if ( ! empty( $font_list ) ) {
	echo "<link href='".esc_url( "//fonts.googleapis.com/css?family=" . implode( '|' , $font_list ) . "&subset=latin,latin-ext,cyrillic,cyrillic-ext,greek-ext,greek,vietnamese" )."' rel='stylesheet' type='text/css'>";
}