<?php
/**
 * This will scan all the content pages that wordpress outputs for our special code. If the code is found, it will replace the requested quiz.
 */
function watupro_shortcode( $attr ) {
	global $wpdb, $post;
	$exam_id = $attr[0];

	$contents = '';
	if(!is_numeric($exam_id)) return $contents;
	
	watupro_vc_scripts();
	ob_start();
		
	// select exam
	$exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE id=%d", $exam_id));		
	if(watupro_intel()) WatuPROIntelligence :: conditional_scripts($exam_id);
	watupro_conditional_scripts($exam);	
	
	// passed question ids?	
	if(!empty($attr['question_ids'])) $passed_question_ids = $attr['question_ids'];
	
	// submitting without ajax?	
	if(!empty($_POST['no_ajax']) and !empty($exam->no_ajax)) {		
		require(WATUPRO_PATH."/show_exam.php");
		$contents = ob_get_clean();
		$contents = apply_filters('watupro_content', $contents);
		return $contents;
	}
	
	// other cases, show here
	if(empty($_GET['waturl']) or !$exam->shareable_final_screen) {
		// showing the exam
		if($exam->mode=='practice' and watupro_intel()) WatuPracticeController::show($exam);
		else include(WATUPRO_PATH . '/show_exam.php');
		$contents = ob_get_contents();
	}
	else {
		// showing taking results
		$url = @base64_decode($_GET['waturl']); 
		
		list($exam_id, $tid) = explode("|", $url); 
		if(!is_numeric($exam_id) or !is_numeric($tid)) return $contents;
		
		// must check if public URL is allowed 
		$taking = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_TAKEN_EXAMS." WHERE ID=%d", $tid));
		$contents = WatuPRO::cleanup($taking->details, 'web');
		
		$post->ID = 0;
		$post->comment_status = 'closed';
	}
	
	ob_end_clean();			
	$contents = apply_filters('watupro_content', $contents);
	
	return $contents;
}

// shortcodes to list exams 
function watupro_listcode($attr) {
	$cat_id = @$attr[0];
	if(empty($cat_id)) $cat_id = @$attr['cat_id'];
	
	// define orderby
	$ob = @$attr['orderby'];
	if(empty($ob)) $ob = @$attr[1];
		
	switch($ob) {		
		case 'title': $orderby = "tE.name"; break;
		case 'latest': $orderby = "tE.ID DESC"; break;
		case 'created': default: $orderby = "tE.ID"; break;
	}
	
	watupro_vc_scripts();
	
	$show_status = empty($attr['show_status']) ? false : true;
	$content = WTPExam::show_list($cat_id, $orderby, $show_status);
	
	return $content;	
}

// outputs my exams page in any post or page
function watupro_myexams_code($attr) {
	$cat_id = @$attr[0];
	
	$content = '';
	if(!is_user_logged_in()) return __('This content is only for logged in users', 'watupro');
	watupro_vc_scripts();
	
	// define orderby
	$ob = @$attr[1];	
	switch($ob) {		
		case 'title': $orderby = "tE.name"; break;
		case 'latest': $orderby = "tE.ID DESC"; break;
		case 'created': default: $orderby = "tE.ID"; break;
	}
	
	ob_start();
	watupro_my_exams($cat_id, $orderby);
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}

// outputs my certificates in any post or page
function watupro_mycertificates_code($attr) {
	$content = '';
	if(!is_user_logged_in()) return __('This content is only for logged in users', 'watupro');
	watupro_vc_scripts();
	
	ob_start();
	watupro_my_certificates();
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}

// outputs generic leaderboard from all tests
function watupro_leaderboard($attr) {
	global $wpdb;
	watupro_vc_scripts();
	
	$num = $attr[0]; // number of users to show
	if(empty($num) or !is_numeric($num)) $num = 10;
	
	// now select them ordered by total points
	$users = $wpdb -> get_results("SELECT SUM(tT.points) as points, tU.user_login as user_login 
		FROM {$wpdb->users} tU JOIN ".WATUPRO_TAKEN_EXAMS." tT ON tT.user_id = tU.ID
		WHERE tT.in_progress = 0 GROUP BY tU.ID ORDER BY points DESC LIMIT $num");
	
	$table = "<table class='watupro-leaderboard'><tr><th>".__('User', 'watupro')."</th><th>".__("Points", 'watupro')."</th></tr>";
	
	foreach($users as $user) {
		$table .= "<tr><td>".$user->user_login."</td><td>".$user->points."</td></tr>";
	}
	
	$table .= "</table>";
	
	return $table;
}

// displays data from user profile of the currently logged user
function watupro_userinfo($atts) {
	global $user_ID;
	if(!is_user_logged_in()) return @$atts[1];
	
	$field = $atts[0];
		
	$user = get_userdata($user_ID);
	
	if(isset($user->data->$field) and !empty($user->data->$field)) return $user->data->$field;
	if(isset($user->data->$field) and empty($user->data->$field)) return @$atts[1];
	
	// not set? must be in meta then
	$metas = get_user_meta($user_ID);
	foreach($metas as $key => $meta) {
		if($key == $field and !empty($meta[0])) return $meta[0];
		if($key == $field and empty($meta[0])) return @$atts[1];
	}
	
	// nothing found, return the default if any
	return @$atts[1];
}