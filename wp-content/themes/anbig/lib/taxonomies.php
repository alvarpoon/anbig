<?php

add_action( 'init', 'create_video_taxonomies', 0 );
function create_video_taxonomies() {
  register_taxonomy(
      'video_category',
      'video',
      array(
          'labels' => array(
              'name' => 'Category',
              'add_new_item' => 'Add Category',
              'new_item_name' => 'New Category'
          ),
          'show_ui' => true,
          'show_tagcloud' => false,
          'hierarchical' => true
      )
  );
}

add_action( 'init', 'create_image_taxonomies', 0 );
function create_image_taxonomies() {
  register_taxonomy(
      'image_category',
      'image',
      array(
          'labels' => array(
              'name' => 'Category',
              'add_new_item' => 'Add Category',
              'new_item_name' => 'New Category'
          ),
          'show_ui' => true,
          'show_tagcloud' => false,
          'hierarchical' => true
      )
  );
}


// in case the templates pop out
// global $wp_taxonomies;
// $taxonomy = 'year';
// unset( $wp_taxonomies[$taxonomy]);
// flush_rewrite_rules();
