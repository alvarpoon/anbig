<div class="container">
  <?
  $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $args = array( 'numberposts' => -1, 'post_type' => 'post', 'post_status' => 'publish', 'order' => 'DESC', 'orderby' => 'date');
    $results = get_posts( $args );
    $wp_query = new WP_Query($args);
  $i=0;
  while ( $wp_query->have_posts() ) :
    $wp_query->the_post();
    $id = get_the_ID();
    $post = get_post($id);
  ?>
  <div class="newsItem clearfix">
    <div class="date"><?=$post->date?></div>
    <div class="col-sm-10 noPadding">
      <p class="newsTitle"><?=$post->post_title?><span class="speaker"> by <?=$post->speaker?></span></p>
      <div class="newsContent"><? $content = get_the_content(); echo $content; ?></div>
      <p><? if($post->show_invitation) echo "*If you are interested, please <a href='mailto:xxx@gmail.com'>send us an email</a> for further details." ?></p>
    </div>
    <div class="col-sm-2 noPadding alignRight"> <a target="_blank" href="<?=get_field('pdf',$id)?>" class="btnReadMore">Read More</a> </div>
  </div>
  <? endwhile;
?>
  <div class="row textAlignCenter">
    <div class="paginationContainer">
      <?
        $big = 999999999; // need an unlikely integer
      
        echo paginate_links( array(
        'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
        'format' => '?paged=%#%',
        'current' => max( 1, get_query_var('paged') ),
        'total' => $wp_query->max_num_pages
      ) );
      ?>
    </div>
  </div>
</div>
