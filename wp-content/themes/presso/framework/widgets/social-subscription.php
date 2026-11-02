<?php

if ( ! defined( 'VW_CONST_SOCIAL_COUNTER_CACHE_EXPIRE' ) ) define( 'VW_CONST_SOCIAL_COUNTER_CACHE_EXPIRE', 14400 ); // 60*60*4 = 4 Hrs cache
if ( ! defined( 'VW_CONST_SOCIAL_COUNTER_GOOGLE_API_KEY' ) ) define( 'VW_CONST_SOCIAL_COUNTER_GOOGLE_API_KEY', 'AIzaSyCNyDK8sPUuf9bTcG1TdFFLAVUfA1IDm38' );
if ( ! defined( 'VW_CONST_SOCIAL_COUNTER_FACEBOOK_APP_ID' ) ) define( 'VW_CONST_SOCIAL_COUNTER_FACEBOOK_APP_ID', '463044130538358' );
if ( ! defined( 'VW_CONST_SOCIAL_COUNTER_FACEBOOK_APP_SECRET' ) ) define( 'VW_CONST_SOCIAL_COUNTER_FACEBOOK_APP_SECRET', 'f2baf676b95716d7022df64829f22070' );

add_action( 'after_setup_theme', 'vw_setup_vw_widgets_init_social_subscription' );
function vw_setup_vw_widgets_init_social_subscription() {
	add_action( 'widgets_init', 'vw_widgets_init_social_subscription' );
}

function vw_widgets_init_social_subscription() {
	register_widget( 'Vw_widget_social_subscription' );
}
add_action( 'widgets_init', 'vw_widgets_init_social_subscription' );

class Vw_widget_social_subscription extends WP_Widget {
	private $default = array(
		'supertitle' => '',
		'title' => '',
		'subtitle' => '',
		'twitter' => '',
		'twitter_key' => 'l9e7w4GDJUOvjhNPzGU4Kw',
		'twitter_secret' => 'N1wS0SXR5m41R2fsSm80YO8ymrFGA42X1GuVNVKFgo',
		'facebook' => '',
		'youtube' => '',
		'googleplus' => '',
		'soundcloud' => '',
		'soundcloud_client_id' => 'a4f4e2270a6fc165adfb47af6c18517e',
	);

	public function __construct() {
		// widget actual processes
		parent::__construct(
	 		'vw_widget_social_subscription', // Base ID
			'Envirra Social Subscription', // Name
			array( 'description' => __( 'Display site social with counter', 'envirra' ), ) // Args
		);
	}

	function widget( $args, $instance ) {
		extract( $args );
		$instance = wp_parse_args( $instance, $this->default );

		if ( function_exists( 'icl_t' ) ) {
			$instance['supertitle'] = icl_t( 'PRESSO Widget', $this->id.'_supertitle', $instance['supertitle'] );
			$instance['title'] = icl_t( 'PRESSO Widget', $this->id.'_title', $instance['title'] );
			$instance['subtitle'] = icl_t( 'PRESSO Widget', $this->id.'_subtitle', $instance['subtitle'] );
		}

		$supertitle_html = '';
		if ( ! empty( $instance['supertitle'] ) ) {
			$supertitle_html = sprintf( __( '<span class="super-title">%s</span>', 'envirra' ), $instance['supertitle'] );
		}

		$title_html = '';
		if ( ! empty( $instance['title'] ) ) {
			$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base);
			$title_html = $supertitle_html.$title;
		}
		$subtitle_html = '';
		if ( ! empty( $instance['subtitle'] ) ) {
			$subtitle_html = sprintf( __( '<p class="section-description">%s</p>', 'envirra' ), $instance['subtitle'] );
		}

		$twitter = strip_tags( $instance['twitter'] );
		$twitter_key = strip_tags( $instance['twitter_key'] );
		$twitter_secret = strip_tags( $instance['twitter_secret'] );
		$facebook = strip_tags( $instance['facebook'] );
		$youtube = strip_tags( $instance['youtube'] );
		$googleplus = strip_tags( $instance['googleplus'] );
		$soundcloud = strip_tags( $instance['soundcloud'] );
		$soundcloud_client_id = strip_tags( $instance['soundcloud_client_id'] );

		echo $before_widget;
		if ( $instance['title'] ) echo $before_title . $title_html . $after_title . $subtitle_html;

