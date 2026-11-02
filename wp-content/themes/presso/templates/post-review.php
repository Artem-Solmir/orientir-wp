<?php
if ( '1' == get_post_meta( get_the_id(), 'vw_enable_review', true ) ) :
?>
<div class="review-box clearfix" itemprop="review" itemscope itemtype="http://schema.org/Review">
	<meta itemprop="name" content="<?php echo esc_attr( get_the_title() ); ?>">
	<meta itemprop="author" content="<?php echo esc_attr( get_the_author() ); ?>">
	<meta itemprop="datePublished" content="<?php echo esc_attr( get_the_date( 'Y-m-d' ) ); ?>">

	<h3 class="review-box-title vw-slabtext2"><?php _e( "Editor's Rating", 'envirra' ); ?></h3>

	<?php $review_summary = esc_html( get_post_meta( get_the_id(), 'vw_review_summary', true ) );
	if ( ! empty( $review_summary ) ) : ?>
	<p class="review-box-summary" itemprop="description"><?php echo $review_summary; ?></p>
	<?php endif; ?>

	<div class="review-box-dial">
		<?php $avg_score = get_post_meta( get_the_id(), 'vw_review_average_score', true ); ?>
		<input type="text" value="<?php echo esc_attr( $avg_score ); ?>" class="dial" data-readOnly="true" data-width="150" data-height="150" data-thickness="0.12" data-min="0" data-max="10" data-displayPrevious="true" data-fgColor="<?php echo vw_get_option( 'color_primary' ); ?>">
	</div>
	
	<div class="chart-bars">
		<?php
		$counter = 1;
		for( $counter=1; $counter<=10; $counter++ ) :
			$label = get_post_meta( get_the_id(), 'vw_review_score_'.$counter.'_label', true );
			$score = get_post_meta( get_the_id(), 'vw_review_score_'.$counter.'_score', true );

			if ( empty( $score ) ) break;
		?>
		<div class="chart-bar">
			<div class="chart-bar-value header-font"><?php echo esc_html( $score ) ?></div>
			<div class="chart-bar-title header-font"><?php echo esc_html( $label ) ?></div>
			<div class="chart-bar-bg"><div class="chart-bar-percent" style="width: <?php echo esc_attr( $score / 10 * 100 ); ?>%;"></div></div>
		</div>
		<?php endfor; ?>

	</div>
	<div class="hidden" itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">
		<meta itemprop="worstRating" content="0">
		<meta itemprop="bestRating" content="10">
		<meta itemprop="ratingValue" content="<?php echo esc_attr( $avg_score ); ?>">
	</div>
</div>
<?php endif; ?>