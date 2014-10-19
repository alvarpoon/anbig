<?php
// Watu PRO Question model
class WTPQuestion {
	public static $advanced_settings = '';
	public static $in_progress = '';	
	
	static function add($vars) {
		global $wpdb;
		
		// get max sort order
		if(empty($vars['sort_order'])) {
			if(empty($vars['add_first'])) {				
				$sort_order=$wpdb->get_var($wpdb->prepare("SELECT MAX(sort_order) FROM ".WATUPRO_QUESTIONS."
				WHERE exam_id=%d", $vars['quiz']));
				$sort_order++;
			} else {
				// adding it as first question
				$sort_order = 1;				
				$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_QUESTIONS." SET sort_order = sort_order+1 WHERE exam_id=%d", $vars['quiz']));
			}
		}
		else $sort_order=$vars['sort_order'];	
		
		$elaborate_explanation = (empty($vars['do_elaborate_explanation']) or empty($vars['elaborate_explanation'])) ? '' : $vars['elaborate_explanation'];
		$tags = "|".str_replace(",", "|", str_replace(", ", "|", @$vars['tags']) )."|";
		
		// open end display - accept file?
		if(!empty($vars['accept_file_upload'])) $vars['open_end_display'] .= '|file';
		
		// single-choice dropdown?
		if($vars['answer_type'] == 'radio' and !empty($vars['is_dropdown'])) $vars['open_end_display'] = 'dropdown';
		
		$sql = $wpdb->prepare("INSERT INTO ".WATUPRO_QUESTIONS." (exam_id, question, answer_type, 
			cat_id, explain_answer, is_required, sort_order, correct_condition, max_selections, is_inactive, is_survey, 
			elaborate_explanation, open_end_mode, tags, open_end_display, exclude_on_final_screen, hints, 
			compact_format, round_points, importance, calculate_whole, unanswered_penalty, truefalse,
			accept_feedback, feedback_label, reward_only_correct) 
			VALUES(%d, %s, %s, %d, %s, %d, %d, %s, %d, %d, %d, %s, %s, %s, %s, %d, %s, %d, %d, %d, %d, 
				%f, %d, %d, %s, %d)", 
			$vars['quiz'], $vars['content'], $vars['answer_type'], $vars['cat_id'], 
			$vars['explain_answer'], @$vars['is_required'], $sort_order, @$vars['correct_condition'], 
			$vars['max_selections'], @$vars['is_inactive'], @$vars['is_survey'], $elaborate_explanation, 
			@$vars['open_end_mode'], $tags, @$vars['open_end_display'], @$vars['exclude_on_final_screen'], @$vars['hints'], 
			@$vars['compact_format'], @$vars['round_points'], @$vars['importance'], @$vars['calculate_whole'], 
			@$vars['unanswered_penalty'], @$vars['truefalse'], @$vars['accept_feedback'], $vars['feedback_label'], @$vars['reward_only_correct']);
					
		$wpdb->query($sql);
		
		$id = $wpdb->insert_id;
		
		if(watupro_intel()) {
			// extra fields in Intelligence module
			require_once(WATUPRO_PATH."/i/models/question.php");
			WatuPROIQuestion::edit($vars, $id);
		}
		
