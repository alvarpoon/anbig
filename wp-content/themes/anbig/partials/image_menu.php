<div class="container image-menu">
	<div class="hidden-xs">
      <?
      $args = array(
        'numberposts' => -1,
        'post_type' => 'page',
        'post_status' => 'publish',
        'order' => 'ASC',
        'orderby' => 'menu_order',
        'post_parent' => 25
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
						<span class="dropdownArrow"><b class="caret"></b></span>
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