<?php
// store some of the logic here to encapsulate the things a little bit 
class WatuPRO {
	 static $output_sent = false;
	 
    function add_taking($exam_id, $in_progress=0) {
        global $user_ID, $wpdb;   
        
        // existing incomplete taking with this exam and user ID?
        if(!empty($user_ID)) {
        		$exists=$wpdb->get_var($wpdb->prepare("SELECT ID FROM ".WATUPRO_TAKEN_EXAMS."
        			WHERE user_id=%d AND exam_id=%d AND in_progress=1",$user_ID,$exam_id));
        		if(!empty($exists))  $taking_id=$exists;  
        		
        		// when completing the exam in_progress should become 0
        		if(!$in_progress and !empty($taking_id)) {
        			$wpdb->query("UPDATE ".WATUPRO_TAKEN_EXAMS." SET in_progress=0 WHERE ID='$taking_id'");
        		}      		
        } 
        
        if(empty($taking_id)) {
					  // select exam
					  $exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", $exam_id));					  
					  if(!empty($_POST['start_time'])) {
					  		$start_time = $_POST['start_time'];
					  		if(!strstr($start_time, '-')) $start_time = date("Y-m-d H:i:s", $start_time); // make sure it's in datetime format and not unix timestamp
					  }
					  else $start_time = current_time('mysql');
					  
				// avoid re-saving on page refresh (when no ajax)
				if($exam->no_ajax) {
					$taking_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM ".WATUPRO_TAKEN_EXAMS."
						WHERE ip=%s AND user_id=%d AND start_time=%s AND exam_id=%d",
						$_SERVER['REMOTE_ADDR'], $user_ID, $start_time, $exam->ID));
					if(!empty($taking_id)) return $taking_id;	
				}				      	
        	
        		$wpdb->insert(WATUPRO_TAKEN_EXAMS, array(
	            "user_id"=>$user_ID,
	            "exam_id"=>$exam_id, 
	            "date"=>date("Y-m-d"),
	            "start_time"=>$start_time,
	            "ip"=>$_SERVER['REMOTE_ADDR'],
	            "in_progress"=>$in_progress,
	            "details" => "",
	            "result" => "",
	            "end_time" => "2000-01-01 00:00:00",
	            "grade_id" => 0,
	            "percent_correct" => 0,
	            "serialized_questions" => $_POST['watupro_questions'],
	            "points" => 0
			   ),
		      array('%d','%d','%s','%s','%s','%s','%s','%s','%s','%d','%d', '%s', '%s'));
		        
		      // save the ID just in case
		      $taking_id=$wpdb->insert_id;
        }
        
        update_user_meta( $user_ID, "current_watupro_taking_id", $taking_id);
        
        return $taking_id;
    }

    // store results in the DB
    function update_taking($taking_id, $points, $grade, $details="", $percent = 0, $grade_obj = null, $catgrades = '') {
        // update existing taking   
         global $user_ID, $wpdb;     
         
         // user data from contact fields
         $contact_data = '';   
        	if(!empty($_POST['taker_phone']) or !empty($_POST['taker_company'])) {
        		$contact_datas = array();
        		if(!empty($_POST['taker_phone'])) $contact_datas[] = sprintf(__('Phone: %s', 'watupro'), $_POST['taker_phone']);
        		if(!empty($_POST['taker_company'])) $contact_datas[] = sprintf(__('Company: %s', 'watupro'), $_POST['taker_company']);
        		$contact_data = implode(', ', $contact_datas);
        	}
        	
        	if(!empty($_POST['watupro_taker_email'])) $_POST['taker_email'] = $_POST['watupro_taker_email'];
			if(!empty($_POST['watupro_taker_name'])) $_POST['taker_name'] = $_POST['watupro_taker_name'];
         
         $num_hints_used = $wpdb->get_var($wpdb->prepare("SELECT SUM(num_hints_used) FROM ".WATUPRO_STUDENT_ANSWERS."
         	WHERE taking_id=%d", $taking_id));
                    
        $wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_TAKEN_EXAMS." SET 
            details=%s, points=%s, result=%s, end_time=%s, percent_correct=%d, grade_id=%d, email=%s, catgrades=%s, 
            num_hints_used=%d, name=%s, contact_data=%s 
            WHERE id=%d", 
			      $details, $points, wpautop($grade, false), current_time('mysql'), $percent, @$grade_obj->ID, 
			      @$_POST['taker_email'], $catgrades, $num_hints_used, @$_POST['taker_name'], $contact_data, $taking_id));
    }  
    