		return $id;
	}
	
	static function edit($vars, $id) {
		global $wpdb;
		
		$elaborate_explanation = (empty($vars['do_elaborate_explanation']) or empty($vars['elaborate_explanation'])) ? '' : $vars['elaborate_explanation'];
		$tags = "|".str_replace(",", "|", str_replace(", ", "|", $vars['tags']) )."|";
		
		// open end display - accept file?
		if(!empty($vars['accept_file_upload'])) $vars['open_end_display'] .= '|file';
		
		// single-choice dropdown?
		if($vars['answer_type'] == 'radio' and !empty($vars['is_dropdown'])) $vars['open_end_display'] = 'dropdown';
		
		$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_QUESTIONS."
			SET question=%s, answer_type=%s, cat_id=%d, explain_answer=%s, is_required=%d,
			correct_condition=%s, max_selections=%d, is_inactive=%d, is_survey=%d, elaborate_explanation = %s, 
			open_end_mode=%s, tags=%s, open_end_display=%s, exclude_on_final_screen=%d, hints=%s, 
			compact_format=%d, round_points=%d, importance=%d, calculate_whole=%d, unanswered_penalty=%f,
			truefalse=%d, accept_feedback=%d, feedback_label=%s, reward_only_correct=%d 
			WHERE ID=%d", 
			$vars['content'], $vars['answer_type'], $vars['cat_id'], $vars['explain_answer'],
			@$vars['is_required'],	$vars['correct_condition'], $vars['max_selections'], 
			@$vars['is_inactive'], @$vars['is_survey'], $elaborate_explanation, $vars['open_end_mode'], $tags, 
			$vars['open_end_display'], @$vars['exclude_on_final_screen'], @$vars['hints'], 
			@$vars['compact_format'], @$vars['round_points'], @$vars['importance'], 
			@$vars['calculate_whole'], @$vars['unanswered_penalty'], @$vars['truefalse'], 
			@$vars['accept_feedback'], $vars['feedback_label'], @$vars['reward_only_correct'], $id));
						
		if(watupro_intel()) {
			// extra fields in Intelligence module
			require_once(WATUPRO_PATH."/i/models/question.php");
			WatuPROIQuestion::edit($vars, $id);
		}		
	}	
	
	// backward compatibility. In old versions sort order was not given
	// so we'll make sure all questions have correct one when loading the page
	static function fix_sort_order($questions) {
		global $wpdb;
		$questions_table=$wpdb->prefix."watupro_question";
		
		foreach($questions as $cnt=>$question) {
			$cnt++;
			if(@$question->sort_order!=$cnt) {
				$wpdb->query("UPDATE $questions_table SET sort_order=$cnt WHERE ID={$question->ID}");
			}
		}
	}
	
	static function reorder($id, $exam_id, $dir) {
		global $wpdb;
		$questions_table=$wpdb->prefix."watupro_question";
		
		// select question
		$question=$wpdb->get_row($wpdb->prepare("SELECT * FROM $questions_table WHERE ID=%d", $id));
		
		if($dir=="up") {
			$new_order=$question->sort_order-1;
			if($new_order<0) $new_order=0;
			
			// shift others
			$wpdb->query($wpdb->prepare("UPDATE $questions_table SET sort_order=sort_order+1 
			  WHERE ID!=%d AND sort_order=%d AND exam_id=%d", $id, $new_order, $exam_id));
		}
		
		if($dir=="down") {
			$new_order=$question->sort_order+1;			
			
			// shift others
			$wpdb->query($wpdb->prepare("UPDATE $questions_table SET sort_order=sort_order-1 
			  WHERE ID!=%d AND sort_order=%d AND exam_id=%d", $id, $new_order, $exam_id));
		}		
			
		// change this one
		$wpdb->query($wpdb->prepare("UPDATE $questions_table SET sort_order=%d WHERE ID=%d", 
			$new_order, $id));
	}
	
	// to display a question
	function display($ques, $qct, $question_count, $in_progress, $exam = null) {
		global $wpdb, $question_catids;

		// should we display category header? (when quiz is paginated 1 category per page this is handled by watupro_cat_header())
		if(!empty($exam) and $exam->group_by_cat and !in_array($ques->cat_id, $question_catids) 
			and $exam->single_page != WATUPRO_PAGINATE_PAGE_PER_CATEGORY) {			
			echo "<h3>".$ques->cat."</h3>";
			if(!empty($ques->cat_description)) echo "<div>".apply_filters('watupro_content', stripslashes(wpautop($ques->cat_description)))."</div>";
			$question_catids[] = $ques->cat_id;
		}		
				
		// fill in_progress once to avoid running multiple qiueries
		if(!empty($in_progress)) {
	  		// check if we already fetched the answers. if not, fetch
	  		// this is to avoid queries on every question
	  		WTPQuestion :: $in_progress = $in_progress;
	  		if(empty($this->inprogress_details)) {
	  			$answers=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".WATUPRO_STUDENT_ANSWERS." 
	  				WHERE taking_id=%d AND exam_id=%d", $in_progress->ID, $in_progress->exam_id));
	 			 				
	  			$this->inprogress_details = $this->inprogress_hints = $this->inprogress_snapshots = array();
	  			$this->num_hints_total = 0;
	  			foreach($answers as $answer) {
	  					$this->inprogress_details[$answer->question_id]=unserialize($answer->answer);
	  					$this->inprogress_hints[$answer->question_id]=stripslashes($answer->hints_used);
	  					$this->inprogress_snapshots[$answer->question_id]=stripslashes($answer->snapshot);
	  					if(!empty($answer->feedback)) $this->inprogress_feedbacks[$answer->question_id]= $answer->feedback;
	  					$this->num_hints_total += $answer->num_hints_used;
	  			}
	  		}
	  }   	
	  
	  // if there is snapshot, means we have called 'see answer'. In this case we should make the div below invisible
	  $nodisplay = '';
	  if(!empty($this->inprogress_snapshots[$ques->ID]) and $exam->live_result) {
	  	  $nodisplay = 'style="display:none;"';
	  }
	  
	  $compact_class = $ques->compact_format ? "watupro-compact" : "";
	  $question_number = empty(self :: $advanced_settings['dont_display_question_numbers']) ? "<span class='watupro_num'>Question $qct </span>"  : '';
		
		echo "<div id='questionWrap-$question_count' $nodisplay class='$compact_class'>
			<div class='question-content' ".@$display_style.">";
			
		// replace {{{ID}}} if any
		$ques->question = str_replace('{{{ID}}}', $ques->ID, $ques->question);	
		
		if(watupro_intel() and ($ques->answer_type=='gaps' or $ques->answer_type=='sort' or $ques->answer_type == 'matrix')) {
			require_once(WATUPRO_PATH."/i/models/question.php");
			WatuPROIQuestion::display($ques, $qct, $question_count, @$this->inprogress_details);
		}
		else echo wpautop( self :: flag_review($ques, $qct) . $question_number . stripslashes($ques->question));
		
 		echo "<input type='hidden' name='question_id[]' id='qID_{$question_count}' value='{$ques->ID}' />";
 		echo "<input type='hidden' id='answerType{$ques->ID}' value='{$ques->answer_type}'>";
 		if($ques->is_required) echo "<input type='hidden' id='watupro-required-question-".$ques->ID."'>";
 		
 		if(!empty($exam->question_hints) ) $this->display_hints($ques, $in_progress);
 		
 		if($ques->answer_type != 'sort') echo  "<!-- end question-content--></div>"; // end question-content
 		
 		$this->display_choices($ques, $in_progress);
 		
 		// accept feedback?
 		if($ques->accept_feedback) {
 			$feedback = empty($this->inprogress_feedbacks[$ques->ID]) ? '' : stripslashes($this->inprogress_feedbacks[$ques->ID]);
 			echo "<p>".stripslashes($ques->feedback_label)."<br>
 			<textarea name='feedback-{$ques->ID}' rows='3' cols='30' class='watupro-user-feedback' id='watuproUserFeedback{$ques->ID}'>$feedback</textarea></p>";
		}
 		
 		echo '<!-- end questionWrap--></div>'; // end questionWrap
	}
		
	// display the radio, checkbox or text area for answering a question
    // also take care for pre-selecting anything in case we are continuing on unfinished exam
  function display_choices($ques, $in_progress=null) {
		global $wpdb, $answer_display;
		  
  	  $ans_type = $ques->answer_type;
  	  $answer_class = '';
  	  $enumerator = self :: define_enumerator();
  	   
      switch($ans_type) {
      	case 'textarea':
      	 echo "<div class='question-choices'>";
      	 // open end question
      	 $value = (!empty($this->inprogress_details[$ques->ID][0])) ? stripslashes($this->inprogress_details[$ques->ID][0]) : ""; 
      	 
      	 // open_end_display may also contain "file" upload allowance like this: medium|file
      	 $allow_file_upload = false;
			 if(strstr($ques->open_end_display, '|')) {
			 	  list($ques->open_end_display) = explode("|",   $ques->open_end_display);
			 	  if(!empty($this->exam->no_ajax)) $allow_file_upload = true;
			 }      	 
      	 
      	 switch($ques->open_end_display) {
      	 	 case 'text':      	 	 	
      	 	 	echo "<p><input type='text' name='answer-{$ques->ID}[]' id='textarea_q_{$ques->ID}' class='watupro-text' value=\"$value\" size='60'></p>";
      	 	 break;	
      	 	 case 'medium':
      	 	 case 'large':
      	 	 default:
      	 	 	 $class = (empty($ques->open_end_display) or $ques->open_end_display == 'medium') ? 'watupro-textarea-medium' : 'watupro-textarea-large';
      	 	 	  echo "<p><textarea name='answer-{$ques->ID}[]' id='textarea_q_{$ques->ID}' class='$class'>$value</textarea></p>";
      	 	 	 // echo "<p>".wp_editor($value, 'textarea_q_'.$ques->ID, array("textarea_name"=>'answer-'.$ques->ID.'[]'))."</p>";
      	 	 break;
      	 }        

			 // output file upload?
			 if($allow_file_upload) {
			 	echo "<p>".__('Upload file:', 'watupro')." <input type='file' name='file-answer-{$ques->ID}'></p>";
			 }    	 
      	 
      	 echo "<!-- end question-choices--></div>"; 
      	break;
      	case 'radio':
      	case 'checkbox':
      		echo "<div class='question-choices'>";
				
				// radios allow drop-down display. This is stored in the "open_end_display" field
				if($ques->open_end_display == 'dropdown') echo "<select name='answer-{$ques->ID}[]'>";      		
      		
      		// radio and checkbox
      		foreach ($ques->q_answers as $ans_cnt => $ans) {      			
	        		if($answer_display == 2) {
	        			$answer_class = 'wrong-answer-label';
	        			if($ans->correct) $answer_class = 'correct-answer-label';
	        		}
	        		
	        		if($ques->truefalse and $ans_cnt >= 2) break;
	        		
	        		$checked="";
					if(!empty($this->inprogress_details[$ques->ID])) {
							if(is_array($this->inprogress_details[$ques->ID])) {
								if(in_array($ans->ID, $this->inprogress_details[$ques->ID])) $checked=" checked ";
							}
							else {
								if($this->inprogress_details[$ques->ID]==$ans->ID) $checked=" checked ";
							}
					}	        		
	        		
	        		// max selection limit?
	        		$maxsel_js = '';
	        		if($ques->answer_type == 'checkbox' and $ques->max_selections > 0) {
	        			$maxsel_js = "onclick='return WatuPRO.maxSelections(".$ques->ID.",".$ques->max_selections.", this)';";
	        		}
	        		
	        		if($ques->open_end_display == 'dropdown') {
	        			if(!empty($checked)) $checked = 'selected';
	        			echo "<option value='{$ans->ID}' $checked class='answer  $answer_class answerof-{$ques->ID}'>".stripslashes($ans->answer)."</option>";
	        		}
	        		else { // not dropdown
		        		if($enumerator) { 
		        			$enumerator_visible = $enumerator.'. ';
		        			$enumerator++;
		        		} else $enumerator_visible = '';
		        		echo "<div class='watupro-question-choice'>$enumerator_visible<input type='$ans_type' name='answer-{$ques->ID}[]' id='answer-id-{$ans->ID}' class='answer  $answer_class answerof-{$ques->ID}' value='{$ans->ID}' $checked $maxsel_js/>";
		        		echo "&nbsp;<label for='answer-id-{$ans->ID}' id='answer-label-{$ans->ID}' class='$answer_class answer'><span>" . stripslashes($ans->answer) . "</span></label></div>";
		        } // end if NOT dropdown
        	 } // end foreach answer   
        	 
        	 if($ques->open_end_display == 'dropdown') echo "</select>";
        	 echo "<!-- end question-choices--></div>";
      	break;
      }      
    }
    
    // a small helper that will cleanup markup that shows correct/incorrect info
    // so unresolved questions can be displayed
    function display_unresolved($output) {
    	$output = WatuPRO::cleanup($output, 'web');
    	
    	// now remove correct-answer style
    	$output = str_replace('correct-answer','',$output);
    	$output = str_replace('user-answer-unrevealed','user-answer',$output); // do it back & forth to avoid nasty bug
    	$output = str_replace('user-answer','user-answer-unrevealed',$output);
    	
    	// remove hardcoded correct/incorrect images if any
    	// (for example we may have these in fill the gaps questions)
    	$output = str_replace('<img src="'.WATUPRO_URL.'/correct.png" hspace="5">', '', $output);
    	$output = str_replace('<img src="'.WATUPRO_URL.'/wrong.png" hspace="5">', '', $output);
    	
    	return $output;	
    }
    
    // figure out if a question is correctly answered accordingly to the requirements
    // $answer is single value or array depending on the question type
    // $choices are the possible choices of this question
    // $user_grade_ids is passed by referrence and used only in personality quizzes
    // returns array($points, $is_correct)
    static function calc_answer($question, $answer, $choices = -1, &$user_grade_ids = null) {    	
		// points for unanswered question
		$empty_points = ($question->unanswered_penalty > 0 and !$question->is_survey) ? (0 - $question->unanswered_penalty) : 0;    
    
    	// negative points and unanswered questions are always incorrect
    	// but let intelligence module take care for gaps and matrix
    	if($question->answer_type != 'gaps' and $question->answer_type != 'matrix') {
    		if(empty($answer) or $question->is_survey) return array($empty_points, 0);
    	}
    	    	
    	// when textareas have no possible answers, they are always correct when answered and incorrect when not answered
    	if($question->answer_type == 'textarea' and !sizeof($choices)) {
    		if(!empty($answer[0])) return array(0, 1);
    		else return array($empty_points, 0); // unanswered
    	} 
    	
    	global $wpdb;
    	
    	// when choices is -1 means we have not passed them and we have to select them
    	if($choices == -1) {
    		$choices = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".WATUPRO_ANSWERS." 
    			WHERE question_id=%d", $question->ID));
    	}
    	
    	// single-answer questions
    	if($question->answer_type=='radio') {
    		// answers are given however. We need to figure out whether the answer is correct
    		$is_correct = $points = 0;    		
    		$answer = $answer[0];
			if(empty($answer)) return array($empty_points, 0); // unanswered and incorrect    		
    		
    		foreach($choices as $choice) {
    			 if($choice->ID == trim($answer)) {
    			 		$points = $choice->point;
    			 		if($choice->correct) $is_correct = 1;    	
    			 		if(!empty($choice->grade_id)) $user_grade_ids[] = $choice->grade_id;		 		
    			 		break;
    			 }	
			}		
			if($question->round_points) $points = round($points, 1);		
			return array($points, $is_correct);				
    	}
 
 		// multiple answer and open-end			   	
		if($question->answer_type == 'checkbox' or $question->answer_type == 'textarea') {			
			// figure out maximum points and calculate received points
			$points = $max_points = $is_correct = 0;			
			
			foreach($choices as $choice) {
				if($choice->point > 0) $max_points += $choice->point;
				list($p) = self :: evaluate_choice($question, $answer, $choice, $user_grade_ids);
				$points += $p;				
			}
			
			if(empty($question->correct_condition) or $question->correct_condition == 'any') {
				 if($points > 0) $is_correct = 1;
			}
			else {
				// max points required
				if($points >= $max_points) $is_correct = 1;
			}
			if($question->round_points) $points = round($points, 1);
			if(empty($answer)) $points = $empty_points; // unanswered question
			return array($points, $is_correct);
		}
		
		// fill the gaps and sortable
		if(watupro_intel() and ($question->answer_type == 'gaps' 
			or $question->answer_type == 'sort' or $question->answer_type == 'matrix')) {
			$is_correct = 0;
			list($points, $html, $max_points) = WatuPROIQuestion::process($question, $answer);
			
			if(empty($question->correct_condition) or $question->correct_condition == 'any') {
				 if($points > 0) $is_correct = 1;
			}
			else {
				// max points required
				if($points >= $max_points) $is_correct = 1;
			}
			
			if($question->round_points) $points = round($points, 1);
			if(empty($answer) and empty($points)) $points = $empty_points; // unanswered question
			return array($points, $is_correct);
		}
		
		// return just in case
		return array(0, 0);   
    }
    
    // calculate maximum points that can be achieved by the question   
    static function max_points($question) {
    	 $points = 0;
    	 
    	 if($question->is_survey) return 0;
    	 
    	 // sorting and fill the gaps questions
    	 if($question->answer_type == 'gaps') {
    	 	 $matches = array();
			 preg_match_all("/{{{([^}}}])*}}}/", $question->question, $matches);
			
			 $num_gaps = sizeof($matches[0]);
			 $points = $num_gaps * $question->correct_gap_points;
			 if($question->round_points) $points = round($points, 1);			 
			 return $points;
    	 }
    	 
    	 if($question->answer_type == 'sort') {
    	 	 if($question->calculate_whole) return $question->correct_gap_points;			    	 	
    	 	
    	 	 $sort_values = explode("\n",trim(stripslashes($question->sorting_answers)));
    	 	 
    	 	 $points = sizeof($sort_values) * $question->correct_gap_points;
    	 	 if($question->round_points) $points = round($points, 1);			
    	 	 return $points;
    	 }
    	 
    	  if($question->answer_type == 'matrix') {
    	 	 if($question->calculate_whole) return $question->correct_gap_points;			    	 	
    	 	
    	 	 $points = sizeof($question->q_answers) * $question->correct_gap_points;
    	 	 if($question->round_points) $points = round($points, 1);			
    	 	 return $points;
    	 }
    	 
    	 // thereon further we have to have possible answers, otherwise points are zero
    	 if(empty($question->q_answers) or !is_array($question->q_answers)) return 0;
    	 
    	 // for now cover only the basic question types - single answer,multiple answer, open-end
    	 // take into account the possible limit of number of selections for multiple-choice (max_selections)
    	 // for 'open-end' questions and radios max_selction will be set to 1
    	 if($question->answer_type == 'radio') $question->max_selections = 1;

		 // we have to reorder choices so the max point ones are on top
		 $qanswers = $question->q_answers;
		 uasort($qanswers, array(__CLASS__, 'max_points_reorder'));	 
    	 
    	 $num_calculated = 0;    	 
    	 foreach($qanswers as $choice) {
    	 	 if($choice->point <= 0) continue; // skip these with no points or negative points (wee ned max!)
    	 	 if($question->max_selections > 0 and $num_calculated >= $question->max_selections) break; // no more selections than the max allowed
    	 	 
    	 	 $points += $choice->point;
    	 	 $num_calculated++;
    	 }
    	 if($question->round_points) $points = round($points, 1);		
    	 return $points;
	 } // end calc_answer
	 
	 // called by max_points to reorder the answers in a way that lets the best ones on top 
	 // so we can extract maximum points properly
	 static function max_points_reorder($a, $b) {
	 	 if ($a->point == $b->point) {
        return 0;
	    }
	    return ($a->point > $b->point) ? -1 : 1;
	 }
    
    // select all questions for an exam
    static function select_all($exam) {
    	global $wpdb;
    		// if specific question IDs are passed in the shortcode, disregard anything below and select those questions
    	   // this is both to allow using same exam with different specified questions and to allow the "user selects" addon    	   
    	   if(!empty($exam->passed_question_ids)) return self :: select_specified($exam);
    	
    		// order by
			$ob=($exam->randomize_questions==1 or $exam->randomize_questions==2 or $exam->pull_random) ? "RAND()":"sort_order,ID";
			
			if($exam->random_per_category and $ob == 'RAND()') $ob = "cat, RAND()";			
			
			$limit_sql="";
			if($exam->pull_random and !$exam->random_per_category) {
				$limit_sql=" LIMIT ".$exam->pull_random;
			}
			
			// when we are pulling random questions from all we need to make sure important questions are included
			if($exam->pull_random) {
				$ob = str_replace("RAND()", "importance DESC, RAND()", $ob);
			}
			
			$q_exam_id = (watupro_intel() and $exam->reuse_questions_from) ? $exam->reuse_questions_from : $exam->ID;
			
			$questions = $wpdb->get_results("SELECT tQ.*, tC.name as cat, tC.description as cat_description
			FROM ".WATUPRO_QUESTIONS." tQ LEFT JOIN ".WATUPRO_QCATS." tC
			ON tC.ID=tQ.cat_id
			WHERE tQ.exam_id IN ($q_exam_id) AND tQ.is_inactive=0
			ORDER BY $ob $limit_sql");
						
			return $questions;
    }
    
    // select specified questions
    static function select_specified($exam) {    	
    	global $wpdb;
    	
    	// extract the question IDs making sure there are no empty etc
		$exam->passed_question_ids = str_replace(' ', '', trim($exam->passed_question_ids));
    	$question_ids = explode(",", $exam->passed_question_ids);
    	$question_ids = array_filter($question_ids);
    	    	
    	$q_exam_id = (watupro_intel() and $exam->reuse_questions_from) ? $exam->reuse_questions_from : $exam->ID;
    	
    	// now select
    	$questions = $wpdb->get_results("SELECT tQ.*, tC.name as cat, tC.description as cat_description
			FROM ".WATUPRO_QUESTIONS." tQ LEFT JOIN ".WATUPRO_QCATS." tC
			ON tC.ID=tQ.cat_id WHERE tQ.exam_id IN ($q_exam_id) AND tQ.is_inactive=0
			AND tQ.ID IN (".implode(',', $question_ids).")");
		
		// now reorder accordingly to the passed order
		$ordered_questions = array();
		foreach($question_ids as $qid) {
			foreach($questions as $question) {
				if($question->ID == $qid) $ordered_questions[] = $question;
			}
		} 
		
		return $ordered_questions;	
    }
    
    // processes a question when submitting exam or toggling answer. Used in submit_exam and the toggle result button 
    function process($_watu, $qct, $question_content, $ques, $ansArr, $correct, $points) {
			    	
		$original_answer=""; // this var is used only for textareas    	
		$answer_text=""; // answers as text
		$unresolved_text = "";
		$compact_class = $ques->compact_format ? ' watupro-compact ' : '';
		$question_number = empty(self :: $advanced_settings['dont_display_question_numbers']) ? "<span class='watupro_num'>Question $qct </span>"  : '';
		
		$enumerator = self :: define_enumerator();
		
		if($ques->answer_type == 'gaps') {			
			// gaps are displayed in different way to avoid repeating the question
			$current_text = "<div class='show-question [[watupro-resolvedclass]]'><div class='show-question-content'>" . $question_number;
		}	
    	else {
	    	$current_text = "<div class='show-question [[watupro-resolvedclass]]".$compact_class."'><div class='show-question-content'>"
	    		.$question_number . stripslashes($question_content) . "</div>\n";	
	    	$current_text .= "<div class='show-question-choices'>";
			$current_text .= "<ul>";
		}			        
		
		// replace the {{{ID}}} mask
		$current_text = str_replace('{{{ID}}}', $ques->ID, $current_text);

	   $class = 'answer';
	   $any_answers=false; // this is for textareas -is there any answer provided at all?
		
	   foreach ($ques->q_answers as $ans) {
	   	if($ques->answer_type == 'matrix') continue;
	  		$user_answer_class = ($ques->is_survey or $_watu->this_quiz->is_personality_quiz) ? 'user-answer-unrevealed' : 'user-answer';
			$class = 'answer';			
			if( in_array($ans->ID , $ansArr) ) { $class .= ' '.$user_answer_class; }
			if($ans->correct == 1 and $ques->answer_type!='textarea' and !$ques->is_survey) $class .= ' correct-answer';
			
			if($enumerator) { 
     			$enumerator_visible = $enumerator.'. ';
     			$enumerator++;
     		} else $enumerator_visible = '';
            
        if($ques->answer_type=='textarea'):
             // textarea answers have only 1 element. Make comparison case insensitive
				 $original_answer=@$ansArr[0];
				 $ansArr[0]=strtolower(strip_tags(trim($ansArr[0])));
             $compare=strtolower($ans->answer);
             if(!empty($compare)): $any_answers=true; endif;
        else:
             $compare=$ans->ID;
             $current_text .= "<li class='$class'><span class='answer'><!--WATUEMAIL".$class."WATUEMAIL-->" . stripslashes($enumerator_visible.$ans->answer) . "</span></li>\n";
        endif;    
		} // end foreach choice;
		
     // open end will be displayed here
     if($ques->answer_type=='textarea') {
     		 $user_answer_class = $ques->is_survey ? 'user-answer-unrevealed' : 'user-answer';
			
			 // repeat this line in case there were no answers to compare	
			 $answer_text = empty($original_answer) ? $ansArr[0] : $original_answer;
			 $ansArr[0] = strtolower($ansArr[0]);
			 
          $class .= ' '. $user_answer_class;
          if($correct) $class .= ' correct-answer';
          $current_text .= "<li class='$class'><span class='answer'>" . nl2br(stripslashes($answer_text)) . "</span></li>\n";
          
          // uploaded file?
          if(!empty($_FILES['file-answer-'.$ques->ID]['tmp_name'])) $current_text .= '<!--watupro-uploaded-file-'.$ques->ID.'-->';
     }
     
     if(($ques->answer_type=='gaps' or $ques->answer_type=='sort' or $ques->answer_type=='matrix') and watupro_intel()) {     		
     		list($points, $answer_text) = WatuPROIQuestion::process($ques, $ansArr);
     		$current_text .= $answer_text;
     }
     
     if(empty($answer_text)) $answer_text=$_watu->answer_text($ques->q_answers, $ansArr);
  		            
  		if($ques->answer_type != 'gaps') $current_text .= "</ul>"; // close the ul for answers
  		if(empty($_POST["answer-" . $ques->ID])) $current_text .= "<p class='unanswered'>" . __('Question was not answered', 'watupro') . "</p>";
  		
  		if(!$correct) $unresolved_text = $this->display_unresolved($current_text)."</div>";
  
		// close question-choices
		$current_text .= "</div>";  
		$unresolved_text .= "</div>";
		
		// if there is user's feedback, display it too
		if($ques->accept_feedback and !empty($_POST['feedback-'.$ques->ID])) {
			$current_text .= "<p><b>".stripslashes($ques->feedback_label)."</b><br>".stripslashes($_POST['feedback-'.$ques->ID])."</p>";
		}
  
		// if explain_answer, display it		
		$current_text .= $this->answer_feedback($ques, $correct, $ansArr, $points); 
    
  		$current_text .= "</div>";
  		$current_text = wpautop($current_text);
  		
  		// apply filter to allow 3rd party changes.
  		$current_text = apply_filters( 'watu_filter_current_question_text', $current_text, $qct, $question_content, $correct );
  		
  		// if question is survey, unresolved should be empty
  		if($ques->is_survey) $unresolved_text = '';
  		
  		return array($answer_text, $current_text, $unresolved_text); 
    } // end process()
    
    // displays the optional answerfeedback
    function answer_feedback($question, $is_correct, $ansArr, $points) {    	
    	$feedback = "";
    	$feedback_contents = stripslashes($question->explain_answer);
		if(empty($feedback_contents)) return "";
		
    	if(!empty($question->explain_answer)) {
    		if(!empty($question->elaborate_explanation)) {
				if($question->elaborate_explanation == 'boolean') {
	    			$parts = explode("{{{split}}}", $feedback_contents);
	    			if($is_correct and !empty($parts[0])) $feedback .= "<div class='watupro-main-feedback feedback-correct'>".$parts[0]."</div>";        			
	    			elseif(!empty($parts[1])) $feedback .= "<div class='watupro-main-feedback feedback-incorrect'>".$parts[1]."</div>";
	    		}
	    		
	    		if($question->elaborate_explanation == 'exact') {
	    			foreach ($question->q_answers as $ans) {
	    				if(in_array($ans->ID , $ansArr)) $feedback .= "<div class='watupro-choice-feedback'>".stripslashes($ans->explanation)."</div>"; 
	    			}
	    		}	
    		}
    		else  $feedback .= "<div class='watupro-main-feedback'>".$feedback_contents."</div>";    
    	}
    	
    	if($question->round_points) $points = round($points, 1);		
    	$points = number_format($points, 2);
    	$feedback = str_replace("{{{points}}}", $points, $feedback);    
    	return wpautop($feedback);
    } // end feedback   
    
    // evaluates if a choice is within the user answer(s) and returns the points
    // used by self -> calc_answer method for multiple choice and textarea questions
    // @param true_if_selected boolean - when true, we'll return array of points and boolean showing whether the choice matches user's answer
    // $user_grade_ids - passed by referrence and used in personality quzzes, the same global as in calc_answers
    static function evaluate_choice($question, $answer, $choice, &$user_grade_ids = null) {    	
    	$points = 0;
    	$is_selected = false;
    	
    	if($question->answer_type == 'checkbox') {
		 	foreach($answer as $part) {
				 if($part == $choice->ID) { 
				 	$points += $choice->point; 
				 	$is_selected = true;
				 	if(!empty($choice->grade_id)) $user_grade_ids[] = $choice->grade_id; 
				 }
			}
		}  // end if checkbox
		
		if($question->answer_type == 'textarea') {
			$answer = trim(strtolower(@$answer[0])); // user answer			
			$compare = trim(strtolower($choice->answer)); // the choice given
			
			if(empty($compare) or empty($answer)) return array(0, false);
    			 
 			switch(@$question->open_end_mode) {
 			 	 case 'contained': // the choice is contained in the user answer 			 	 
 			 	  	 if(strstr($answer, $compare)) { 
 			 	  	 	$points = $choice->point; 
 			 	  	 	$is_selected = true;
 			 	  	 	if(!empty($choice->grade_id)) $user_grade_ids[] = $choice->grade_id;
 			 	  	 }
			 	 break;    			 	 
			 
			 	 case 'contains': // the given choice contains the user answer
 			 	 	 if(strstr($compare, $answer)) {
 			 	 	 	$points = $choice->point; 
 			 	 	 	$is_selected = true;
 			 	 	 	if(!empty($choice->grade_id)) $user_grade_ids[] = $choice->grade_id;
 			 	 	 }
			 	 break;
 			 	 
 			 	 // correct in both cases (contains, contained)
 			 	 case 'loose': 			 	 		
 			 	 	 if(strstr($answer, $compare) or strstr($compare, $answer)) {
 			 	 	 		$points = $choice->point; 
 			 	 	 		$is_selected = true;
 			 	 	 		if(!empty($choice->grade_id)) $user_grade_ids[] = $choice->grade_id;
 			 	 	 	}
 			 	 break;
 			 	 	
 			 	 case 'exact': 
 			 	 default: // defaults to strict because this is how it used to be
		 	 	   if($compare == $answer) {
		 	 	   	$points = $choice->point; 
		 	 	   	$is_selected = true;
		 	 	   	if(!empty($choice->grade_id)) $user_grade_ids[] = $choice->grade_id;
		 	 	   }
 			 	 break;
 			}			
		} // end if textarea
		
		return array($points, $is_selected);
    } // end evaluate choice
    
    // display question hints
    // using $in_progress we must display the hints that were shown already
    function display_hints($question, $in_progress = null) {
    	if(empty($question->hints)) return "";
    	$get_hints_link = true;
    	if(empty($this->exam->question_hints)) return "";
    	list($per_quiz, $per_question) = explode("|", $this->exam->question_hints);
    	
		$current_hints = empty($this->inprogress_hints[$question->ID]) ? "" : $this->inprogress_hints[$question->ID];
		
		if($in_progress and $per_quiz and $this->num_hints_total >= $per_quiz) $get_hints_link = false;
		
		// now check per question
		if($in_progress and $per_question and $get_hints_link) {
			$num = sizeof(explode('watupro-hint', $current_hints)) - 1;
			if($num >= $per_question) $get_hints_link = false;
		}
		    	    	
    	// wrap div
    	echo "<div class='watupro-question-hints'>";
		if($get_hints_link) echo "<p id='questionHintLink".$question->ID."'><a href='#' onclick='WatuPRO.getHints(".$question->ID.");return false;'>".__('[Get Hints]', 'watupro')."</a></p>";
		echo "<div id='questionHints".$question->ID."'>".$current_hints."</div>";
    	echo "</div>";
    }
    
    // define enumeration for answers
    static function define_enumerator() {
    	  $enumerate = empty(self :: $advanced_settings['enumerate_choices']) ? false : self :: $advanced_settings['enumerate_choices'];
	  	  // $enumerate = 'cap_letter'; // TEMP!!!
	  	  $enumerator = '';
	  	  if($enumerate) {
	  	  		switch($enumerate) {
	  	  			case 'cap_letter': $enumerator = 'A'; break;
					case 'small_letter': $enumerator = 'a'; break;
					case 'number': $enumerator = '1'; break;
					default: $enumerator = ''; break;
	  	  		}
	  	  }
	  	  
	  	  return $enumerator;
	 }
	 
	 // mark for review icon
	 static function flag_review($question, $qct) {
	 	$allow = empty(self :: $advanced_settings['flag_for_review']) ? false : self :: $advanced_settings['flag_for_review'];
	 	if(!$allow) return '';
	 	
	 	// in progress?
	 	$marked_class = '';
		$filename = 'mark-review.png';
	 	if(!empty(self :: $in_progress)) {
	 		$marked_for_review = self :: $in_progress->marked_for_review;
	 		$marked_for_review = unserialize($marked_for_review);
	 		
	 		if(!empty($marked_for_review['question_ids']) and is_array($marked_for_review['question_ids']) 
	 			and in_array($question->ID, $marked_for_review['question_ids'])) {
	 			$marked_class = ' marked ';
	 			$filename = 'unmark-review.png';
	 		}
	 	}
	 	
	 	// flag for review allowed
	 	// for now let's not worry about "in progress"
	 	return '<img src="'.WATUPRO_URL.'img/'.$filename.'" class="'.$marked_class.'watupro-mark-review question-id-'.$question->ID.' question-cnt-'.$qct.'" alt=""  title="'.__('Flag for review', 'watupro').'" id="watuproMarkHandler'.$question->ID.'">';
	 }
}