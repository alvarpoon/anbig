<div class="container image-menu hidden-xs">
      <?
      /*$args = array(
          'type'                     => 'post',
          'orderby'                  => 'id',
          'order'                    => 'ASC',
          'hide_empty'               => 0,
          'hierarchical'             => 1,
          'taxonomy'                 => 'video_category',
          'parent'                   => 0
      );*/
      $args = array(
        'numberposts' => -1,
        'post_type' => 'page',
        'post_status' => 'publish',
        'order' => 'ASC',
        'orderby' => 'menu_order',
        'post_parent' => 11
        );
      $results = get_posts( $args );
      foreach( $results as $result ){
        $permalink = get_permalink($result->ID);
      ?>
        <a class="<? if ($result->ID==$post->ID) {?>active<? } ?>" href="<?=$permalink?>"><?=$result->post_title;?></a>
      <?
      }
      ?>
</div>
<div class="container">
  <div class="row">
<?
  //show category thumbnail if no category is selected
  if ($_GET['cat'] == ""){
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
 		<div class="col-sm-4">
 			<h2><?=$result->post_title?></h2>
 			<a href="<?=$permalink?>"><img class="img-responsive" src="<?=$image_url[0]?>" /></a>
 		</div>
 <?
  endforeach;
  }
  else{
    //get the image of the current category
    $args= array(
      'post_status' => 'publish',
      'posts_per_page' => -1,
      'orderby'  => 'date',
      'order'   => 'DESC',
      'post_type' => 'image',
      'tax_query' => array(
                      array(
                        'taxonomy' => 'image_category',
                        'field'    => 'id',
                        'terms'    => intval($_GET['cat']),
                      )
                    )
    );
      $results = get_posts( $args );
      foreach( $results as $result ){
      $image = get_field('original_image',$result->ID);
      $url = $image['sizes']['listing-image'];
    ?>
      <div class="col-sm-4">
        <img class="img-responsive" src="<?=$url?>" />
        <p><?=get_field("doctor",$result->ID)?></p>
        <p><?=cutoff_string($result->post_content,100)?></p>
      </div>
    <?
      }
    }
  ?>
</div>
</div>