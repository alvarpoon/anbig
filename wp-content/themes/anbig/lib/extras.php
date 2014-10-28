<?php
/**
 * Clean up the_excerpt()
 */
function roots_excerpt_more($more) {
  return ' &hellip; <a href="' . get_permalink() . '">' . __('Continued', 'roots') . '</a>';
}
add_filter('excerpt_more', 'roots_excerpt_more');

/**
 * Manage output of wp_title()
 */
function roots_wp_title($title) {
  if (is_feed()) {
    return $title;
  }

  $title .= get_bloginfo('name');

  return $title;
}
add_filter('wp_title', 'roots_wp_title', 10);

function cutoff_string($string, $length) {
   $length++;
   if(strlen($string)>$length) {
       $subex = substr($string,0,$length-5);
       $exwords = explode(" ",$subex);
       $excut = -(strlen($exwords[count($exwords)-1]));
       if($excut<0) {
            $return_string = substr($subex,0,$excut);
       } else {
       	    $return_string = $subex;
       }
       $return_string .= "...";
   } else {
	   $return_string = $string;
   }
   return $return_string;
}

//hide toolbar after login, ref: http://www.wpbeginner.com/wp-tutorials/how-to-disable-wordpress-admin-bar-for-all-users-except-administrators/
add_action('after_setup_theme', 'remove_admin_bar');

function remove_admin_bar() {
if (!current_user_can('administrator') && !is_admin()) {
  show_admin_bar(false);
}
}

function my_custom_display_topic_index_query () {
	
  //$args['orderby'] = 'date';
  //$args['order']   = 'ASC';
  if($_GET["orderby"] == null || $_GET["order"] == null){
	$args['orderby'] = 'date';
	$args['order']   = 'DESC';
  }else{
	$args['orderby'] = $_GET["orderby"];
  	$args['order']   = $_GET["order"];
  }

  return $args;
}
add_filter('bbp_before_has_topics_parse_args', 'my_custom_display_topic_index_query' );

function setOrderSign($string){
	if($_GET["orderby"] == null || $_GET["order"] == null){
		$args['orderby'] = 'date';
		$args['order']   = 'DESC';
	  }else{
		$args['orderby'] = $_GET["orderby"];
		$args['order']   = $_GET["order"];
	  }
	  
	if($args['orderby'] == $string){
		if($args['order'] == 'DESC'){
			echo 'descOrder';	
		}else if($args['order'] == 'ASC'){
			echo 'ascOrder';
		}else{
			return;	
		}
	}else{
		return;	
	}
}

/** changing default wordpres email settings */
add_filter('wp_mail_from_name', 'new_mail_from_name');
 
function new_mail_from_name($old) {
 return 'ANBIG';
}