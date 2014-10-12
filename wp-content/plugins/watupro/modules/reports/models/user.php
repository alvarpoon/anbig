<?php
// functions that manage the users.php page in admin and maybe more
class WTPReportsUser {
	static function add_status_column($columns) {	
		$columns['exam_reports'] = sprintf(__('%s Reports', 'watupro'), __('Quiz', 'watupro'));
	 	return $columns;	
	}
}