<div class="container video-menu">
	<div class="hidden-xs">
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
</div>