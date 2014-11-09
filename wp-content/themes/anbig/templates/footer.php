<?
	$post_id = 4;
	$address = get_field("address",$post_id);
	$telephone = get_field("telephone",$post_id);
	$fax = get_field("fax",$post_id);
	$email = get_field("email",$post_id);
?>
<footer class="content-info footerPaddingTop" role="contentinfo">
  <div class="container">
    <div class="row">
    	<div class="col-sm-6 hidden-xs">
    		<?php
		          //footer menu
		          if (has_nav_menu('footer_menu')) :
		            wp_nav_menu(array('theme_location' => 'footer_menu', 'menu_class' => 'nav navbar-nav', 'depth' => 1));
		          endif;
		      ?>
    	</div>
    	<div class="col-sm-6 contactInfoDiv">
    		<h4>Contact Us</h4>
    		<p><?=$address?></p>
            <div class="directContactInfo">
            	<div class="col-sm-6">
                    <p class="logo_tel">Tel: <?=$telephone?></p>
                    <p class="logo_email">Email: <a href="mailto:<?=$email?>"><?=$email?></a></p>
                </div>
                <div class="col-sm-6">
                    <p class="logo_fax">Fax: <?=$fax?></p>
                </div>
    	</div>
    </div>
    <div class="row">
<!--     	<p class="pull-right viewCounter">
        	<strong>20 136</strong>
            <span>Total Views</span>
        </p> -->
        <div class="pull-right viewCounter">
        <p>Visitor numbers</p>
        <?
        the_widget('counter_free_widget');
        ?>
        </div>
    </div>
  </div>
</footer>

<?php wp_footer(); ?>
