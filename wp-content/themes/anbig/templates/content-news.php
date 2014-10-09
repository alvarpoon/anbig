<div class="container">
<?
  $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $args = array( 'numberposts' => -1, 'post_type' => 'activity', 'post_status' => 'publish', 'order' => 'DESC', 'orderby' => 'date');
    $results = get_posts( $args );
    $wp_query = new WP_Query($args);
  $i=0;
  while ( $wp_query->have_posts() ) :
    $wp_query->the_post();
    $id = get_the_ID();
    $post = get_post($id);
  ?>
    <div class="row activitiesItem">
      <div class="col-sm-8">
        <h2><?=$post->post_title?></h2>
        <dl class="dl-horizontal">
          <dt>Date:</dt>
          <dd><?=get_field("date",$id)?></dd>
        </dl>
        <dl class="dl-horizontal">
          <dt>Venue:</dt>
          <dd><?=get_field("venue",$id)?></dd>
        </dl>
        <dl class="dl-horizontal">
          <dt>Organizer:</dt>
          <dd><?=get_field("organizer",$id)?></dd>
        </dl>
      </div>
      <div class="col-sm-4">
      <?
        $poster = get_field("poster",$id);
        if(!empty($poster)){
          $url = $poster['sizes']['large'];
      ?>
          <a class="imgLink" href="<?=$url?>"><img class="fullwidthImg img-responsive" src="<?=$url?>" /></a>
      <?
        }
      ?>
      </div>
    </div>
  <? endwhile;
?>
</div>
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