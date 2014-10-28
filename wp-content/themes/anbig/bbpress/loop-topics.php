<?php

/**
 * Topics Loop
 *
 * @package bbPress
 * @subpackage Theme
 */

?>
<?php
$args = my_custom_display_topic_index_query ();
if($args['order'] == 'DESC'){
	$tpOrder = 'ASC';
}else{
	$tpOrder = 'DESC';
}

parse_str($queryString, $vars);
unset($vars['return']);
$queryString = http_build_query($vars);
?>


<?php do_action( 'bbp_template_before_topics_loop' ); ?>

<ul id="bbp-forum-<?php bbp_forum_id(); ?>" class="bbp-topics">

	<li class="bbp-header">
		<ul class="forum-titles">
			<li class="bbp-topic-title"><a class="<? setOrderSign('title') ?>" href="<?php echo $actual_link = "$queryString?order=$tpOrder&orderby=title";?>"><?php _e( 'Topic', 'bbpress' ); ?></a></li>
            <li class="bbp-topic-freshness"><a class="<? setOrderSign('user') ?>" href="<?php echo $actual_link = "$queryString?order=$tpOrder&orderby=user";?>"><?php _e( 'Post By', 'bbpress' ); ?></a></li>
			<li class="bbp-topic-voice-count"><a class="<? setOrderSign('date') ?>" href="<?php echo $actual_link = "$queryString?order=$tpOrder&orderby=date";?>"><?php _e( 'Post date', 'bbpress' ); ?></a></li>
		</ul>

	</li>

	<li class="bbp-body">

		<?php while ( bbp_topics() ) : bbp_the_topic(); ?>

			<?php bbp_get_template_part( 'loop', 'single-topic' ); ?>

		<?php endwhile; ?>

	</li>

	<li class="bbp-footer">

		<div class="tr">
			<p>
				<span class="td colspan<?php echo ( bbp_is_user_home() && ( bbp_is_favorites() || bbp_is_subscriptions() ) ) ? '5' : '4'; ?>">&nbsp;</span>
			</p>
		</div><!-- .tr -->

	</li>

</ul><!-- #bbp-forum-<?php bbp_forum_id(); ?> -->

<?php do_action( 'bbp_template_after_topics_loop' ); ?>
