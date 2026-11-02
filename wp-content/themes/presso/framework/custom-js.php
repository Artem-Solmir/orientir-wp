<?php

add_action( 'after_setup_theme', 'vw_setup_custom_js' );
function vw_setup_custom_js() {
	add_action( 'wp_footer', 'vw_render_footer_js', 99 );
	add_action( 'vw_render_custom_js', 'vw_render_default_flexslider' );
}

/* -----------------------------------------------------------------------------
 * Render Custom JS
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vw_render_footer_js' ) ) {
	function vw_render_footer_js() {
		/**
		 * Render Tracking Code
		 */
		echo vw_get_option( 'tracking_code' );

		/**
		 * Render Custom JS
		 */
		?>
		<script type='text/javascript'>
			;(function( $, window, document, undefined ){
				"use strict";

				$( document ).ready( function ($) {
					<?php do_action( 'vw_render_custom_js' ); ?>
				} );
				
			})( jQuery, window , document );

			<?php echo vw_get_option( 'custom_js' ); ?>
		</script>
		<?php 
	}
}

/* -----------------------------------------------------------------------------
 * Render Flexslider
 * -------------------------------------------------------------------------- */
if ( ! function_exists( 'vw_render_default_flexslider' ) ) {
	function vw_render_default_flexslider() {
	?>
		$( '.flexslider' ).flexslider({
			animation: "<?php echo vw_get_option( 'flexslider_animation' ); ?>",
			easing: "<?php echo vw_get_option( 'flexslider_easing' ); ?>",
			slideshow: <?php echo vw_get_option( 'flexslider_slideshow' ) ? 'true' : 'false'; ?>,
			slideshowSpeed: <?php echo vw_get_option( 'flexslider_slideshowspeed' ); ?>,
			animationSpeed: <?php echo vw_get_option( 'flexslider_animationspeed' ); ?>,
			randomize: <?php echo vw_get_option( 'flexslider_randomize' ) ? 'true' : 'false'; ?>,
			pauseOnHover: <?php echo (boolean)vw_get_option( 'flexslider_pauseonhover' ) ? 'true' : 'false'; ?>,
			prevText: '',
			nextText: '',
			start: function( slider ) {
				slider.css( 'opacity', '1' );
				slider.find( '.post-thumbnail-wrapper' ).css( 'height', '500px' ).imgLiquid().fadeIn(250);
			},
		});
	<?php
	}
}