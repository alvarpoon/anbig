<?=get_template_part('partials/image_menu'); ?>
<?=get_template_part('partials/image_submenu'); ?>
<div class="container">
  <div class="row">
<?
  global $current_cat_id;
  //echo $current_cat_id;
  $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
  $args= array(
    'post_type' => 'image',
    'tax_query' => array(
                      array(
                        'taxonomy' => 'image_category',
                        'field'    => 'id',
                        'terms'    => $current_cat_id,
                      )
                    ),
    'post_status' => 'publish',
    'posts_per_page' => 6,
    'orderby'   => 'date',
    'order' => 'DESC',
    'paged' => $paged
  );
  $wp_query = new WP_Query($args);
  while ( $wp_query->have_posts() ) :
    $wp_query->the_post();
    $id = get_the_ID();
    $post = get_post($id);
    $image = get_field('original_image',$id);
    $url = $image['sizes']['listing-image'];
 ?>
    <div class="col-sm-4">
      <img class="img-responsive" src="<?=$url?>" />
        <p><?=get_field("doctor",$id)?></p>
        <p><?=cutoff_string(the_content(),100)?></p>
        <a class="fancybox" href="<?=$url?>">Read more</a>
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
