<!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo('charset'); ?>">
		<title><?php wp_title( '|', true, 'right' ); ?></title>
		
		<!-- Default Meta Tags -->
		<?php get_template_part( '/templates/header-meta' ); ?>
		<?php if ( vw_get_option( 'site_enable_open_graph' ) ) get_template_part( '/templates/facebook-opengraph' ); ?>
		<!-- css + javascript -->
		<?php wp_head(); ?>
		
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
			<script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7/html5shiv.min.js"></script>
			<script src="//cdnjs.cloudflare.com/ajax/libs/respond.js/1.3.0/respond.min.js"></script>
		<![endif]-->

	</head>
	<body id="top" <?php body_class(); ?>>

			<nav id="mobile-nav-wrapper"></nav>
			<div id="off-canvas-body-inner">

				<!-- Top Bar -->
				<div id="top-bar" class="top-bar">
					<div class="container">
						<div class="row">
							<div class="col-sm-12">
								<div class="top-bar-right">

									<?php if( function_exists( 'qtrans_generateLanguageSelectCode' ) ) qtrans_generateLanguageSelectCode( 'image' ); ?>

									<?php vw_render_site_social_icons(); ?>

									<a class="instant-search-icon" href="#menu1"><i class="icon-entypo-search"></i></a>
								</div>

								<a id="open-mobile-nav" href="#mobile-nav" title="<?php esc_attr_e( 'Search', 'envirra' ); ?>"><i class="icon-entypo-menu"></i></a>
								
								<nav id="top-nav-wrapper">
								<?php
								if ( has_nav_menu('top_navigation' ) ) {
									wp_nav_menu( array(
										'theme_location' => 'top_navigation',
										'container' => false,
										'menu_class' => 'top-nav list-unstyled clearfix',
										'link_before' => '<span>',
										'link_after' => '</span>',
										'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
										'depth' => 3,
										'walker' => new vw_text_menu_walker()
									) );
								}
								?>
								</nav>
								
							</div>
						</div>
					</div>
				</div>
				<!-- End Top Bar -->
				
				<!-- Main Bar -->
				<?php $header_layout = vw_get_option( 'header_layout', 'left-logo' ); ?>
				<header class="main-bar <?php echo 'header-layout-'.esc_attr( $header_layout ); ?>">
					<div class="container">
						<div class="row">
							<div class="col-sm-12">
								<div id="logo" class="">
									<a href="<?php echo esc_url( home_url() ); ?>/">
									<?php $logo = vw_get_option( 'logo' ); ?>
									<?php $logo_2x = vw_get_option( 'logo_2x' ); ?>

									<?php if ( ! empty( $logo[ 'url' ] ) ): ?>
										<?php if ( ! empty( $logo_2x[ 'url' ] ) ): ?>
											<img src="<?php echo esc_url( $logo_2x[ 'url' ] ); ?>" width="<?php echo esc_attr( $logo[ 'width' ] ); ?>" height="<?php echo esc_attr( $logo[ 'height' ] ); ?>" alt="<?php esc_attr( bloginfo( 'name' ) ); ?>" class="logo-retina" />
										<?php endif; ?>
										<img src="<?php echo esc_url( $logo[ 'url' ] ); ?>" width="<?php echo esc_attr( $logo[ 'width' ] ); ?>" height="<?php echo esc_attr( $logo[ 'height' ] ); ?>" alt="<?php esc_attr( bloginfo( 'name' ) ); ?>" class="logo-original" />

									<?php else : ?>
										<h1 id="site-title" class="title title-large"><?php bloginfo( 'name' ); ?></h1>
									<?php endif; ?>
									
									<?php if ( vw_get_option( 'show_site_tagline' ) && get_bloginfo( 'description' ) ): ?>
										<h2 id="site-tagline" class="subtitle"><?php bloginfo( 'description' ) ?></h2>
									<?php endif; ?>
									</a>
								</div>
							
								<?php if( vw_get_option( 'header_ads_code' ) != '' ) : ?>
								<div class="header-ads">
									<?php echo do_shortcode( vw_get_option( 'header_ads_code' ) ) ; ?>
								</div>
								<?php endif; ?>
								
							</div>
						</div>
					</div>
				</header>
				<!-- End Main Bar -->

				<!-- Main Navigation Bar -->
				<div class="main-nav-bar <?php echo 'header-layout-'.esc_attr( $header_layout ); ?>">
					<div class="container">
						<div class="row">
							<div class="col-sm-12">
								<?php
								if ( has_nav_menu('main_navigation' ) ) {
									wp_nav_menu( array(
										'theme_location' => 'main_navigation',
										'container' => false,
										'menu_class' => 'main-nav list-unstyled',
										'link_before' => '<span>',
										'link_after' => '</span>',
										'items_wrap' => '<nav id="main-nav-wrapper"><ul id="%1$s" class="%2$s">%3$s</ul></nav>',
										'depth' => 2,
										'walker' => new vw_main_menu_walker()
									) );
								}
								?>
							</div>
						</div>
					</div>
				</div>
				<!-- End Main Navigation Bar -->