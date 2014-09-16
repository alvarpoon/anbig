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