		if ( $twitter ) {
			$twitter_count = vw_get_twitter_count( $twitter, $twitter_key, $twitter_secret );
			?>
				<div class="social-subscription">
					<a class="social-subscription-icon" href="<?php echo esc_attr( $twitter_count['page_url'] ); ?>" title="<?php esc_attr_e( 'Follow our twitter', 'envirra' ) ?>" target="_blank"><i class="icon-social-twitter"></i></a>
					<div class="social-subscription-counter">
						<div class="social-subscription-count header-font"><?php echo number_format( $twitter_count['count'] ); ?></div>
						<div class="social-subscription-unit"><?php _e( 'followers', 'envirra' ) ?></div>
					</div>
					<div class="clearfix"></div>
				</div>
			<?php
		}

		if ( $facebook ) {
			$facebook_count = vw_get_facebook_count( $facebook );
			?>
				<div class="social-subscription">
					<a class="social-subscription-icon" href="<?php echo esc_attr( $facebook_count['page_url'] ); ?>" title="<?php esc_attr_e( 'Like our facebook', 'envirra' ) ?>" target="_blank"><i class="icon-social-facebook"></i></a>
					<div class="social-subscription-counter">
						<div class="social-subscription-count header-font"><?php echo number_format( $facebook_count['count'] ); ?></div>
						<div class="social-subscription-unit"><?php _e( 'fans', 'envirra' ) ?></div>
					</div>
					<div class="clearfix"></div>
				</div>
			<?php
		}

		if ( $youtube ) {
			$youtube_count = vw_get_youtube_count( $youtube );
			?>
				<div class="social-subscription">
					<a class="social-subscription-icon" href="<?php echo esc_attr( $youtube_count['page_url'] ); ?>" title="<?php esc_attr_e( 'Subscribe our youtube', 'envirra' ) ?>" target="_blank"><i class="icon-social-youtube"></i></a>
					<div class="social-subscription-counter">
						<div class="social-subscription-count header-font"><?php echo number_format( $youtube_count['count'] ); ?></div>
						<div class="social-subscription-unit"><?php _e( 'subscribers', 'envirra' ) ?></div>
					</div>
					<div class="clearfix"></div>
				</div>
			<?php
		}

		if ( $googleplus ) {
			$googleplus_count = vw_get_googleplus_count( $googleplus );
			?>
				<div class="social-subscription">
					<a class="social-subscription-icon" href="<?php echo esc_attr( $googleplus_count['page_url'] ); ?>" title="<?php esc_attr_e( '+1 our page', 'envirra' ) ?>" target="_blank"><i class="icon-social-gplus"></i></a>
					<div class="social-subscription-counter">
						<div class="social-subscription-count header-font"><?php echo number_format( $googleplus_count['count'] ); ?></div>
						<div class="social-subscription-unit"><?php _e( 'people', 'envirra' ) ?></div>
					</div>
					<div class="clearfix"></div>
				</div>
			<?php
		}

		if ( $soundcloud ) {
			$soundcloud_count = vw_get_soundcloud_count( $soundcloud );
			?>
				<div class="social-subscription">
					<a class="social-subscription-icon" href="<?php echo esc_attr( $soundcloud_count['page_url'] ); ?>" title="<?php esc_attr_e( 'Follow our Soundcloud', 'envirra' ) ?>" target="_blank"><i class="icon-social-soundcloud"></i></a>
					<div class="social-subscription-counter">
						<div class="social-subscription-count header-font"><?php echo number_format( $soundcloud_count['count'] ); ?></div>
						<div class="social-subscription-unit"><?php _e( 'Followers', 'envirra' ) ?></div>
					</div>
					<div class="clearfix"></div>
				</div>
			<?php
		}
		?>
		<div class="clearfix"></div>
		<?php

		wp_reset_postdata();
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args( (array) $new_instance, $this->default );
		$instance['supertitle'] = strip_tags( $new_instance['supertitle'] );
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['subtitle'] = strip_tags( $new_instance['subtitle'] );
		$instance['twitter'] = strip_tags( $new_instance['twitter'] );
		$instance['twitter_key'] = strip_tags( $new_instance['twitter_key'] );
		$instance['twitter_secret'] = strip_tags( $new_instance['twitter_secret'] );
		$instance['facebook'] = strip_tags( $new_instance['facebook'] );
		$instance['youtube'] = strip_tags( $new_instance['youtube'] );
		$instance['googleplus'] = strip_tags( $new_instance['googleplus'] );
		$instance['soundcloud'] = strip_tags( $new_instance['soundcloud'] );
		$instance['soundcloud_client_id'] = strip_tags( $new_instance['soundcloud_client_id'] );