    // email exam details to where is selected
    // grade_id is passed to check if there is advanced setting that limits sending email to user
    function email_results($exam, $output, $grade_id = null) {
    	   global $user_ID, $user_email;
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
			$headers .= 'From: '. watupro_admin_email() . "\r\n";
			
			$admin_output = $output = WatuPRO::cleanup($output);		
			if(strstr($output, "{{{split}}}")) {
				$parts = explode("{{{split}}}", $output);
				$output = trim($parts[0]);
				$admin_output = trim($parts[1]);
			}	
			
			$admin_subject = __('User results on %%QUIZ_NAME%%', 'watupro');
			$user_subject = __('Your results on %%QUIZ_NAME%%', 'watupro');	
			if(!empty($exam->email_subject)) {
				$email_subject = stripslashes($exam->email_subject);
				if(strstr($email_subject, '{{{split}}}')) {
					list($user_subject, $admin_subject) = explode('{{{split}}}', $email_subject);
				}
				else $user_subject = $admin_subject = $email_subject;
			}	
		
			$output='<html><head><title>'.__('Your results on ', 'watupro').$exam->name.'</title>
			</head>
			<html><body>'.$output.'</body></html>';
			// echo $output;
				
			if(!is_user_logged_in() or !empty($_POST['taker_email'])) $user_email = @$_POST['taker_email'];
			// user setting may override the var
			if($exam->email_taker and is_user_logged_in() and get_user_meta($user_ID, 'watupro_no_quiz_mails', true)) $exam->email_taker = false;
			
			// now email user
			if($exam->email_taker and $user_email) {
				// check for grade-related restrictions
				$advanced_settings = unserialize( stripslashes($exam->advanced_settings));
				if(!empty($advanced_settings['email_grades']) and is_array($advanced_settings['email_grades'])
					and !in_array($grade_id, $advanced_settings['email_grades'])) $dont_email_taker = true;			
					
				$user_subject = str_replace('%%QUIZ_NAME%%', stripslashes($exam->name), $user_subject);	
				if(empty($dont_email_taker)) wp_mail($user_email, $user_subject, $output, $headers);				
			}
			
			if($exam->email_admin) {				
				// if user is logged in, let admin know who is taking the test
				$output = $admin_output;
				// echo($output);
				$user_data = $user_email;
				if(!empty($_POST['taker_name'])) $user_data .= " ($_POST[taker_name])";
				if(!empty($user_email)) $output="Details of $user_data:<br><br>".$output;			
				
				$admin_email = empty($exam->admin_email)?	get_option('admin_email') : $exam->admin_email;
				
				$admin_subject = str_replace('%%QUIZ_NAME%%', stripslashes($exam->name), $admin_subject); 
				wp_mail($admin_email, $admin_subject, $output, $headers);
			}
	}
	
	// see if user still can take the exam depending on number of takings allowed
	// returns true if they can take and false if they can't 
	function can_retake($exam) {
		global $wpdb, $user_ID;
		
		// no login required but have a restriction by IP
		if(!empty($exam->takings_by_ip)) {
			// select number of takings by this IP address
			$num_taken = $wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) FROM ".WATUPRO_TAKEN_EXAMS."
				WHERE exam_id=%d AND ip=%s AND in_progress=0", $exam->ID, $_SERVER['REMOTE_ADDR']));
				
			if($exam->takings_by_ip <= $num_taken) {
				echo "<p><b>";
				printf(__("Sorry, you can take this quiz only %d times.", 'watupro'), $exam->takings_by_ip);
				echo "</b></p>";
				return false;
			}	
		} // end IP based check		
		
		// no limits if login is not required
		if(!$exam->require_login) return true;		
		
		if($exam->take_again) {			
			// Intelligence limitations
			if(watupro_intel()) {
				require_once(WATUPRO_PATH."/i/models/exam_intel.php");
				if(!WatuPROIExam::can_retake($exam)) return false;
			}		
		
			if(empty($exam->times_to_take) and (empty($exam->retake_grades) or strlen($exam->retake_grades) <=2) ) return true; // 0 = unlimited

         // now select number of takings			
			if(!is_user_logged_in()) {
				echo __("Sorry, you are not allowed to submit this quiz.", 'watupro');				
				return false;
			}
			
			$cnt_takings=$wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) FROM ".WATUPRO_TAKEN_EXAMS."
				WHERE exam_id=%d AND user_id=%d AND in_progress=0", $exam->ID, $user_ID));
			if(!$cnt_takings) return true; // if there are no takings no need to check further			
				
