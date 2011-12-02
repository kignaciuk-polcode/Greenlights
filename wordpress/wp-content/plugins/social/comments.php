<div id="social">
<?php
ob_start();
?>
	<?php if (post_password_required()): ?>
	<p class="nopassword"><?php _e('This post is password protected. Enter the password to view any comments.', Social::$i18n); ?></p>
	<?php else: ?>
	<div class="social-post">
		<div id="loading" style="display:none">
			<input type="hidden" id="reload_url" value="<?php echo esc_url(site_url('?'.Social::$prefix.'action=reload_form&redirect_to='.get_permalink(get_the_ID()).'&post_id='.get_the_ID())); ?>" />
			<?php _e('Logging In...', Social::$i18n); ?>
		</div>
		<?php
            if (comments_open()):
			    if (get_option('comment_registration') and !is_user_logged_in()):
        ?>
				<p class="must-log-in"><?php printf(__('You must be <a href="%s">logged in</a> to post a comment.'), wp_login_url(apply_filters('the_permalink', get_permalink(get_the_ID())))); ?></p>
	    <?php
                    do_action('comment_form_must_log_in_after');
			    else:
			        Social_Comment_Form::as_html();
			    endif;
            else:
                do_action('comment_form_comments_closed');
        ?>
		<p class="nocomments"><?php _e('Comments are closed.', Social::$i18n); ?></p>
		<?php endif; ?>
	</div>
<?php
$form = ob_get_clean();

ob_start();
?>
	<div id="social-tabs-comments">
		<?php if (have_comments()): ?>
		<?php
			$groups = array();
			foreach ($comments as $comment) {
				if (empty($comment->comment_type)) {
					$comment_type = get_comment_meta($comment->comment_ID, Social::$prefix.'comment_type', true);
					if (empty($comment_type)) {
						$comment_type = 'wordpress';
					}

					if ($comment_type != 'wordpress') {
						$status_id = get_comment_meta($comment->comment_ID, Social::$prefix.'status_id', true);
						if (empty($status_id)) {
							$comment_type = 'wordpress';
						}
					}
					$comment->comment_type = $comment_type;
				}

				if (!isset($groups[$comment->comment_type])) {
					$groups[$comment->comment_type] = 1;
				}
				else {
					++$groups[$comment->comment_type];
				}
			}
		?>
		<ul class="social-nav social-clearfix">
			<li class="social-all social-tab-main social-current-tab"><a href="#" rel="social-all"><span><?php comments_number(__('0 Replies', Social::$i18n), __('1 Reply', Social::$i18n), __('% Replies', Social::$i18n)); ?></span></a></li>
			<li class="social-wordpress"><a href="#" rel="social-wordpress"><span><?php printf(_n('1 Comment', '%1$s Comments', (isset($groups['wordpress']) ? $groups['wordpress'] : 0), Social::$i18n), (isset($groups['wordpress']) ? $groups['wordpress'] : 0)); ?></span></a></li>
			<li class="social-twitter"><a href="#" rel="social-twitter"><span><?php printf(_n('1 Tweet', '%1$s Tweets', (isset($groups['twitter']) ? $groups['twitter'] : 0), Social::$i18n), (isset($groups['twitter']) ? $groups['twitter'] : 0)); ?></span></a></li>
			<li class="social-facebook"><a href="#" rel="social-facebook"><span><?php printf(_n('1 Facebook', '%1$s Facebook', (isset($groups['facebook']) ? $groups['facebook'] : 0), Social::$i18n), (isset($groups['facebook']) ? $groups['facebook'] : 0)); ?></span></a></li>
			<li class="social-pingback"><a href="#" rel="social-pingback"><span><?php printf(_n('1 Pingback', '%1$s Pingbacks', (isset($groups['pingback']) ? $groups['pingback'] : 0), Social::$i18n), (isset($groups['pingback']) ? $groups['pingback'] : 0)); ?></span></a></li>
		</ul>

		<!-- panel items -->
		<div id="social-comments-tab-all" class="social-tabs-panel social-tabs-first-panel">
			<div id="comments" class="social-comments">
				<div class="social-last-reply-when"><?php printf(__('Last reply was %s ago', Social::$i18n), human_time_diff(strtotime($comments[(count($comments)-1)]->comment_date))); ?></div>
				<ol class="social-commentlist">
					<?php wp_list_comments(array('callback' => array('Social', 'comment'), 'walker' => new Social_Walker_Comment)); ?>
				</ol>
			</div>
		</div>
		<?php else: ?>
		<?php endif; ?>
	</div>
	<!-- #Comments Tabs -->
	<?php endif; ?>
<?php
$comments = ob_get_clean();

$order = apply_filters('social_comment_block_order', array('form', 'comments'));

foreach ($order as $block) {
	if (isset($$block)) {
		echo $$block;
	}
}
?>
</div>
