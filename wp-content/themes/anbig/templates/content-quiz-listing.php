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
 ?>
 			<p class="title"><a href="<?=$permalink?>"><?=$result->post_title?></a></p>
 <?
  endforeach;
 ?>
 	</div>
</div>