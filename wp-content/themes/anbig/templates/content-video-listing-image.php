<?=get_template_part('partials/video_menu'); ?>
<?=get_template_part('partials/video_submenu'); ?>
<div class="container">
	<div class="row">
<?
  global $current_cat_id;
  //echo $current_cat_id;
  $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
  $args= array(
    'post_type' => 'video',
    'tax_query' => array(
                      array(
                        'taxonomy' => 'video_category',
                        'field'    => 'id',
                        'terms'    => $current_cat_id,
                      )
                    ),
    'post_status' => 'publish',
    'posts_per_page' => 6,
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
    $image_url = wp_get_attachment_image_src( get_post_thumbnail_id($id), 'listing-image' );
 ?>
 		<div class="col-sm-4">
      <a class="fancybox" href="<?=$video_url?>"><img class="img-responsive" src="<?=$image_url[0]?>" /></a>
      <p class="doctor"><?=get_field("doctor",$result->ID)?></p>
      <p class="desp"><?=cutoff_string(the_content(),100)?></p>
      <a class="fancybox" href="<?=$video_url?>">Read more</a>
    </div>
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