			if(!empty($exam->times_to_take) and $cnt_takings >= $exam->times_to_take) {
				echo "<p><b>";
				printf(__("Sorry, you can take this quiz only %d times.", 'watupro'), $exam->times_to_take);
				echo "</b></p>";
				return false;
			}		
			
			// all OK so far? Let's see if we have grade-based limitation and there are previous takings
			if(!empty($exam->retake_grades) and strlen($exam->retake_grades) > 2 and $cnt_takings) {
				$grids = explode("|", $exam->retake_grades);
				$grids = array_filter($grids);
				
				if(sizeof($grids)) {
					// get latest taking
					$latest_taking = $wpdb->get_row( $wpdb->prepare("SELECT * FROM ".WATUPRO_TAKEN_EXAMS." 
						WHERE exam_id=%d AND user_id=%d AND in_progress=0 ORDER BY ID DESC LIMIT 1", $exam->ID, $user_ID));
						
					if(!in_array($latest_taking->grade_id, $grids)) {
						echo "<p><b>";
						_e("You can't take this quiz again because of the latest grade you achieved on it.", 'watupro');
						echo "</b></p>";
						return false;
					}	
				}	
			}	// end grade-related limitation check		
					
		} // end if $exam->take_again
		else {
			// Only 1 taking allowed: see if exam is already taken by this user
			$taking=$this->get_taking($exam);
						
			if(!empty($taking->ID) and !$taking->in_progress) {
				echo "<p><b>";
				printf(__("Sorry, you can take this %s only once!", 'watupro'), __('quiz', 'watupro'));
				echo "</b></p>";
				return false;
			}
		}		
		
		// just in case
		return true;
	}
	
	// get existing taking for given exam (only for logged in users)
	function get_taking($exam)	{
		global $wpdb, $user_ID;		
		if(!is_user_logged_in()) return false;
		
		$taking=$wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}watupro_taken_exams
			WHERE exam_id=%d AND user_id=%d ORDER BY ID DESC LIMIT 1", $exam->ID, $user_ID));
			
