<div class="container">
	<div class="row">
<?
  $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
  $args= array(
    'post_type' => 'video',
    'tax_query' => array(
                      array(
                        'taxonomy' => 'video_category',
                        'field'    => 'slug',
                        'terms'    => 'nbi-principles',
                      )
                    ),
    'post_status' => 'publish',
    'posts_per_page' => 1,
    'orderby'		=> 'date',
    'order' => 'DESC',
    'paged' => $paged
  );
  $wp_query = new WP_Query($args);
  while ( $wp_query->have_posts() ) :
    $wp_query->the_post();
    $id = get_the_ID();
    $post = get_post($id);
    $video_id = get_field('video');
    $video_url = wp_get_attachment_url( $video_id );
 ?>
 		<div class="col-sm-2"><p class="date"><?=get_the_date("M j",$id)?></p></div>
    <div class="col-sm-8"><p class="title"><a class="video" href="<?=$video_url?>"><?=$post->post_title?></a></p></div>
    <div class="col-sm-2"><p class="person"><?=get_field("doctor")?></p></div>
 <?
  endwhile;
 ?>
 	</div>
  <div class="row">
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
