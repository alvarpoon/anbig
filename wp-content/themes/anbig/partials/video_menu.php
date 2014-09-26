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
	  <span class="hidden-xs"></span>
	</div>
	<div class="hidden-sm hidden-md hidden-lg">
		<? foreach( $results as $result ){
				$permalink = get_permalink($result->ID); 
				if ($result->ID==$post->ID) {
		?>
				<div class="mobileMediaNav">
					<div class="mobileMediaToggle clearfix">
						<span class="currentPageName"><?=$result->post_title;?></span>
						<span class="dropdownArrow"></span>
					</div>
					<div class="mobileMediaList">
			<?		
					}
				}
				foreach( $results as $result ){
					$permalink = get_permalink($result->ID); 
				?>
					
						<a href="<?=$permalink?>"><?=$result->post_title;?></a>
					
				<?	
				}
				
			?>
			</div>
		</div>
	</div>
</div>