<?php 
if ( ! is_active_sidebar( 'footer-sidebar-1'  )
	&& ! is_active_sidebar( 'footer-sidebar-2' )
	&& ! is_active_sidebar( 'footer-sidebar-3'  )
) { return; }
?>
<div class="footer-sidebar">
	<div class="container">
		<div class="row">
			<?php if ( is_active_sidebar( 'footer-sidebar-1' ) ) : ?>
				<div class="footer-sidebar-1 widget-area col-sm-4" role="complementary">
				<?php dynamic_sidebar( 'footer-sidebar-1' ); ?>
				</div>
			<?php endif; ?>

			<?php if ( is_active_sidebar( 'footer-sidebar-2' ) ) : ?>
				<div class="footer-sidebar-2 widget-area col-sm-4" role="complementary">
				<?php dynamic_sidebar( 'footer-sidebar-2' ); ?>
				</div>
			<?php endif; ?>

			<?php if ( is_active_sidebar( 'footer-sidebar-3' ) ) : ?>
				<div class="footer-sidebar-3 widget-area col-sm-4" role="complementary">
				<?php dynamic_sidebar( 'footer-sidebar-3' ); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>