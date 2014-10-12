<?php
// Initial setup for ajax.
if(isset($_REQUEST['action']) and $_REQUEST['action']=='watupro_submit' ) $exam_id = $_REQUEST['quiz_id'];

$_question = new WTPQuestion();
$_exam = new WTPExam();
global $wpdb, $post, $user_ID;

// select exam
$exam=$wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE id=%d", $exam_id));
if(empty($exam->is_active)) {
	printf(__('This %s is currently inactive.', 'watupro'), __('quiz', 'watupro'));
	return true;
}

// passed question IDs in the shortcode?
if(!empty($passed_question_ids)) $exam->passed_question_ids = $passed_question_ids;

$_question->exam = $exam; 
do_action('watupro_select_show_exam', $exam); // API Call
$advanced_settings = unserialize( stripslashes($exam->advanced_settings));
if(watupro_intel()) {
	WatuPROIQuestion :: $advanced_settings = $advanced_settings;
	WTPQuestion :: $advanced_settings = $advanced_settings;
}

// in progress taking of this exam?
$in_progress = null;
$exam->full_time_limit = $exam->time_limit; // store this for the verify_timer calculations
if(is_user_logged_in()) {
	$in_progress=$wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_TAKEN_EXAMS." 
		WHERE user_id=%d AND exam_id=%d AND in_progress=1 ORDER BY ID DESC LIMIT 1", $user_ID, $exam_id));
		
	if($exam->time_limit) $meta_start_time = get_user_meta($user_ID, "start_exam_".$exam->ID, true);
		
	if($exam->time_limit > 0 and (!empty($in_progress->ID) or !empty($meta_start_time))) {		
		// recalculate time limit
		$start_time = !empty($in_progress->ID) ? watupro_mktime($in_progress->start_time) : $meta_start_time;
		$limit_in_seconds = intval($exam->time_limit*60);
		$time_elapsed = current_time('timestamp') - $start_time;	
		$new_limit_seconds = $limit_in_seconds - $time_elapsed;
		// echo $new_limit_seconds;
		if($new_limit_seconds < 0) {		
			unset($in_progress); // unset this so we will submit empty the results 	
			$exam->time_limit = 0.003;
			$timer_warning = __("Warning: your unfinished attempt was recorded. You ran out of time and your answers will be submitted automatically.", 'watupro');	
		}		 	
		else {	
			// echo $new_limit_seconds;		
			$exam->time_limit = round($new_limit_seconds/60, 3);
			// never zero
			if(empty($exam->time_limit)) $exam->time_limit = 0.003;
			$timer_warning = __("Warning: you have started this test earlier and the timer is running behind the scene!", 'watupro');
		}
	}	
}
if(!empty($advanced_settings['dont_load_inprogress'])) $in_progress = null;

if(!WTPUser::check_access($exam, $post)) return false;

// is scheduled?
if($exam->is_scheduled==1) {	 
    $now = current_time('timestamp');
    $schedule_from = strtotime($exam->schedule_from);
    $schedule_to = strtotime($exam->schedule_to);
    if ($now < $schedule_from or $now > $schedule_to) {
        printf(__('This test will be available between %s and %s.', 'watupro'), date(get_option('date_format').' '.get_option('time_format'), $schedule_from), date(get_option('date_format').' '.get_option('time_format'), $schedule_to));
        if(current_user_can(WATUPRO_MANAGE_CAPS)) echo ' '.__('You can still see it only because you are administrator or manager.', 'watupro').' ';
        else return false; // students can't take this test
    }
}

// logged in or login not required here		
$_watu=new WatuPRO();    
  
// re-taking allowed?       
$ok=$_watu->can_retake($exam);
 
// check time limits on submit
if($ok and $exam->time_limit > 0 and !empty($_REQUEST['action'])) {	
	$ok=$_watu->verify_time_limit($exam, @$in_progress);
	if(!$ok) { 
		echo "<p><b>".__("Time limit exceeded! We cannot accept your results.", 'watupro')."</b></p>";
		if(!empty($in_progress->ID)) {
			$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_TAKEN_EXAMS." 
			SET in_progress=0 WHERE id=%d", $in_progress->ID));
			echo $wpdb->prepare("UPDATE ".WATUPRO_TAKEN_EXAMS." SET in_progress=0 WHERE id=%d", $in_progress->ID);
		}
	}
	
	// $ok, so clear the time limit for the future takings
	update_user_meta( $user_ID, "start_exam_".$exam->ID, 0);
}
            
if(!$ok) return false;

if(!is_singular() and !empty($GLOBALS['watupro_client_includes_loaded'])) { #If this is in the listing page - and a quiz is already shown, don't show another.
	printf(__("Please go to <a href='%s'>%s</a> to view this test", 'watupro'), get_permalink(), get_the_title());
	return false;
} 
            
// now select and display questions			
$answer_display = $exam->show_answers==""?get_option('watupro_show_answers'):$exam->show_answers;	

// loading serialized questions or questions coming by POST
if(!empty($_POST['action']) or !empty($in_progress->serialized_questions)) {	
	$serialized_questions = empty($_REQUEST['watupro_questions']) ? @$in_progress->serialized_questions : $_REQUEST['watupro_questions'];
	$all_question=watupro_unserialize_questions($serialized_questions);
}

// this happens either at the beginning or if for some reason $all_question is empty on submitting			
if(empty($all_question)) {	
	$all_question = WTPQuestion::select_all($exam);
		
	// regroup by cats?	
	if(empty($passed_question_ids)) $all_question = $_watu->group_by_cat($all_question, $exam);	
 		
	// now match answers to non-textarea questions
	$_watu->match_answers($all_question, $exam);				
}    					
$cnt_questions	= sizeof($all_question);	

// get required question ids as string
$rids=array(0);
foreach($all_question as $q)  {
	if($q->is_required) $rids[]=$q->ID;
}
$required_ids_str=implode(",",$rids);

// requires captcha?
if($exam->require_captcha) {
	$recaptcha_public = get_option("watupro_recaptcha_public");
	$recaptcha_private = get_option("watupro_recaptcha_private");
	if(!function_exists('recaptcha_get_html')) {
		 require(WATUPRO_PATH."/lib/recaptcha/recaptchalib.php");					 
	}
	$recaptcha_style = $exam->single_page==1?"":"style='display:none;'";
	$recaptcha_html = "<div id='WTPReCaptcha' $recaptcha_style><p>".recaptcha_get_html($recaptcha_public)."</p></div>";
	
	// check captcha
	if(!empty($_POST['action'])) {
		$resp = recaptcha_check_answer ($recaptcha_private,
                          $_SERVER["REMOTE_ADDR"],
                          $_POST["recaptcha_challenge_field"],
                          $_POST["recaptcha_response_field"]);
      if (!$resp->is_valid) die('WATUPRO_CAPTCHA:::'.__('Invalid image validation code', 'watupro'));			
	}
} // end recaptcha code
			
if($all_question) {
	$GLOBALS['watupro_client_includes_loaded'] = true;
			
	if(empty($_REQUEST['action'])) {	
		if(@file_exists(get_stylesheet_directory().'/watupro/show_exam.php')) $show_exam_view = get_stylesheet_directory().'/watupro/show_exam.php';
		else $show_exam_view = WATUPRO_PATH."/views/show_exam.php";		
		
		$show_exam_view = apply_filters( 'watupro_filter_view_show_exam', $show_exam_view, $exam);  
		require($show_exam_view);
	}
	else require(WATUPRO_PATH.'/controllers/submit_exam.php'); 
}  // end if $all_question 