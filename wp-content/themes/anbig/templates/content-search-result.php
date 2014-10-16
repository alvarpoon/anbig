  <?php //the_content(); ?>
  <div class="container" style="margin-top:15px;">
  	<? if($post->post_type == "video"){
		  $i=0;
		  while ( $wp_query->have_posts() ) :
			$wp_query->the_post();
			$id = get_the_ID();
			$post = get_post($id);
			$video_id = get_field('video');
			$video_url = wp_get_attachment_url( $video_id );
			$image_url = wp_get_attachment_image_src( get_post_thumbnail_id($id), 'listing-image' );
			if($i%3==0){?>
				<div class="row">
			<? } ?>
			<div class="col-sm-4 listingImageItem clearfix">
				<div class="videoThumbContainer">
				  <a class="videoLink" href="<?=$video_url?>" person="<?=get_field("doctor",$result->ID)?>" desp="<?=the_content()?>"><img class="img-responsive fullwidthImg" src="<?=$image_url[0]?>" /></a>
				  <a class="videoLink btnVideoPlay" href="<?=$video_url?>" person="<?=get_field("doctor",$result->ID)?>" desp="<?=the_content()?>"></a>
				</div>
				<p class="doctor"><?=get_field("doctor",$result->ID)?></p>
				<p class="desp"><? $content = get_the_content(); $content = strip_tags($content); echo substr($content, 0, 90).'...'; ?></p>
				<a class="btnReadMore videoLink" href="<?=$video_url?>" person="<?=get_field("doctor",$result->ID)?>" desp="<?=the_content()?>">Read more</a>
			</div>
		 <? if($i%3==2){ ?>
			</div>
		 <? }	
		  $i++; 
		  endwhile;
		 ?>
         
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
    <? } ?>
    
    <? if($post->post_type == "image"){
		  $i=0;
		  while ( $wp_query->have_posts() ) :
			$wp_query->the_post();
			$id = get_the_ID();
			$post = get_post($id);
			$image = get_field('original_image',$id);
			$url = $image['sizes']['large'];
			$nbiImage = get_field("nbi_image",$id);
			$nbiURL = $nbiImage['sizes']['large'];
		  if($i%3==0){?>
	        <div class="row">
    	    <? } ?>
                <div class="col-sm-4 listingImageItem clearfix">
                    <a class="imgArchievesLink" href="<?=$url?>" person="<?=get_field("doctor",$result->ID)?>" desp="<?=the_content()?>" nbi_image="<?=$nbiURL?>"><img class="fullwidthImg img-responsive" src="<?=$url?>" /></a>
                    <p class="doctor"><?=get_field("doctor",$id)?></p>
                    <p class="desp"><? $content = get_the_content(); $content = strip_tags($content); echo substr($content, 0, 90).'...'; ?></p>
                    <a class="btnReadMore imgArchievesLink" href="<?=$url?>" person="<?=get_field("doctor",$result->ID)?>" desp="<?=the_content()?>" nbi_image="<?=$nbiURL?>">Read more</a>
                </div>
             <? if($i%3==2){ ?>
	        </div>
    	    <? }
			$i++;
			endwhile; ?>
            
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
  <? } ?>
  </div>

  <?php wp_link_pages(array('before' => '<nav class="pagination">', 'after' => '</nav>')); ?>