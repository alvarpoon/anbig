<?php
// object to handle "takings" - stored records of taken exams
class WTPRecord {
	 function export($takings, $exam) {
			global $wpdb;	 	
	 		$newline=watupro_define_newline();
			$rows=array();
			
			// add all first names and last names to match them when exporting
			$uids = array(0);
			foreach($takings as $taking) {
				if(!empty($taking->user_id)) $uids[] = $taking->user_id;
			} 
			$uids = array_unique($uids);
			$first_names = $wpdb->get_results("SELECT meta_value, user_id FROM {$wpdb->usermeta}
				WHERE meta_key = 'first_name' AND user_id IN (".implode(",", $uids).")");
			$last_names = $wpdb->get_results("SELECT meta_value, user_id FROM {$wpdb->usermeta}
				WHERE meta_key = 'last_name' AND user_id IN (".implode(",", $uids).")");
			foreach($takings as $cnt=>$taking) {
				foreach($first_names as $first_name) {
					if($first_name->user_id == $taking->user_id) $takings[$cnt]->first_name = $first_name->meta_value;
				}
				
				foreach($last_names as $last_name) {
					if($last_name->user_id == $taking->user_id) $takings[$cnt]->last_name = $last_name->meta_value;
				}
			}	// end adding first / last names to takings	 
			
			if(empty($_GET['details'])) $rows[]=__('First name', 'watupro')."\t".__('Last name', 'watupro')."\t".__('Username and details', 'watupro').
				"\t".__('Email', 'watupro')."\t".__('IP', 'watupro').	"\t".__('Date', 'watupro')."\t".__('Points', 'watupro').
				"\t".__('Percent correct', 'watupro')."\t".__('Grade', 'watupro');
			else {
				// exports with questions and answers
				$questions = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".WATUPRO_QUESTIONS."
					WHERE exam_id=%d ORDER BY ID", $exam->ID));
					
					$titlerow =__('First name', 'watupro')."\t".__('Last name', 'watupro')."\t".__('Username', 'watupro')."\t".__('Email', 'watupro')."\t".__('IP', 'watupro')."\t".__('Date', 'watupro') . "\t";
					foreach($questions as $question) {
						 // strip tags and remove semicolon to protect the CSV sanity						 
						 $question_txt = strip_tags(str_replace("\t","   ",$question->question));
						 $question_txt = str_replace("\n", " ", $question_txt);
						 $question_txt = str_replace("\r", " ", $question_txt);
						 $question_txt = stripslashes($question_txt);
						 $titlerow .= $question_txt."\t";
					}
					$titlerow .= __('Points', 'watupro')."\t".__('% Correct', 'watupro')."\t".__('Grade', 'watupro');		
					$rows[] = $titlerow;		
					
					// we also have to get full details so they can be matched below
					$details = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".WATUPRO_STUDENT_ANSWERS."
						WHERE exam_id=%d", $exam->ID));	
			}
			
			foreach($takings as $taking) {
					$taking_email = ($taking->user_id) ? $taking->user_email : $taking->email;
					$taking_name = ($taking->user_id ? $taking->display_name : (empty($taking->name) ? "N/A" : stripslashes($taking->name))); 
					
					// add contact data if any
					if(!empty($taking->contact_data)) $taking_name .= ' - '.$taking->contact_data;					
					
				   $row = (!empty($taking->first_name) ? $taking->first_name : "N/A") . "\t" . 
				   (!empty($taking->last_name) ? $taking->last_name : "N/A") . "\t" .
					$taking_name."\t".
					($taking_email?$taking_email:"N/A")."\t".
					$taking->ip."\t".date(get_option('date_format'), strtotime($taking->date))."\t";
					
			  if(!empty($_GET['details'])) {
			  	 foreach($questions as $question) {
			  	 		$answer = $feedback = "";
			  	 		foreach($details as $detail) {
		  	 			 if($detail->taking_id==$taking->ID and $detail->question_id==$question->ID) {		
								// handle matrix better
								if($question->answer_type == 'matrix') {
									$detail->answer = str_replace('</td><td>', ' = ', $detail->answer);
									$detail->answer = str_replace('</tr><tr>', '; ', $detail->answer);
								}			  	 			 
		  	 			 	  	 			 		
		  	 			 		$answer = strip_tags(str_replace("\t","   ",$detail->answer));
		  	 			 		$answer = str_replace("\n", " ", $answer);
		  	 			 		$answer = str_replace("\r", " ", $answer);
								$answer = stripslashes($answer);
								
								// question accepts user feedback?
								if($question->accept_feedback and !empty($detail->feedback)) {									
									$feedback = strip_tags(str_replace("\t","   ",$detail->feedback));
				  	 			 	$feedback = str_replace("\n", " ", $feedback);
				  	 			 	$feedback = str_replace("\r", " ", $feedback);
									$feedback = stripslashes($feedback);
									$answer .= "; ".stripslashes($question->feedback_label)." ".$feedback;
								}	// end if accepts feedback
		  	 			 	} // end if detail matches taking and question
						}	// end foreach answer				
							
						$row .= $answer."\t";
			  	 } // end foreach question
			  }					
					
				$taking_result = strip_tags(str_replace("\t","   ",$taking->result));
			  	$taking_result = str_replace("\n", " ", $taking_result);
			  	$taking_result = str_replace("\r", " ", $taking_result);
				$taking_result = stripslashes($taking_result);	
					
				$row .=	$taking->points."\t" . $taking->percent_correct."\t" .$taking_result;
					
				$rows[] = $row;	
			}
			
			$csv=implode($newline,$rows);
			
			// credit to http://yoast.com/wordpress/users-to-csv/	
			$now = gmdate('D, d M Y H:i:s') . ' GMT';
		
			header('Content-Type: ' . watupro_get_mime_type());
			header('Expires: ' . $now);
			header('Content-Disposition: attachment; filename="quiz-'.$exam->ID.'.csv"');
			header('Pragma: no-cache');
			echo $csv;
			exit;
	 }
	 
	 // helper to calculate time spent in exam
	static function time_spent($taking) {
		list($date, $time) = explode(" ", $taking->start_time);
		list($y, $m, $d) = explode("-", $date);
		list($h, $min, $s) = explode(":", $time);		 		
 		$start_time = mktime($h, $min, $s, $m, $d, $y);
 		
 		list($date, $time) = explode(" ", $taking->end_time);
 		list($y, $m, $d) = explode("-", $date);
 		list($h, $min, $s) = explode(":", $time);
 		$end_time = mktime($h, $min, $s, $m, $d, $y);
 		
 		$diff = $end_time - $start_time;
 		
 		if($diff < 0) $diff = 0;
 		
 		return $diff;
	} 
	
	static function time_spent_human($time_spent) {
		$time_spent = ($time_spent > 60) ? gmdate("H:i", $time_spent) : gmdate("H:i:s", $time_spent);
		return $time_spent;
	}	
}