		return $taking;	
	}
	
	// verifies if time limit is fine and there is no cheating
	// allow 15 seconds for submitting in case of server overload
	function verify_time_limit($exam, $in_progress = null) {
		global $user_ID;
		
		if(!$exam->full_time_limit) return true;
		
		if(is_user_logged_in() and $in_progress) {
			// compare with saved data
			$start = watupro_mktime($in_progress->start_time);			
			//echo ($start+$exam->full_time_limit*60+10)."<br>".current_time();
			//exit;
			if($start and ($start+$exam->full_time_limit*60+10)<current_time('timestamp')) return false;
		}
		/*else {
			// check based on post field			
			if(($_POST['start_time']+$exam->full_time_limit*60+10)<current_time()) return false;
		}*/		
		return true;
	}
	
	// small helper to convert answer ID's into texts
	function answer_text($answers, $ansArr) {
		$answer_text="";
		foreach($answers as $answer) {
			if(in_array($answer->ID, $ansArr)) {
				if(!empty($answer_text)) $answer_text.=", ";
				$answer_text.=$answer->answer;
			}
		}
		
		return $answer_text;
	}
	
    // INSERT specific details in watupro_student_answers 
    // done either in completing exam or while clicking next/prev
    // $points and question_text are not required for in_progress takings. As there we only need to store
    // what answer is given so student can continue
    // $answer is answer text when we are completing the exam. But it's stored as (ID, text, or array)
    // if we are storing in progress data - because it's easier to save&retrieve this way
    function store_details($exam_id, $taking_id, $question_id, $answer, $points=0, $question_text="", $is_correct=0, $snapshot = '') {
        global $wpdb, $user_ID;
        
        if(empty($points)) $points = "0.00";
        
        // remove hardcoded correct/incorrect images if any
	    	// (for example we may have these in fill the gaps questions)
	    	$answer = str_replace('<img src="'.plugins_url("watupro").'/correct.png" hspace="5">', '', $answer);
	    	$answer = str_replace('<img src="'.plugins_url("watupro").'/wrong.png" hspace="5">', '', $answer);	    	
                
        // if detail exists update
        $detail=$wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_STUDENT_ANSWERS."
         WHERE taking_id=%d AND exam_id=%d AND question_id=%d", $taking_id, $exam_id, $question_id));
         
        // question hits if any
        $hints = @$_POST['question_'.$question_id.'_hints'];						
        $no_hints = sizeof( explode("watupro-hint", $hints) ) - 1; 
        $question_text = ''; // unset this, we'll no longer store it for performance reasons
         
        if(empty($detail->ID)) {
    		   $wpdb->insert(WATUPRO_STUDENT_ANSWERS,
    			array("user_id"=>$user_ID, "exam_id"=>$exam_id, "taking_id"=>$taking_id,
    				"question_id"=>$question_id, "answer"=>$answer,
    				"points"=>$points, "question_text"=>$question_text, 
    				"is_correct" => $is_correct, 'snapshot'=>$snapshot, 'hints_used'=>$hints, 
    				"num_hints_used" => $no_hints, "onpage_question_num" => @$_POST['current_question'],
    				"feedback" => @$_POST['feedback-'.$question_id]),
    			array("%d","%d","%d","%d","%s","%f","%s", "%d", "%s", "%s", "%d", "%d", "%s"));    
    			$detail_id = $wpdb->insert_id;			
        }
        else {
				// don't remove the snapshot
				if(empty($snapshot) and !empty($detail->snapshot)) $snapshot = stripslashes($detail->snapshot);        	
        	
            $wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_STUDENT_ANSWERS." SET
               answer=%s, points=%s, question_text=%s, is_correct=%d, snapshot=%s, hints_used = %s, 
               num_hints_used=%d, onpage_question_num=%d, feedback=%s
               WHERE id=%d", $answer, $points, $question_text, $is_correct, $snapshot, $hints, $no_hints,
               @$_POST['current_question'], @$_POST['feedback-'.$question_id], $detail->ID ));
            $detail_id = $detail->ID;                 
        } 
        
        // uploaded file?
        WatuPROFileHandler :: upload_file($question_id, $detail_id, $taking_id);
    }
    
    // regroup questions by category or pull random per category
    function group_by_cat($questions, $exam) {
    		$advanced_settings = unserialize( stripslashes($exam->advanced_settings) );
    		   		
			// pull random by category?    	
			if($exam->pull_random and $exam->random_per_category) {
				$cat_ids = array();
				$cats = array();
				
				foreach($questions as $cnt=>$question) {
					 if(!in_array($question->cat_id, $cat_ids)) {
					 		$cat_ids[] = $question->cat_id;
							$cats[$question->cat_id] = 0;
					 }
								 
					 // enough questions in the category? then skip this one	
					 $pull_random = isset($advanced_settings['random_per_'.$question->cat_id]) ? 
					 	intval($advanced_settings['random_per_'.$question->cat_id]) : $exam->pull_random;		
					 					 				 
					 if($cats[$question->cat_id] >= $pull_random) {
					 		unset($questions[$cnt]);
					 		continue;
					 }
					 
					 $cats[$question->cat_id]++;
				}
			}
    	
    	  // now group by category if selected
    	  if(!$exam->group_by_cat) return $questions;

			// now regroup
			$cats=array();
			foreach($questions as $question) {
				if(empty($question->cat)) $question->cat = __('Uncategorized', 'watupro');
				if(!in_array($question->cat, $cats)) $cats[]=$question->cat;
			}    
			
			$cats = WTPCategory :: sort_cats($cats, $advanced_settings, $exam);	
			
			$regrouped_questions=array();
			
			foreach($cats as $cat) {
				foreach($questions as $question) {
					if($question->cat==$cat) $regrouped_questions[]=$question;
				}
			}			
    	
    	  return $regrouped_questions;
    }
    
    // calculate generic rating
    function calculate_rating($total, $score, $percent) {
    	$all_rating = array(__('Failed', 'watupro'), __('Failed', 'watupro'), __('Failed', 'watupro'), __('Failed', 'watupro'), __('Just Passed', 'watupro'),
    	__('Satisfactory', 'watupro'), __('Competent', 'watupro'), __('Good', 'watupro'), __('Very Good', 'watupro'),__('Excellent', 'watupro'), __('Unbeatable', 'watupro'), __('Cheater', 'watupro'));
    	$rate = intval($percent / 10);
    	if($percent == 100) $rate = 9;
    	if($score == $total) $rate = 10;
    	if($percent>100) $rate = 11;
    	$rating = @$all_rating[$rate];
    	return $rating;
    }
    
    // match answers to questions and if required show only some of the answers
    function match_answers(&$all_question, $exam) {
    		global $wpdb, $ob;
    		
    		$ob = "sort_order,ID";
    		// if answers are limited, correct is selected first, then we'll shuffle the answers    		
    		if($exam->num_answers) $ob="correct DESC, RAND()";
    		if(!$exam->num_answers and ($exam->randomize_questions==1 or $exam->randomize_questions==3)) $ob = "RAND()";
    		
    	   $qids=array(0);
			foreach($all_question as $question) $qids[]=$question->ID;
			$qids=implode(",",$qids);
			
			// answers array accordingly to randomization settings
			$all_answers = $wpdb->get_results("SELECT *	FROM ".WATUPRO_ANSWERS."
			WHERE question_id IN ($qids) ORDER BY $ob");
			
			// because of survey and true/false, always select ordered by ID
			$all_answers_by_order = $wpdb->get_results("SELECT *	FROM ".WATUPRO_ANSWERS."
			WHERE question_id IN ($qids) ORDER BY sort_order, ID");
			
			foreach($all_question as $cnt=>$question) {
				$all_question[$cnt]->q_answers = array();
				 
				// see whether we use the pre-ordered or randomized questions 	
				if($question->is_survey or $question->truefalse or $question->answer_type == 'matrix') $answers_for_use = $all_answers_by_order;
				else $answers_for_use = $all_answers ;
				
				foreach($answers_for_use as $answer) {
					 if($answer->question_id==$question->ID) {
					 		$all_question[$cnt]->q_answers[]=$answer;
					 }
				}	
				
				// shall we cut number of answers?
				if($exam->num_answers and !$question->is_survey and !$question->truefalse 
					and $question->answer_type!='matrix' and $question->answer_type!='textarea') {
					$all_question[$cnt]->q_answers = array_slice($all_question[$cnt]->q_answers, 0, $exam->num_answers);
					
					// shuffle again to make sure the correct are not on top
					shuffle($all_question[$cnt]->q_answers);
				}
			} // end foreach question
    }
    
    // check if user can access exam
    static function can_access($exam) {    	
    	 // always access public exams
		 if(!$exam->require_login) return true;   
		 
		 if($exam->require_login and !is_user_logged_in()) return false;
		 
		 // admin can always access
		 if(current_user_can('manage_options') or current_user_can('watupro_manage_exams')) {
		 	if(empty($_POST['action']) and $exam->fee > 0) echo "<b>".__('Note: This quiz requires payment, but you are administrator and do not need to go through it.','watupro')."</b>";
		 	return true;
		 }
		 
    	     	 
    	 // USER GROUP CHECKS
		 $allowed = WTPCategory::has_access($exam);
		 
		 if(!$allowed) {
		 		echo "<!-- not in allowed user group -->";
		 		return false;
		 }
		 
		 // INTELLIGENCE MODULE RESTRICTIONS
		 if(watupro_intel()) {
			if($exam->fee > 0) {				
				require_once(WATUPRO_PATH."/i/models/payment.php");
				if(!empty($_POST['stripe_pay'])) WatuPROPayment::Stripe(); // process Stripe payment if any
				if(!WatuPROPayment::valid_payment($exam)) {
					self::$output_sent = WatuPROPayment::render($exam);
					return false;					
				}				
			}		 	
		 	
		 	require_once(WATUPRO_PATH."/i/models/dependency.php");
		 	if(!WatuPRODependency::check($exam)) {
		 		echo "<!-- WATUPROCOMMENT unsatisfied dependencies -->";
		 		return false;
		 	}
		 }

    	 return true;
	 }
	 
	 // convert our special correct/wrong classes to 
	 // simple HTML so it can be visible in email and downloaded doc
	 static function cleanup($output, $media='email') {
	 	// replace correct/wrong classes for the email
		$correct_style=' style="padding-right:20px;background:url('.plugins_url("watupro").'/correct.png) no-repeat right top;" ';
		$wrong_style=' style="padding-right:20px;background:url('.plugins_url("watupro").'/wrong.png) no-repeat right top;" ';
		$user_answer_style = ' style="font-weight:bold;color:blue;" ';
		
		// of blank == true just remove the comments (to avoid cluttering the HTML response)
		if($media=='web') $correct_style=$wrong_style="";		
		
		$output=str_replace('><!--WATUEMAILanswerWATUEMAIL--','',$output);
		$output=str_replace('><!--WATUEMAILanswer user-answer correct-answerWATUEMAIL--', $correct_style,$output);
		$output=str_replace('><!--WATUEMAILanswer correct-answerWATUEMAIL--',$correct_style,$output);
		$output=str_replace('><!--WATUEMAILanswer user-answerWATUEMAIL--', $wrong_style,$output);
		
		// in email we have to replace user-answer in <li> tag with hardcoded code
		// the class is replaced even when it contains correct-answer
		if($media == 'email') {
			$output = str_replace("<li class='answer user-answer'>", "<li ".$user_answer_style.">", $output);
			$output = str_replace("<li class='answer user-answer correct-answer'>", "<li ".$user_answer_style.">", $output);
			
			// fill the gaps have this code
			$output = str_replace('<span class="user-answer">', "<span ".$user_answer_style.">", $output);
			$output = str_replace('<span class="user-answer-unrevealed">', "<span ".$user_answer_style.">", $output);
			$output = str_replace("<li class='answer user-answer-unrevealed'>", "<li ".$user_answer_style.">", $output);
			
			// and some questions have it this way:
			$output = str_replace("<li class='answer user-answer-unrevealed '>", "<li ".$user_answer_style.">", $output);
		}
		
		// shortcodes
		if($media=='web')  $output=do_shortcode($output);	
		else 	$output=strip_shortcodes($output);
				
		return $output;
	 }
}


/******************************** Procedure functions below ************************************/
function watupro_taking_details() {
		global $wpdb, $user_ID;
		
		// select taking
		$taking=$wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_TAKEN_EXAMS."
			WHERE id=%d", $_REQUEST['id']));
		
		// select user
		$student=$wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->users} 
			WHERE id=%d", $taking->user_id));

		// make sure I'm admin or that's me
		if(!current_user_can(WATUPRO_MANAGE_CAPS) and $student->ID!=$user_ID) {
			wp_die( __('You do not have sufficient permissions to access this page', 'watupro') );
		}
		
		// select detailed answers
		$answers=$wpdb->get_results($wpdb->prepare("SELECT tA.*, tQ.question as question, tQ.feedback_label as feedback_label
		FROM ".WATUPRO_STUDENT_ANSWERS." tA JOIN ".WATUPRO_QUESTIONS." tQ ON tQ.id=tA.question_id 
		WHERE taking_id=%d ORDER BY id", $taking->ID));
		
		// select exam
		$exam=$wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE id=%d", $taking->exam_id));
		
		if($exam->no_ajax) {
			// any uploaded files?
			$files = $wpdb->get_results($wpdb->prepare("SELECT ID, user_answer_id, filename, filesize FROM ".WATUPRO_USER_FILES."
				WHERE taking_id=%d", $taking->ID));
				
			foreach($answers as $cnt=>$answer) {
				foreach($files as $file) {
					if($file->user_answer_id == $answer->ID) $answers[$cnt]->file = $file;
				}
			}	
		} // end no_ajax
		
		$advanced_settings = unserialize(stripslashes($exam->advanced_settings));
		if(current_user_can(WATUPRO_MANAGE_CAPS)) $advanced_settings['show_only_snapshot'] = null;
		
		// export?
		if(!empty($_GET['export'])) {
			if(!empty($advanced_settings['show_only_snapshot']) and !current_user_can(WATUPRO_MANAGE_CAPS)) {
				wp_die( __('You do not have sufficient permissions to access this page', 'watupro') );
			}
			
			$now = gmdate('D, d M Y H:i:s') . ' GMT';
			header('Content-Type: ' . watupro_get_mime_type());
			header('Expires: ' . $now);
			header('Content-Disposition: attachment; filename="results.doc"');
			header('Pragma: no-cache');			
			echo "<html>";
			echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">";
			echo "<body>";
			
			if(@file_exists(get_stylesheet_directory().'/watupro/taking_details.php')) require get_stylesheet_directory().'/watupro/taking_details.php';
			else require WATUPRO_PATH."/views/taking_details.php";
			
			echo "</body></html>";
			exit;
		}
		   
		if(@file_exists(get_stylesheet_directory().'/watupro/taking_details.php')) require get_stylesheet_directory().'/watupro/taking_details.php';
		else require WATUPRO_PATH."/views/taking_details.php";
		exit;
}

function watupro_define_newline() {
	// credit to http://yoast.com/wordpress/users-to-csv/
	$unewline = "\r\n";
	if (strstr(strtolower($_SERVER["HTTP_USER_AGENT"]), 'win')) {
	   $unewline = "\r\n";
	} else if (strstr(strtolower($_SERVER["HTTP_USER_AGENT"]), 'mac')) {
	   $unewline = "\r";
	} else {
	   $unewline = "\n";
	}
	return $unewline;
}

function watupro_get_mime_type()  {
	// credit to http://yoast.com/wordpress/users-to-csv/
	$USER_BROWSER_AGENT="";

			if (preg_match('/OPERA(\/| )([0-9].[0-9]{1,2})/', strtoupper($_SERVER["HTTP_USER_AGENT"]), $log_version)) {
				$USER_BROWSER_AGENT='OPERA';
			} else if (preg_match('/MSIE ([0-9].[0-9]{1,2})/',strtoupper($_SERVER["HTTP_USER_AGENT"]), $log_version)) {
				$USER_BROWSER_AGENT='IE';
			} else if (preg_match('/OMNIWEB\/([0-9].[0-9]{1,2})/', strtoupper($_SERVER["HTTP_USER_AGENT"]), $log_version)) {
				$USER_BROWSER_AGENT='OMNIWEB';
			} else if (preg_match('/MOZILLA\/([0-9].[0-9]{1,2})/', strtoupper($_SERVER["HTTP_USER_AGENT"]), $log_version)) {
				$USER_BROWSER_AGENT='MOZILLA';
			} else if (preg_match('/KONQUEROR\/([0-9].[0-9]{1,2})/', strtoupper($_SERVER["HTTP_USER_AGENT"]), $log_version)) {
		    	$USER_BROWSER_AGENT='KONQUEROR';
			} else {
		    	$USER_BROWSER_AGENT='OTHER';
			}

	$mime_type = ($USER_BROWSER_AGENT == 'IE' || $USER_BROWSER_AGENT == 'OPERA')
				? 'application/octetstream'
				: 'application/octet-stream';
	return $mime_type;
}

// calls $watu->store details
// called by ajax, add_action('wp_loaded','watupro_store_details'); is in main watupro.php
function watupro_store_details() {
   // only for logged in users
   if(!is_user_logged_in()) exit;
   
   $_watu=new WatuPRO();
   $taking_id=$_watu->add_taking($_POST['exam_id'],1);
   $answer = serialize(@$_POST['answer-'.$_POST['question_id']]);
   $_watu->store_details($_POST['exam_id'], $taking_id, $_POST['question_id'], $answer);
   exit;
}

// calls watpro_store_details for each question in $_POST
// called by ajax when user clicks the optional save buton
function watupro_store_all($question_ids) {
	if(!is_user_logged_in()) exit;
	$_watu=new WatuPRO();
	$taking_id=$_watu->add_taking($_POST['exam_id'],1);
	
	$qids = $_POST['question_ids'];	
	foreach($qids as $qid) {
		$answer=serialize($_POST['answer-'.$qid]);
		$_watu->store_details($_POST['exam_id'], $taking_id, $qid, $answer);
	}
}

function watupro_submit() {
	require(WATUPRO_PATH."/show_exam.php");
	exit;
}

function watupro_initialize_timer() {
	// set up timer and return time as ajax
	// to avoid cheating this won't happen if current $in_progress taking exists for this exam and user
	global $user_ID;
	$time=current_time('timestamp');
	
	if(is_user_logged_in()) {
		$meta_start_time = get_user_meta($user_ID, "start_exam_".$_REQUEST['exam_id'], true);
		if(empty($meta_start_time)) update_user_meta( $user_ID, "start_exam_".$_REQUEST['exam_id'], $time);
	}
	
	echo "<!--WATUPRO_TIME-->".$time."<!--WATUPRO_TIME-->";
	exit;
}

// check if intelligence module is present
function watupro_intel() {
	if(file_exists(WATUPRO_PATH."/i/controllers/practice.php")) return true;
	else return false;
}

// similar to above but for other modules
function watupro_module($module) {
	if(@file_exists(WATUPRO_PATH."/modules/".$module."/controllers/init.php")) return true;
	else return false;
}