<div class="container video-submenu">
<?
  $catid = get_term_by( 'slug', $post->post_name, 'video_category' );
  $catid = $catid->term_id;
  //echo $post->post_title;
  //echo $post->post_title;
    $args = array(
        'type'                     => 'post',
        'orderby'                  => 'id',
        'order'                    => 'ASC',
        'hide_empty'               => 0,
        'hierarchical'             => 0,
        'taxonomy'                 => 'video_category',
        'parent'                   => $catid
    );
    $categories = get_categories( $args );
    
    global $current_cat_id;
    if(sizeof($categories)==0){
      $current_cat_id = $catid;
    }
    else{
      $i=0;
      foreach ($categories as $category){
        if($_GET['cat']=="" && $i==0){
          $class="active";
          $current_cat_id = $category->term_id;
        }
        else if($_GET['cat']!="" && intval($_GET['cat']) == $category->term_id){
          $class="active";
          $current_cat_id = $category->term_id;
        }
        else{
          $class="";
        }
        ?>
          <a class="<?=$class?>" href="<?=get_permalink($post->ID)?>?cat=<?=$category->term_id?>"><?=$category->name;?></a>
        <?
        $i++;
        }
      }
      ?>
</div>