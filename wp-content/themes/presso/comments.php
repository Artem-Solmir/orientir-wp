<?php if ( comments_open() ) : ?>
<div id="comments" class="section-container clearfix">
	<?php
	
		if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME'])) {
			die ('Please do not load this page directly. Thanks!');
		}
	
		if ( post_password_required() ) {
			_e('This post is password protected. Enter the password to view comments.', 'envirra');
			echo '</div>';
			return;
		}
	?>
	
	<h3 class="section-title"><?php comments_number( __('No Comment', 'envirra'), __('<span>1</span> Comment', 'envirra'), __('<span>%</span> Comments', 'envirra') );?></h3>
	
	<?php if ( have_comments() ) : ?>
	
		<div class="navigation">
			<div class="next-posts"><?php previous_comments_link() ?></div>
			<div class="prev-posts"><?php next_comments_link() ?></div>
		</div>
	
		<ol class="commentlist clearfix">
			<?php wp_list_comments( array( 'before' => ' &mdash; ', 'callback' => 'vw_render_comments' ) ); ?>
		</ol>
	
		<div class="navigation">
			<div class="next-posts"><?php previous_comments_link() ?></div>
			<div class="prev-posts"><?php next_comments_link() ?></div>
		</div>
		
	<?php endif; ?>

	<?php
	
		$req = get_option( 'require_name_email' );
		$aria_req = ( $req ? " aria-required='true'" : '' );

		//Custom Fields
		$fields =  array(
			'author'=> '<div id="respond-inputs" class="clearfix"><p><input name="author" type="text" placeholder="' . __('Name (required)', 'envirra') . '" size="30"' . $aria_req . ' /></p>',
			'email' => '<p><input name="email" type="text" placeholder="' . __('E-Mail (required)', 'envirra') . '" size="30"' . $aria_req . ' /></p>',
			'url' => '<p class="last"><input name="url" type="text" placeholder="' . __('Website', 'envirra') . '" size="30" /></p></div>',
		);

		//Comment Form Args
        $comments_args = array(
			'fields' => $fields,
			'title_reply'=> __('Leave a reply', 'envirra'),
			'comment_field' => '<div id="respond-textarea"><p><textarea id="comment" name="comment" aria-required="true" cols="58" rows="10" tabindex="4"></textarea></p></div>',
			'label_submit' => __('Submit comment','envirra')
		);
		
		// Show Comment Form
		comment_form($comments_args); 
	?>
</div>
<?php endif; ?>