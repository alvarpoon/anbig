<?php
// called when exam is submitted
$_question = new WTPQuestion();
global $user_email, $user_identity, $question_catids, $post;	
if(!is_user_logged_in()) $user_email = @$_POST['taker_email'];

if(watupro_intel()) require_once(WATUPRO_PATH."/i/models/question.php");
$taking_id = $_watu->add_taking($exam->ID);  
$_POST['watupro_current_taking_id'] = $GLOBALS['watupro_taking_id'] = $taking_id;  // needed in personality quizzes and shortcodes
$_watu->this_quiz = $exam;

$total = $score = $achieved = $max_points = 0; 
$result = $unresolved_questions = $current_text = '';
$user_grade_ids = array(); // used in personality quizzes (Intelligence module)
   
$question_catids = array(); // used for category based pagination
foreach ($all_question as $qct=>$ques) {	
		if(empty($ques->is_survey)) $total ++;
		// the two rows below are about the category headers	
		if(!$ques->exclude_on_final_screen) {
			$result .= watupro_cat_header($exam, $qct, $ques, 'submit');
			if(!in_array($ques->cat_id, $question_catids)) $question_catids[] = $ques->cat_id;
		}
		
      $qct++;
      $question_content = $ques->question;
      // fill the gaps need to replace gaps
      if($ques->answer_type=='gaps') $question_content = preg_replace("/{{{([^}}}])*}}}/", "_____", $question_content);

		$ansArr = is_array( @$_POST["answer-" . $ques->ID] )? $_POST["answer-" . $ques->ID] : array();      
				
		// points and correct calculation
		list($points, $correct) = WTPQuestion::calc_answer($ques, $ansArr, $ques->q_answers, $user_grade_ids);		
		$max_points += WTPQuestion::max_points($ques);
		
		// handle sorting personalities
		if($exam->is_personality_quiz and $ques->answer_type == 'sort' and watupro_intel()) {
			WatuPROIQuestion :: sort_question_personality($ques, $ansArr, $user_grade_ids);
		}
		
		// discard points?
		if($points and !$correct and $ques->reward_only_correct) $points = 0; 
						  			
  		list($answer_text, $current_text, $unresolved_text) = $_question->process($_watu, $qct, $question_content, $ques, $ansArr, $correct, $points);
  		$unresolved_questions .= str_replace('[[watupro-resolvedclass]]', '', $unresolved_text);
  		
  		// replace the resolved class
  		if($correct) $current_text = str_replace('[[watupro-resolvedclass]]','watupro-resolved',$current_text);
  		else $current_text = str_replace('[[watupro-resolvedclass]]','watupro-unresolved',$current_text);
  		
  		if(empty($ques->exclude_on_final_screen)) $result .= $current_text;		 
  		
  		// insert taking data
  		$_watu->store_details($exam->ID, $taking_id, $ques->ID, $answer_text, $points, $ques->question, $correct, $current_text);
        
      if($correct) $score++;  
      $achieved += $points;   
}

// uploaded files?
if($exam->no_ajax) $result = WatuPROFileHandler :: final_screen($result, $taking_id);
    
// calculate percentage
if($total==0) $percent=0;
else $percent = number_format($score / $total * 100, 2);
$percent = round($percent);

// generic rating
$rating=$_watu->calculate_rating($total, $score, $percent);
	
// assign grade
list($grade, $certificate_id, $do_redirect, $grade_obj) = WTPGrade::calculate($exam_id, $achieved, $percent, 0, $user_grade_ids);

// assign certificate if any
$certificate="";
if(!empty($certificate_id) and is_user_logged_in()) {	
	$certificate = WatuPROCertificate::assign($exam, $taking_id, $certificate_id, $user_ID);	
}

// category grades if any
$catgrades = WTPGrade::replace_category_grades($exam->final_screen, $taking_id, $exam->ID);

// replace some old confusingly named vars
$exam->final_screen = str_replace("%%SCORE%%", "%%CORRECT%%", $exam->final_screen);

// url to share the final screen and maybe redirect to it?
$post_url = empty($post) ? get_permalink($_POST['post_id']) : get_permalink($post->ID);
$post_url .= strstr($post_url, "?") ? "&" : "?";  
$share_url = $post_url."waturl=".base64_encode($exam->ID."|".$taking_id);
if(!empty($exam->shareable_final_screen) and !empty($exam->redirect_final_screen)) $do_redirect = $share_url;

// time spent on this quiz
$time_spent = '';
if(strstr($exam->final_screen, '%%TIME-SPENT%%') or strstr($exam->email_output, '%%TIME-SPENT%%')) {
	$taking = $wpdb->get_row($wpdb->prepare("SELECT start_time, end_time FROM ".WATUPRO_TAKEN_EXAMS." WHERE ID=%d", $taking_id));
	$taking->end_time = date('Y-m-d H:i:s');
	$time_spent = WTPRecord :: time_spent_human( WTPRecord :: time_spent($taking));
}

