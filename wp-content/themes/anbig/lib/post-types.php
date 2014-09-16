<?php

// Activities
add_action('init', 'activity_register');
function activity_register() {
  $labels = array(
      'name' => _x('Activity', 'post type general name'),
      'singular_name' => _x('Activity', 'post type singular name'),
      'add_new' => _x('Add New', 'rep'),
      'add_new_item' => __('Add New Activity'),
      'edit_item' => __('Edit Activity'),
      'new_item' => __('New Activity'),
      'view_item' => __('View Activity'),
      'search_items' => __('Search Activity'),
      'not_found' =>  __('Nothing found'),
      'not_found_in_trash' => __('Nothing found in Trash'),
      'parent_item_colon' => ''
  );
  $args = array(
      'labels' => $labels,
      'public' => true,
      'publicly_queryable' => true,
      'show_ui' => true,
      'query_var' => true,
      'rewrite' => true,
      'capability_type' => 'post',
      'hierarchical' => false,
      'menu_position' => 6,
      'supports' => array('title')
  );
  register_post_type( 'activity' , $args );
}

// mainpage banner
add_action('init', 'mainpage_banner_register');
function mainpage_banner_register() {
  $labels = array(
      'name' => _x('Mainpage banner', 'post type general name'),
      'singular_name' => _x('Mainpage banner', 'post type singular name'),
      'add_new' => _x('Add Mainpage banner', 'rep'),
      'add_new_item' => __('Add New Mainpage banner'),
      'edit_item' => __('Edit Mainpage banner'),
      'new_item' => __('New Mainpage banner'),
      'view_item' => __('View Mainpage banner'),
      'search_items' => __('Search Mainpage banner'),
      'not_found' =>  __('Nothing found'),
      'not_found_in_trash' => __('Nothing found in Trash'),
      'parent_item_colon' => ''
  );
  $args = array(
      'labels' => $labels,
      'public' => true,
      'publicly_queryable' => true,
      'show_ui' => true,
      'query_var' => true,
      'rewrite' => true,
      'capability_type' => 'post',
      'hierarchical' => true,
      'menu_position' => 6,
      'supports'      => array( 'title', 'thumbnail'),
  );
  register_post_type( 'mainpage_banner' , $args );
}

// Video
add_action('init', 'video_register');
function video_register() {
  $labels = array(
      'name' => _x('Video', 'post type general name'),
      'singular_name' => _x('Video', 'post type singular name'),
      'add_new' => _x('Add Video', 'rep'),
      'add_new_item' => __('Add New Video'),
      'edit_item' => __('Edit Video'),
      'new_item' => __('New Video'),
      'view_item' => __('View Video'),
      'search_items' => __('Search Video'),
      'not_found' =>  __('Nothing found'),
      'not_found_in_trash' => __('Nothing found in Trash'),
      'parent_item_colon' => ''
  );
  $args = array(
      'labels' => $labels,
      'public' => true,
      'publicly_queryable' => true,
      'show_ui' => true,
      'query_var' => true,
      'rewrite' => true,
      'capability_type' => 'post',
      'hierarchical' => true,
      'menu_position' => 6,
      'supports'      => array( 'title', 'thumbnail', 'editor'),
  );
  register_post_type( 'video' , $args );
}

// Image
add_action('init', 'image_register');
function image_register() {
  $labels = array(
      'name' => _x('Image', 'post type general name'),
      'singular_name' => _x('Image', 'post type singular name'),
      'add_new' => _x('Add Image', 'rep'),
      'add_new_item' => __('Add New Image'),
      'edit_item' => __('Edit Image'),
      'new_item' => __('New Image'),
      'view_item' => __('View Image'),
      'search_items' => __('Search Image'),
      'not_found' =>  __('Nothing found'),
      'not_found_in_trash' => __('Nothing found in Trash'),
      'parent_item_colon' => ''
  );
  $args = array(
      'labels' => $labels,
      'public' => true,
      'publicly_queryable' => true,
      'show_ui' => true,
      'query_var' => true,
      'rewrite' => true,
      'capability_type' => 'post',
      'hierarchical' => true,
      'menu_position' => 6,
      'supports'      => array( 'title', 'thumbnail', 'editor'),
  );
  register_post_type( 'image' , $args );
}

?>
