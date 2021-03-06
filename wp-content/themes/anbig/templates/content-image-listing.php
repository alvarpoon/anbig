<div class="container">
  <div class="row">
      <?
      $args = array(
        'numberposts' => -1,
        'post_type' => 'page',
        'post_status' => 'publish',
        'order' => 'ASC',
        'orderby' => 'menu_order',
        'post_parent' => 25
        );
      $results = get_posts( $args );
      foreach( $results as $result ){
        $permalink = get_permalink($result->ID);
        $image_url = wp_get_attachment_image_src( get_post_thumbnail_id( $result->ID ), 'listing-image' );
      ?>
        <div class="col-sm-4">
          <h2><a href="<?=$permalink?>"><?=$result->post_title?></a></h2>
          <a href="<?=$permalink?>"><img class="img-responsive" src="<?=$image_url[0]?>" /></a>
        </div>
      <?
      }
      ?>
    </div>
</div>