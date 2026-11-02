<link href="//www.google-analytics.com" rel="dns-prefetch">

<meta http-equiv="X-UA-Compatible" content="IE=edge">

<meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=yes">

<?php if( vw_get_option( 'site_enable_meta_description' ) ) { ?><meta name="description" content="<?php bloginfo('description'); ?>"><?php } ?>

<?php $fav_icon = vw_get_option( 'fav_icon' );
if( ! empty( $fav_icon['url'] ) ) : ?><link rel="shortcut icon" href="<?php echo esc_url( $fav_icon['url'] ); ?>"><?php endif; ?>
		
<?php $fav_icon_iphone = vw_get_option( 'fav_icon_iphone' );
if( ! empty( $fav_icon_iphone['url'] ) ) : ?><link rel="apple-touch-icon" href="<?php echo esc_url( $fav_icon_iphone['url'] ); ?>"><?php endif; ?>

<?php $fav_icon_iphone_retina = vw_get_option( 'fav_icon_iphone_retina' );
if( ! empty( $fav_icon_iphone_retina['url'] ) ) : ?><link rel="apple-touch-icon" sizes="114x114" href="<?php echo esc_url( $fav_icon_iphone_retina['url'] ); ?>"><?php endif; ?>

<?php $fav_icon_ipad = vw_get_option( 'fav_icon_ipad' );
if( ! empty( $fav_icon_ipad['url'] ) ) : ?><link rel="apple-touch-icon" sizes="72x72" href="<?php echo esc_url( $fav_icon_ipad['url'] ); ?>"><?php endif; ?>

<?php $fav_icon_ipad_retina = vw_get_option( 'fav_icon_ipad_retina' );
if( ! empty( $fav_icon_ipad_retina['url'] ) ) : ?><link rel="apple-touch-icon" sizes="144x144" href="<?php echo esc_url( $fav_icon_ipad_retina['url'] ); ?>"><?php endif; ?>

<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>