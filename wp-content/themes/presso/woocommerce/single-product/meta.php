<?php
/**
 * Single Product Meta
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $product;
?>
<div class="product_meta">

	<?php do_action( 'woocommerce_product_meta_start' ); ?>

	<?php if ( $product->is_type( array( 'simple', 'variable' ) ) && get_option( 'woocommerce_enable_sku' ) == 'yes' && $product->get_sku() ) : ?>
		<span itemprop="productID" class="sku_wrapper"><?php _e( 'SKU:', 'woocommerce' ); ?> <span class="sku"><?php echo $product->get_sku(); ?></span>.</span>
	<?php endif; ?>

	<?php
		$size = sizeof( get_the_terms( $post->ID, 'product_cat' ) );
		if ( $size > 0 ):
	?>
		<div class="post-tags clearfix">
			<?php echo $product->get_categories( ' ', '<span class="post-tags-title header-font posted_in">' . _n( 'Category:', 'Categories:', $size, 'woocommerce' ) . '</span>', '' ); ?>
		</div>
	<?php endif; ?>

	<?php
		$size = sizeof( get_the_terms( $post->ID, 'product_tag' ) );
		if ( $size > 0 ):
	?>
		<div class="post-tags clearfix">
			<?php echo $product->get_tags( ' ', '<span class="post-tags-title header-font tagged_as">' . _n( 'Tag:', 'Tags:', $size, 'woocommerce' ) . '</span>', '' ); ?>
		</div>
	<?php endif; ?>

	<?php do_action( 'woocommerce_product_meta_end' ); ?>

</div>