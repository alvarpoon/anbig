<header class="banner navbar navbar-default navbar-fixed-top" role="banner">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a href="javascript:;" class="menu-label hidden-xs hidden-sm hidden-md hidden-lg" data-toggle="collapse" data-target=".navbar-collapse">menu</a>
      <a class="navbar-brand" href="<?php echo home_url(); ?>/"><img src="<?=get_stylesheet_directory_uri()?>/assets/img/logo-top.png"></a>
    </div>
  </div>
  <div class="nav-container">
  <nav class="collapse navbar-collapse main-menu" role="navigation">
      <?php
          //Main menu
          if (has_nav_menu('primary_navigation')) :
            wp_nav_menu(array('theme_location' => 'primary_navigation', 'menu_class' => 'nav navbar-nav', 'depth' => 0));
          endif;

      ?>
    </nav>
    <div class="container">
      <nav class="collapse navbar-collapse sector-menu" role="navigation">
        <?php
            //Utility menu
            /*if (has_nav_menu('utility_menu')) :
              wp_nav_menu(array('theme_location' => 'utility_menu', 'menu_class' => 'nav navbar-right navbar-nav','depth' => 1));
            endif;*/
        ?>
          <ul id="menu-utility-menu" class="nav navbar-right navbar-nav">
            <li class="active menu-home"><a href="<?php echo home_url(); ?>">Home</a></li>
            <li class="menu-links"><a href="<?php echo home_url(); ?>/links/">Links</a></li>
        <?
          if ( is_user_logged_in()) {
            echo '<li class="menu-logout"><a href="'.wp_logout_url().'">Logout</a></li>';
          }
          else {
            echo '<li class="menu-login"><a href="'.home_url().'/login/">Login</a></li>';
          }
        ?>
          </ul>
      </nav>
    </div>
  </div>
</header>
