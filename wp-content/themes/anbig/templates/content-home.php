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
				<h2 class="videoHomeHeader"><a href="<?=site_url()?>/vid/">Videos</a></h2>
			<?
				$args = array( 'numberposts' => 1, 'post_type' => 'video', 'post_status' => 'publish', 'order' => 'DESC', 'orderby' => 'date');
				  $results = get_posts( $args );
				  //foreach( $results as $result ){
				  	$result = $results[0];
				  	$id = $result->ID;
				  	$link = get_permalink($id);
				  	$post = get_post($id);
				    $video_id = get_field('video',$id);
				    $video_url = wp_get_attachment_url( $video_id );
				    $image_url = wp_get_attachment_image_src( get_post_thumbnail_id($id), 'listing-image' );
				    echo '<a class="videoLink" href="'.$video_url.'" person="'.get_field("doctor",$id).'" desp="'.apply_filters('the_content', $post->post_content).'"><img class="img-responsive fullwidthImg" src="'.$image_url[0].'" /></a>';
				  //}
			?>
			</div>
			<div class="col-sm-4 col-xs-6 smallPadding-xs">
				<h2 class="imageHomeHeader"><a href="<?=site_url()?>/image-archives/">Image Archives</a></h2>
				<?
				if(!empty($image_archives_banner)){
					echo '<a href="'.site_url().'/image-archives/"><img class="img-responsive fullWidthImg" src="'.$image_archives_banner['sizes']['listing-image'].'"></a>';
				}
				?>
			</div>
			<div class="col-sm-4 hidden-xs">
				<h2 class="activitiesHomeHeader"><a href="<?=site_url()?>/activities/">Activities</a></h2>
				<?
				if(!empty($activities_banner)){
					echo '<a href="'.site_url().'/activities/"><img class="img-responsive fullWidthImg" src="'.$activities_banner['sizes']['listing-image'].'"></a>';
				}
				?>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-6">
				<h2 class="discussionHomeHeader"><a href="<?=site_url()?>/forum/discussion-forum-2/">Discussion Forum</a></h2>
				<?
				if(!empty($discussion_forum_banner)){
					echo '<a href="'.site_url().'/forum/discussion-forum-2/"><img class="img-responsive fullWidthImg" src="'.$discussion_forum_banner['sizes']['mainpage-section-image'].'"></a>';
				}
				?>
			</div>
			<div class="col-sm-6">
				<h2 class="quizHomeHeader"><a href="<?=site_url()?>/nbi-quiz/">NBI Quiz</a></h2>
				<?
				if(!empty($nbi_quiz_banner)){
					echo '<a href="'.site_url().'/nbi-quiz/"><img class="img-responsive fullWidthImg" src="'.$nbi_quiz_banner['sizes']['mainpage-section-image'].'"></a>';
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
					<li class="clearfix">
						<p class="date"><?=get_field("date",$result->ID)?></p>
						<div>
							<p class="title"><?=$result->post_title?></p>
							<p class="location"><?=get_field("venue",$result->ID)?></p>
						</div>
					</li>
				<? endforeach;?>
				</ul>
				<a class="pull-right viewAllBtn" href="<?=site_url()?>/activities/">View all activities</a>
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
					<li class="clearfix">
						<p class="date"><?=get_field("speaker",$result->ID)?></p>
						<div>
							<p class="title"><?=$result->post_title?></p>
							<p class="location"><?=get_field("location",$result->ID)?></p>
						</div>
					</li>
				<? endforeach;?>
				</ul>
				<a class="pull-right viewAllBtn" href="<?=site_url()?>/latest-news/">View all news</a>
			</div>
		</div>
	</div>
</section>