		delete_transient( 'vw_social_counter_twitter' );
		delete_transient( 'vw_social_counter_facebook' );
		delete_transient( 'vw_social_counter_youtube' );
		delete_transient( 'vw_social_counter_googleplus' );
		delete_transient( 'vw_social_counter_soundcloud' );

		if ( function_exists( 'icl_register_string' ) ) {
			icl_register_string( 'PRESSO Widget', $this->id.'_supertitle', $instance['supertitle'] );
			icl_register_string( 'PRESSO Widget', $this->id.'_title', $instance['title'] );
			icl_register_string( 'PRESSO Widget', $this->id.'_subtitle', $instance['subtitle'] );
		}

		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->default );

		$supertitle = strip_tags( $instance['supertitle'] );
		$title = strip_tags( $instance['title'] );
		$subtitle = strip_tags( $instance['subtitle'] );
		$twitter = strip_tags( $instance['twitter'] );
		$twitter_key = strip_tags( $instance['twitter_key'] );
		$twitter_secret = strip_tags( $instance['twitter_secret'] );
		$facebook = strip_tags( $instance['facebook'] );
		$youtube = strip_tags( $instance['youtube'] );
		$googleplus = strip_tags( $instance['googleplus'] );
		$soundcloud = strip_tags( $instance['soundcloud'] );
		$soundcloud_client_id = strip_tags( $instance['soundcloud_client_id'] );
		?>

		<!-- super title -->
		<p>
			<label for="<?php echo $this->get_field_id('supertitle'); ?>"><?php _e('Super-title:','envirra-backend'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('supertitle'); ?>" name="<?php echo $this->get_field_name('supertitle'); ?>" type="text" value="<?php echo esc_attr($supertitle); ?>" />
		</p>

		<!-- title -->
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','envirra-backend'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>

		<!-- sub title -->
		<p>
			<label for="<?php echo $this->get_field_id('subtitle'); ?>"><?php _e('Sub-title:','envirra-backend'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('subtitle'); ?>" name="<?php echo $this->get_field_name('subtitle'); ?>" type="text" value="<?php echo esc_attr($subtitle); ?>" />
		</p>

		<!-- twitter username -->
		<p>
			<label for="<?php echo $this->get_field_id('twitter'); ?>"><?php _e('Twitter name:','envirra-backend'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('twitter'); ?>" name="<?php echo $this->get_field_name('twitter'); ?>" type="text" value="<?php echo esc_attr($twitter); ?>" />
		</p>

		<!-- twitter consumer key -->
		<p>
			<label for="<?php echo $this->get_field_id('twitter_key'); ?>"><?php _e('Twitter consumer key:','envirra-backend'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('twitter_key'); ?>" name="<?php echo $this->get_field_name('twitter_key'); ?>" type="text" value="<?php echo esc_attr($twitter_key); ?>" />
		</p>

		<!-- twitter consumer secret -->
		<p>
			<label for="<?php echo $this->get_field_id('twitter_secret'); ?>"><?php _e('Twitter consumer secret:','envirra-backend'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('twitter_secret'); ?>" name="<?php echo $this->get_field_name('twitter_secret'); ?>" type="text" value="<?php echo esc_attr($twitter_secret); ?>" />
		</p>

		<!-- facebook id -->
		<p>
			<label for="<?php echo $this->get_field_id('facebook'); ?>"><?php _e('Facebook ID:','envirra-backend'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('facebook'); ?>" name="<?php echo $this->get_field_name('facebook'); ?>" type="text" value="<?php echo esc_attr($facebook); ?>" />
			<p class="description">The value should be a page id like "<strong>80655071208</strong>".You can find the page id <a href="http://findmyfacebookid.com/" target="_blank">here</a>.</p>
		</p>

		<!-- youtube username -->
		<p>
			<label for="<?php echo $this->get_field_id('youtube'); ?>"><?php _e('Youtube name:','envirra-backend'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('youtube'); ?>" name="<?php echo $this->get_field_name('youtube'); ?>" type="text" value="<?php echo esc_attr($youtube); ?>" />
		</p>

		<!-- googleplus username -->
		<p>
			<label for="<?php echo $this->get_field_id('googleplus'); ?>"><?php _e('Google+ Username/ID:','envirra-backend'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('googleplus'); ?>" name="<?php echo $this->get_field_name('googleplus'); ?>" type="text" value="<?php echo esc_attr($googleplus); ?>" />
			<p class="description">The value should be a name or id like "<strong>+envato</strong>"" or "<strong>107285294994146126204</strong>"</p>
		</p>

		<!-- soundcloud username -->
		<p>
			<label for="<?php echo $this->get_field_id('soundcloud'); ?>">Soundcloud Username:</label>
			<input class="widefat" id="<?php echo $this->get_field_id('soundcloud'); ?>" name="<?php echo $this->get_field_name('soundcloud'); ?>" type="text" value="<?php echo esc_attr($soundcloud); ?>" />
		</p>

		<!-- soundcloud client id -->
		<p>
			<label for="<?php echo $this->get_field_id('soundcloud_client_id'); ?>">Soundcloud Client ID:</label>
			<input class="widefat" id="<?php echo $this->get_field_id('soundcloud_client_id'); ?>" name="<?php echo $this->get_field_name('soundcloud_client_id'); ?>" type="text" value="<?php echo esc_attr($soundcloud_client_id); ?>" />
		</p>

		<?php
	}
}

/* -----------------------------------------------------------------------------
 * Get Counter Functions
 * -------------------------------------------------------------------------- */

if ( ! function_exists( 'vw_save_social_counter' ) ) {
	function vw_save_social_counter( $key, $counter ) {
		if ( is_null( $counter['count'] ) ){
			// When getting counter failed.
			$saved_counter = get_option( $key, array() );

			if ( ! empty( $saved_counter['count'] ) && $saved_counter['page_url'] == $counter['page_url'] ) {
				// Restore previous counter
				$counter['count'] = $saved_counter['count'];
			} else {
				$counter['count'] = 0;
			}

		} else {
			// Save the counter when getting counter successfully.
			update_option( $key, $counter );
		}

		set_transient( $key, $counter, VW_CONST_SOCIAL_COUNTER_CACHE_EXPIRE );

		return $counter;
	}
}

if ( ! function_exists( 'vw_get_twitter_count' ) ) {
	function vw_get_twitter_count( $twitter_id, $consumer_key, $consumer_secret ) {
		$counter_key = 'vw_social_counter_twitter';

		/**
		 * Check for cached version
		 */
		$twitter = get_transient( $counter_key );
		if ( $twitter !== false ) return $twitter;

		/**
		 * Getting counter
		 */
		$twitter = array();
		$twitter['page_url'] = "http://www.twitter.com/$twitter_id";
		$twitter['count'] = null;

		if( $twitter_id && $consumer_key && $consumer_secret ) {
			$token = get_option( 'vw_twitter_token' );
			if( ! $token ) {
				// preparing credentials
				$credentials = $consumer_key . ':' . $consumer_secret;
				$toSend = base64_encode( $credentials );

				// http post arguments
				$args = array(
					'method' => 'POST',
					'httpversion' => '1.1',
					'blocking' => true,
					'sslverify' => false,
					'headers' => array(
						'Authorization' => 'Basic ' . $toSend,
						'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8'
					),
					'body' => array( 'grant_type' => 'client_credentials' )
				);

				$response = wp_remote_post('https://api.twitter.com/oauth2/token', $args);

				$keys = json_decode( wp_remote_retrieve_body( $response ) );

				if( $keys ) {
					// saving token to wp_options table
					update_option( 'vw_twitter_token', $keys->access_token );
					$token = $keys->access_token;
				}
			}

			// we have bearer token wether we obtained it from API or from options
			$args = array(
				'httpversion' => '1.1',
				'blocking' => true,
				'sslverify' => false,
				'headers' => array(
					'Authorization' => "Bearer $token"
				)
			);
		
			$api_url = "https://api.twitter.com/1.1/users/show.json?screen_name=$twitter_id";
			$response = wp_remote_get( $api_url, $args );

			if ( ! is_wp_error( $response ) ) {
				$twitter_reply = json_decode( wp_remote_retrieve_body( $response ) );
				if ( isset( $twitter_reply->followers_count ) ) {
					$twitter['count'] = $twitter_reply->followers_count;
				}
			} else {
				// Delete token for getting a new token on the next request
				delete_option( 'vw_twitter_token' );
			}
		}

		/**
		 * Save counter
		 */
		return vw_save_social_counter( $counter_key, $twitter );
	}
}

if ( ! function_exists( 'vw_get_facebook_count' ) ) {
	function vw_get_facebook_count( $page_id ) {
		$counter_key = 'vw_social_counter_facebook';

		/**
		 * Check for cached version
		 */
		$facebook = get_transient( $counter_key );
		if ( $facebook !== false ) return $facebook;

		/**
		 * Getting counter
		 */
		$facebook = array();
		$facebook['page_url'] = "https://www.facebook.com/".$page_id;
		$facebook['count'] = null;

		if( $page_id ) {
			$token = get_option( 'vw_facebook_token' );
			if( ! $token ) {
				// we have bearer token wether we obtained it from API or from options
				$args = array(
					'httpversion' => '1.1',
					'blocking' => true,
					// 'sslverify' => false,
					'headers' => array(),
				);

				$response = wp_remote_get( 'https://graph.facebook.com/oauth/access_token?client_id='.VW_CONST_SOCIAL_COUNTER_FACEBOOK_APP_ID.'&client_secret='.VW_CONST_SOCIAL_COUNTER_FACEBOOK_APP_SECRET.'&grant_type=client_credentials', $args );

				$body = wp_remote_retrieve_body( $response );

				if( strpos( $body, 'access_token=' ) !== false ) {
					// saving token to wp_options table
					$key = str_replace( 'access_token=', '', $body );
					update_option( 'vw_facebook_token', $key );
					$token = $key;
				} else {
					// Delete token for getting a new token on the next request
					delete_option( 'vw_facebook_token' );
				}
			}

			// we have bearer token wether we obtained it from API or from options
			$args = array(
				'httpversion' => '1.1',
				'blocking' => true,
				// 'sslverify' => false,
				'headers' => array(),
			);

			add_filter( 'https_ssl_verify', '__return_false' );
			$api_url = 'https://graph.facebook.com/v2.3/'.$page_id.'?access_token='.$token;
			$response = wp_remote_get( $api_url, $args );

			if ( ! is_wp_error( $response ) ) {
				$facebook_reply = json_decode( wp_remote_retrieve_body( $response ) );
				if ( isset( $facebook_reply->likes ) ) {
					$facebook['count'] = $facebook_reply->likes;
				}
			} else {
				// Delete token for getting a new token on the next request
				delete_option( 'vw_facebook_token' );
			}
		}

		/**
		 * Save counter
		 */
		return vw_save_social_counter( $counter_key, $facebook );
	}
}

if ( ! function_exists( 'vw_get_instagram_count' ) ) {
	function vw_get_instagram_count( $username ) {
		$counter_key = 'vw_social_counter_instagram';

		/**
		 * Check for cached version
		 */
		$instagram = get_transient( $counter_key );
		if ( $instagram !== false ) return $instagram;

		/**
		 * Getting counter
		 */
		$api_url = 'http://instagram.com/'.$username.'/';
		$instagram = array();
		$instagram['page_url'] = $api_url;
		$instagram['count'] = null;
		
		$raw_page_content = vw_get_subscriber_counter( $api_url ); 

		if ( ! is_wp_error( $raw_page_content ) ) {
			if ( preg_match( '|"followed_by":{"count":([0-9]+)|', $raw_page_content, $match ) ) {
				$instagram['count'] = $match[1];
			}
		}

		/**
		 * Save counter
		 */
		return vw_save_social_counter( $counter_key, $instagram );
	}
}

if ( ! function_exists( 'vw_get_youtube_count' ) ) {
	function vw_get_youtube_count( $channel_id ) {
		$counter_key = 'vw_social_counter_youtube';

		/**
		 * Check for cached version
		 */
		$youtube = get_transient( $counter_key );
		if ( $youtube !== false ) return $youtube;

		/**
		 * Getting counter
		 */
		$youtube = array();
		$youtube['page_url'] = sprintf( 'https://www.youtube.com/channel/%1$s/', $channel_id );
		$youtube['count'] = null;
		$api_url = sprintf( 'https://www.googleapis.com/youtube/v3/channels?part=statistics&id=%1$s&key=%2$s', $channel_id, VW_CONST_SOCIAL_COUNTER_GOOGLE_API_KEY );
		$alt_api_url = sprintf( 'https://www.googleapis.com/youtube/v3/channels?part=statistics&forUsername=%1$s&key=%2$s', $channel_id, VW_CONST_SOCIAL_COUNTER_GOOGLE_API_KEY );

		$data = vw_get_subscriber_counter($api_url);

		if ( ! is_wp_error( $data ) ) {
			$json = json_decode( $data );

			if ( isset( $json->items[0]->statistics->subscriberCount ) ) {
				$youtube['count'] = $json->items[0]->statistics->subscriberCount;
			} else {

				/* Try again with alternative url for getting statistics from user id */
				$youtube['page_url'] = sprintf( 'https://www.youtube.com/user/%1$s/', $channel_id );
				$data = vw_get_subscriber_counter( $alt_api_url );
				$json = json_decode( $data );
				if ( isset( $json->items[0]->statistics->subscriberCount ) ) {
					$youtube['count'] = $json->items[0]->statistics->subscriberCount;
				}
			}
		}

		/**
		 * Save counter
		 */
		return vw_save_social_counter( $counter_key, $youtube );
	}
}

if ( ! function_exists( 'vw_get_googleplus_count' ) ) {
	function vw_get_googleplus_count( $username ) {
		$counter_key = 'vw_social_counter_googleplus';

		/**
		 * Check for cached version
		 */
		$googleplus = get_transient( $counter_key );
		if ( $googleplus !== false ) return $googleplus;

		/**
		 * Getting counter
		 */
		$googleplus = array();
		$googleplus['page_url'] = 'https://plus.google.com/'.$username;
		$googleplus['count'] = null;
		

		if ( preg_match( '/[^0-9]/', $username ) && strpos( $username, '+' ) !== 0 ) {
			// Reformat username
			$username = '+'.$username;
		}
		$api_url = sprintf( 'https://www.googleapis.com/plus/v1/people/%1$s?key=%2$s', $username, VW_CONST_SOCIAL_COUNTER_GOOGLE_API_KEY );
		
		$data = vw_get_subscriber_counter($api_url); 

		if ( ! is_wp_error( $data ) ) {
			$json = json_decode( $data );

			if ( isset( $json->url ) ) {
				$googleplus['page_url'] = $json->url;
			}

			if ( isset( $json->circledByCount ) ) {
				$googleplus['count'] = $json->circledByCount;

			} elseif ( isset( $json->plusOneCount ) ) {
				$googleplus['count'] = $json->plusOneCount;
				
			}
		}

		/**
		 * Save counter
		 */
		vw_save_social_counter( $counter_key, $googleplus );

		return $googleplus;
	}
}

if ( ! function_exists( 'vw_get_vimeo_count' ) ) {
	function vw_get_vimeo_count( $channel_name ) {
		$counter_key = 'vw_social_counter_vimeo';

		/**
		 * Check for cached version
		 */
		$vimeo = get_transient( $counter_key );
		if ( $vimeo !== false ) return $vimeo;

		/**
		 * Getting counter
		 */
		$vimeo = array();
		$vimeo['page_url'] = 'https://vimeo.com/channels/'.$channel_name;
		$vimeo['count'] = null;
		$api_url = 'http://vimeo.com/api/v2/channel/'.$channel_name.'/info.json';
		
		$data = vw_get_subscriber_counter( $api_url ); 

		if ( ! is_wp_error( $data ) ) {
			$json = json_decode( $data );

			if ( isset( $json->url ) ) {
				$vimeo['page_url'] = $json->url;
			}

			if ( isset( $json->total_subscribers ) ) {
				$vimeo['count'] = $json->total_subscribers;
			}
		}

		/**
		 * Save counter
		 */
		return vw_save_social_counter( $counter_key, $vimeo );
	}
}

if ( ! function_exists( 'vw_get_soundcloud_count' ) ) {
	function vw_get_soundcloud_count( $user, $client_id='e15ea601b7aeb07705020236871018e9' ) {
		$counter_key = 'vw_social_counter_soundcloud';

		/**
		 * Check for cached version
		 */
		$soundcloud = get_transient( $counter_key );
		if ( $soundcloud !== false ) return $soundcloud;

		/**
		 * Getting counter
		 */
		$soundcloud['page_url'] = 'https://soundcloud.com/'.$user;
		$soundcloud['count'] = null;
		$api_url = 'http://api.soundcloud.com/users/' . $user . '.json?client_id=' . $client_id;
		
		$data = vw_get_subscriber_counter( $api_url ); 

		if ( ! is_wp_error( $data ) ) {
			$json = json_decode( $data );

			if ( isset( $json->permalink_url ) ) {
				$soundcloud['page_url'] = $json->permalink_url;
			}

			if ( isset( $json->followers_count ) ) {
				$soundcloud['count'] = $json->followers_count;
			}
		}

		/**
		 * Save counter
		 */
		return vw_save_social_counter( $counter_key, $soundcloud );
	}
}

if ( ! function_exists( 'vw_get_pinterest_count' ) ) {
	function vw_get_pinterest_count( $username ) {
		$counter_key = 'vw_social_counter_pinterest';

		/**
		 * Check for cached version
		 */
		$pinterest = get_transient( $counter_key );
		if ( $pinterest !== false ) return $pinterest;

		/**
		 * Getting counter
		 */
		$api_url = 'https://www.pinterest.com/'.$username.'/';
		$pinterest['page_url'] = $api_url;
		$pinterest['count'] = null;
		add_filter( 'https_ssl_verify', '__return_false' );
		$data = vw_get_subscriber_counter( $api_url ); 
		if ( ! is_wp_error( $data ) ) {
			$doc = new DOMDocument();
			@$doc->loadHTML( $data );
			$metas = $doc->getElementsByTagName( 'meta' );
			for ( $i = 0; $i < $metas->length; $i++ ){
				$meta = $metas->item( $i );
				if( $meta->getAttribute('name') == 'pinterestapp:followers' ){
					$pinterest['count'] = $meta->getAttribute( 'content' );
					break;
				}
			}
		}

		/**
		 * Save counter
		 */
		return vw_save_social_counter( $counter_key, $pinterest );
	}
}

if ( ! function_exists( 'vw_get_vk_count' ) ) {
	function vw_get_vk_count( $group_id ) {
		$counter_key = 'vw_social_counter_vk';

		/**
		 * Check for cached version
		 */
		$pinterest = get_transient( $counter_key );
		if ( $pinterest !== false ) return $pinterest;

		/**
		 * Getting counter
		 */
		$vk['page_url'] = 'http://vk.com/'.$group_id;
		$vk['count'] = null;
		$api_url = 'http://api.vk.com/method/groups.getById?gid='.$group_id.'&fields=members_count';
		
		$data = vw_get_subscriber_counter( $api_url ); 

		if ( ! is_wp_error( $data ) ) {
			$json = json_decode( $data );

			if ( isset( $json->response[0]->members_count ) ) {
				$vk['count'] = $json->response[0]->members_count;
			}
		}

		/**
		 * Save counter
		 */
		return vw_save_social_counter( $counter_key, $vk );
	}
}

if ( ! function_exists( 'vw_get_dribbble_count' ) ) {
	function vw_get_dribbble_count( $username ) {
		$counter_key = 'vw_social_counter_dribbble';

		/**
		 * Check for cached version
		 */
		$pinterest = get_transient( $counter_key );
		if ( $pinterest !== false ) return $pinterest;

		/**
		 * Getting counter
		 */
		$dribbble['page_url'] = 'https://dribbble.com/'.$username;
		$dribbble['count'] = null;
		$api_url = 'http://api.dribbble.com/'.$username;
		
		$data = vw_get_subscriber_counter( $api_url ); 

		if ( ! is_wp_error( $data ) ) {
			$json = json_decode( $data );

			if ( isset( $json->url ) ) {
				$dribbble['page_url'] = $json->url;
			}

			if ( isset( $json->followers_count ) ) {
				$dribbble['count'] = $json->followers_count;
			}
		}

		/**
		 * Save counter
		 */
		return vw_save_social_counter( $counter_key, $dribbble );
	}
}

if ( ! function_exists( 'vw_get_subscriber_counter' ) ) {
	function vw_get_subscriber_counter( $api_url ) {
		$args = array(
			'httpversion' => '1.1',
			'blocking' => true,
			'user-agent' => 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36',
		);

		$response = wp_remote_get( $api_url, $args );
		if ( ! is_wp_error( $response ) ) {
			return wp_remote_retrieve_body( $response );
		}
	}
}