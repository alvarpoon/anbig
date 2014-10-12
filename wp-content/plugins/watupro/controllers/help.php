<?php
// user manual - added in version 3.1, to be developed
function watupro_help() {	
	if(@file_exists(get_stylesheet_directory().'/watupro/help.php')) require get_stylesheet_directory().'/watupro/help.php';
	else require WATUPRO_PATH."/views/help.php";
}