<div class="container filter-container hidden-xs">
  <div class="top-filter">
    <a class="cat <? if ($_GET['cat'] == "") {?>active<? } ?>" href="/image-archives/">All</a>
      <?
      $args = array(
          'type'                     => 'post',
          'orderby'                  => 'id',
          'order'                    => 'ASC',
          'hide_empty'               => 0,
          'hierarchical'             => 1,
          'taxonomy'                 => 'image_category',
          'parent'                   => 0
      );
      $categories = get_categories( $args );
      foreach ($categories as $category) :
      ?>
        <a class="cat <? if ($_GET['cat'] == $category->term_id) {?>active<? } ?>" href="/image-archives?cat=<?=$category->term_id?>"><?=$category->name;?></a>
      <? endforeach; ?>
    </div>
    <?
    //show sub filter if a category is selected
    if ($_GET['cat'] != ""){
      $args = array(
          'type'                     => 'post',
          'orderby'                  => 'id',
          'order'                    => 'ASC',
          'hide_empty'               => 0,
          'hierarchical'             => 1,
          'taxonomy'                 => 'image_category',
          'parent'                   => intval($_GET['cat'])
      );
      $categories = get_categories( $args );
      if(sizeof($categories)>0){
      ?>
        <div class="sub-filter">
      <?
      foreach ($categories as $category) :
      ?>
        <a class="cat <? if ($_GET['cat'] == $category->term_id) {?>active<? } ?>" href="/image-archives?cat=<?=$category->term_id?>"><?=$category->name;?></a>
      <?
      endforeach;
      ?>
    </div>
      <?
    }
    ?>
    </div>
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