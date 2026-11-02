				<footer id="footer">
					<?php get_sidebar( 'footer' ); ?>

					<div class="copyright">
						<div class="container">
							<div class="row">
								<div class="col-sm-6 copyright-left"><?php echo vw_get_option( 'copyright_text' ); ?></div>
								<div class="col-sm-6 copyright-right">
									<a class="back-to-top" href="#top">&uarr;	<?php _e( 'Back to top', 'envirra' ) ?></a>
								</div>
							</div>
						</div>
					</div>
				</footer>
				
			</div> <!-- Off canvas body inner -->
		
		<?php wp_footer(); ?>
	</body>
</html>