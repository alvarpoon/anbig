<?
	$post_id = $post->ID;
	$video_lecture = get_field("video_lecture",$post_id);
	$image_archives_banner = get_field("image_archives_banner",$post_id);
	$activities_banner = get_field("activities_banner",$post_id);
	$discussion_forum_banner = get_field("discussion_forum_banner",$post_id);
	$nbi_quiz_banner = get_field("nbi_quiz_banner",$post_id);
?>
<section id="home-hero" class="hero">
	<div class="container">
        <div id="main-banner-container" >
            <?
                $args = array( 'numberposts' => -1, 'post_type' => 'mainpage_banner', 'post_status' => 'publish', 'order' => 'ASC', 'orderby' => 'menu_order');
              $results = get_posts( $args );
              foreach( $results as $result ) :
                $url = wp_get_attachment_image_src( get_post_thumbnail_id($result->ID), 'mainpage-banner');
            ?>
                <div class="main-banner">
                    <img class="img-responsive" src="<?=$url[0]?>" />
                </div>
            <? endforeach;?>
        </div>
	</div>
</section>
<section>
	<div class="container">
		<div class="row">
			<div class="col-sm-4 col-xs-6 smallPadding-xs">
				<h2 class="videoHomeHeader">Videos</h2>

			</div>
			<div class="col-sm-4 col-xs-6 smallPadding-xs">
				<h2 class="imageHomeHeader">Image Archives</h2>
				<?
				if(!empty($image_archives_banner)){
					echo '<img class="img-responsive fullWidthImg" src="'.$image_archives_banner['sizes']['listing-image'].'">';
				}
				?>
			</div>
			<div class="col-sm-4 hidden-xs">
				<h2 class="activitiesHomeHeader">Activities</h2>
				<?
				if(!empty($activities_banner)){
					echo '<img class="img-responsive fullWidthImg" src="'.$activities_banner['sizes']['listing-image'].'">';
				}
				?>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-6">
				<h2 class="discussionHomeHeader">Discussion Forum</h2>
				<?
				if(!empty($discussion_forum_banner)){
					echo '<img class="img-responsive fullWidthImg" src="'.$discussion_forum_banner['sizes']['mainpage-section-image'].'">';
				}
				?>
			</div>
			<div class="col-sm-6">
				<h2 class="quizHomeHeader">NBI Quiz</h2>
				<?
				if(!empty($nbi_quiz_banner)){
					echo '<img class="img-responsive fullWidthImg" src="'.$nbi_quiz_banner['sizes']['mainpage-section-image'].'">';
				}
				?>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-6 clearfix">
				<h2 class="activitesListHomeHeader">Activities</h2>
				<ul class="main-list">
				<?
					$args = array( 'numberposts' => 2, 'post_type' => 'activity', 'post_status' => 'publish', 'order' => 'DESC', 'orderby' => 'date');
				  $results = get_posts( $args );
				  foreach( $results as $result ) :
				  	$link = get_permalink($result->ID);
				?>
					<li>
						<p class="date"><?=get_field("date",$result->ID)?></p>
						<div>
							<p class="title"><?=$result->post_title?></p>
							<p class="location"><?=get_field("venue",$result->ID)?></p>
						</div>
					</li>
				<? endforeach;?>
				</ul>
				<a class="pull-right" href="<?=site_url()?>/activities/">View all activities</a>
			</div>
			<div class="col-sm-6 clearfix">
				<h2 class="newsListHomeHeader">Latest News</h2>
				<ul class="main-list">
				<?
					$args = array( 'numberposts' => 2, 'post_type' => 'post', 'post_status' => 'publish', 'order' => 'DESC', 'orderby' => 'date');
				  $results = get_posts( $args );
				  foreach( $results as $result ) :
				  	$link = get_permalink($result->ID);
				?>
					<li>
						<p class="date"><?=get_field("speaker",$result->ID)?></p>
						<div>
							<p class="title"><?=$result->post_title?></p>
							<p class="location"><?=get_field("location",$result->ID)?></p>
						</div>
					</li>
				<? endforeach;?>
				</ul>
				<a class="pull-right" href="<?=site_url()?>/latest-news/">View all news</a>
			</div>
		</div>
	</div>
</section>