<?=get_template_part('partials/video_menu');?>
<div class="container">
	<div class="mediaSearchDiv">
        <form role="search" action="<?php echo site_url('/'); ?>" method="get" id="searchform">
          <input type="text" name="s" placeholder="Search..."/>
          <input type="hidden" name="post_type" value="video" />
	      <input type="hidden" name="posts_per_page" value="6" />
          <input type="submit" alt="Search" value="Search" />
        </form>
    </div>
 
	<div class="row video-text-listing">
<?
  $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
  $args= array(
    'post_type' => 'video',
    'tax_query' => array(
                      array(
                        'taxonomy' => 'video_category',
                        'field'    => 'slug',
                        'terms'    => 'lectures',
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
 ?><div class="container row">
 		<div class="col-sm-3"><p class="date"><?=get_field("date")?></p></div>
	    <div class="col-sm-6">
			<a class="title btnVideoDetail" href="<?=$video_url?>" person="<?=get_field("doctor")?>" desp="<?=$post->post_title?>"><?=$post->post_title?></a><a href="<?=$video_url?>" class="btnVideoDetail btn-video"></a>
		</div>
    	<div class="col-sm-3">
        	<p class="person"><?=get_field("doctor")?></p>
        </div>
	</div>
 <?
  endwhile;
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
</div>
