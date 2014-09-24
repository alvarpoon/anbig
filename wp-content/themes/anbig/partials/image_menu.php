<div class="container image-menu hidden-xs">
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
      ?>
        <a class="<? if ($result->ID==$post->ID) {?>active<? } ?>" href="<?=$permalink?>"><?=$result->post_title;?></a>
      <?
      }
      ?>
</div>