// calculate averages
$avg_points = $avg_percent = '';
if(strstr($exam->final_screen, '%%AVG-POINTS%%') or strstr($exam->email_output, '%%AVG-POINTS%%')) {
	$all_point_rows = $wpdb->get_results($wpdb->prepare("SELECT points FROM ".WATUPRO_TAKEN_EXAMS." 
		WHERE exam_id=%d AND in_progress=0 AND ID!=%d", $exam->ID, $taking_id));
	$all_points = 0;
	foreach($all_point_rows as $r) $all_points += $r->points;	
	$all_points += $achieved;			
	$avg_points = round($all_points / ($wpdb->num_rows + 1), 1);
}
if(strstr($exam->final_screen, '%%AVG-PERCENT%%') or strstr($exam->email_output, '%%AVG-PERCENT%%')) {
	$all_percent_rows = $wpdb->get_results($wpdb->prepare("SELECT percent_correct FROM ".WATUPRO_TAKEN_EXAMS." 
		WHERE exam_id=%d AND in_progress=0 AND ID!=%d", $exam->ID, $taking_id));
	$all_percent = 0;	
	foreach($all_percent_rows as $r) $all_percent += $r->percent_correct;	
	$all_percent += $percent; 	
	$avg_percent = round($all_percent  / ($wpdb->num_rows + 1));
}

$user_name = empty($_POST['taker_name']) ? $user_identity : $_POST['taker_name'];
if(empty($user_name)) $user_name = __('Guest', 'watupro');

// replace grade and gdesc first so any variables used in them can be replaced after that
$exam->final_screen = str_replace(array('%%GRADE%%', '%%GDESC%%'), array(wpautop($grade, false), wpautop(stripslashes(@$grade_obj->gdescription), false)), $exam->final_screen);
$exam->email_output = str_replace(array('%%GRADE%%', '%%GDESC%%'), array(wpautop($grade, false), wpautop(stripslashes(@$grade_obj->gdescription), false)), $exam->email_output);
	
// prepare output
$replace_these	= array('%%CORRECT%%', '%%TOTAL%%', '%%PERCENTAGE%%', '%%RATING%%', '%%CORRECT_ANSWERS%%', 
	'%%QUIZ_NAME%%', '%%DESCRIPTION%%', '%%POINTS%%', '%%CERTIFICATE%%', '%%GTITLE%%', '%%UNRESOLVED%%', 
'%%ANSWERS%%', '%%CATGRADES%%', '%%DATE%%', '%%EMAIL%%', '%%MAX-POINTS%%', '%%watupro-share-url%%',
	'%%TIME-SPENT%%', '%%USER-NAME%%', '%%AVG-POINTS%%', '%%AVG-PERCENT%%');
$with_these= array($score, $total,  $percent, $rating, $score, stripslashes($exam->name), wpautop(stripslashes($exam->description)), $achieved, 	   $certificate, stripslashes(@$grade_obj->gtitle), $unresolved_questions, $result, $catgrades, date(get_option('date_format'), current_time('timestamp')), $user_email, $max_points, $share_url, $time_spent, 
$user_name, $avg_points, $avg_percent);

// Show the results    
$output = "<div id='startOutput'>&nbsp;</div>";
$output .= str_replace($replace_these, $with_these, wpautop(stripslashes($exam->final_screen), false));
$output = watupro_parse_answerto($output, $taking_id);
$email_output=str_replace($replace_these, $with_these, wpautop(stripslashes($exam->email_output), false));
$email_output = watupro_parse_answerto($email_output, $taking_id);  


// store this taking
$_watu->update_taking($taking_id, $achieved, $grade, $output, $percent, $grade_obj, $catgrades);

// send API call
do_action('watupro_completed_exam', $taking_id);
if(watupro_intel() and !empty($exam->fee) and !empty($exam->pay_always)) do_action('watupro_completed_paid_exam', $taking_id, $exam);

$output = apply_filters('watupro_content', $output);	
$email_output = apply_filters('watupro_content', $email_output);

// show output on the screen
if(empty($do_redirect)) print WatuPRO::cleanup($output, 'web');
else echo "WATUPRO_REDIRECT:::".$do_redirect;

// update taking output with the filters
$wpdb->query( $wpdb->prepare( "UPDATE ".WATUPRO_TAKEN_EXAMS." SET details=%s WHERE ID=%d", $output, $taking_id));

if(!empty($exam->email_output)) $output=$email_output; // here maybe replace output with email output

// clear any timer related info for this exam
delete_user_meta( $user_ID, "start_exam_".$exam->ID );
    
// email details if required
$_watu->email_results($exam, $output, @$grade_obj->ID);     
if(empty($exam->no_ajax)) exit;// Exit due to ajax call