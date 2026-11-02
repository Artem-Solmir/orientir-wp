<?php 

add_action( 'after_setup_theme', 'vw_setup_user_profile_fields' );
function vw_setup_user_profile_fields() {
	add_action( 'show_user_profile', 'vw_user_profile_social_fields' );
	add_action( 'edit_user_profile', 'vw_user_profile_social_fields' );
	add_action( 'personal_options_update', 'vw_save_user_profile_social_fields' );
	add_action( 'edit_user_profile_update', 'vw_save_user_profile_social_fields' );
}

if ( ! function_exists( 'vw_user_profile_social_fields' ) ) {
	function vw_user_profile_social_fields( $user ) {
		?>
		<h3><?php _e('Social Profiles', 'envirra-backend'); ?></h3>
		
		<table class="form-table">
			<tr>
				<th>
					<label for="vw_user_twitter"><?php _e('Twitter', 'envirra-backend'); ?>
					</label>
				</th>
				<td>
					<input type="text" name="vw_user_twitter" id="vw_user_twitter" value="<?php echo esc_attr( get_the_author_meta( 'vw_user_twitter', $user->ID ) ); ?>" class="regular-text" />
				</td>
			</tr>
			<tr>
				<th>
					<label for="vw_user_facebook"><?php _e('Facebook', 'envirra-backend'); ?>
					</label>
				</th>
				<td>
					<input type="text" name="vw_user_facebook" id="vw_user_facebook" value="<?php echo esc_attr( get_the_author_meta( 'vw_user_facebook', $user->ID ) ); ?>" class="regular-text" />
				</td>
			</tr>
			<tr>
				<th>
					<label for="vw_user_google"><?php _e('Google+', 'envirra-backend'); ?>
					</label>
				</th>
				<td>
					<input type="text" name="vw_user_google" id="vw_user_google" value="<?php echo esc_attr( get_the_author_meta( 'vw_user_google', $user->ID ) ); ?>" class="regular-text" />
				</td>
			</tr>
			<tr>
				<th>
					<label for="vw_user_pinterest"><?php _e('Pinterest', 'envirra-backend'); ?>
					</label>
				</th>
				<td>
					<input type="text" name="vw_user_pinterest" id="vw_user_pinterest" value="<?php echo esc_attr( get_the_author_meta( 'vw_user_pinterest', $user->ID ) ); ?>" class="regular-text" />
				</td>
			</tr>
		</table>
	<?php
	}
}

if ( ! function_exists( 'vw_save_user_profile_social_fields' ) ) {
	function vw_save_user_profile_social_fields( $user_id ) {
		 if ( !current_user_can( 'edit_user', $user_id ) )
		return false;
	
		update_user_meta( $user_id, 'vw_user_twitter', $_POST['vw_user_twitter'] );
		update_user_meta( $user_id, 'vw_user_facebook', $_POST['vw_user_facebook'] );
		update_user_meta( $user_id, 'vw_user_google', $_POST['vw_user_google'] );
		update_user_meta( $user_id, 'vw_user_pinterest', $_POST['vw_user_pinterest'] );
	}
}