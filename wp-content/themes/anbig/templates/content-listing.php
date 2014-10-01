<div class="container">
	<div class="row">
<?
  $args= array(
    'post_type' => 'page',
    'post_parent' => $post->ID,
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'orderby'		=> 'menu_order',
    'order' => 'ASC'

  );
  $results = get_posts( $args );
  foreach( $results as $result ) :
  	$permalink = get_permalink( $result->ID );
  	$image_url = wp_get_attachment_image_src( get_post_thumbnail_id( $result->ID ), 'listing-image' );
 ?>
 		<div class="col-sm-4 video-col-sm-padding-bottom">
 			<h2 class="videoTitle"><a href="<?=$permalink?>"><?=$result->post_title?></a></h2>
 			<a href="<?=$permalink?>"><img class="img-responsive fullwidthImg" src="<?=$image_url[0]?>" /></a>
 		</div>
 <?
  endforeach;
 ?>
 	</div>
</div>