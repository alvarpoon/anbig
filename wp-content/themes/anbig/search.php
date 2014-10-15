<?php
/*
Template Name: Search result Page
*/
?>

<?php while (have_posts()) : the_post(); ?>
  <?php get_template_part('templates/page', 'header'); ?>
  <?php get_template_part('templates/content', 'search-result'); ?>
<?php endwhile; ?>