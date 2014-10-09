<?=get_template_part('partials/image_menu'); ?>
<?=get_template_part('partials/image_submenu'); ?>
<div class="container">
  <!-- <div class="row"> -->
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
  $i=0;
  while ( $wp_query->have_posts() ) :
    $wp_query->the_post();
    $id = get_the_ID();
    $post = get_post($id);
    $image = get_field('original_image',$id);
    //$url = $image['sizes']['listing-image'];
	$url = $image['sizes']['large'];
	$nbiImage = get_field("nbi_image",$id);
	$nbiURL = $nbiImage['sizes']['large'];
  if($i%3==0){
        ?>
        <div class="row">
        <?
      }
 ?>
    <div class="col-sm-4 listingImageItem clearfix">
    	<a class="imgArchievesLink" href="<?=$url?>" person="<?=get_field("doctor",$result->ID)?>" desp="<?=the_content()?>" nbi_image="<?=$nbiURL?>"><img class="fullwidthImg img-responsive" src="<?=$url?>" /></a>
   		<p class="doctor"><?=get_field("doctor",$id)?></p>
        <p class="desp"><? $content = get_the_content(); $content = strip_tags($content); echo substr($content, 0, 90).'...'; ?></p>
        <a class="btnReadMore imgArchievesLink" href="<?=$url?>" person="<?=get_field("doctor",$result->ID)?>" desp="<?=the_content()?>" nbi_image="<?=$nbiURL?>">Read more</a>
    </div>
 <?
 if($i%3==2){
        ?>
        </div>
        <?
        }
        $i++;
  endwhile;
 ?>
  <!-- </div> -->
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
