<?php
// questions controller. For now keep mix with procedural functions, but to-do is to move them all in the class
class WatuPROQuestions {
	static function mark_review() {
		global $wpdb;
		$_watu = new WatuPRO();
		
		// this will only happen for logged in users
		if(!is_user_logged_in()) return false;
				
		$taking_id = $_watu->add_taking($_POST['exam_id'],1);
				
		// select current data if any
		$marked_for_review = $wpdb->get_var($wpdb->prepare("SELECT marked_for_review FROM ".WATUPRO_TAKEN_EXAMS."
			WHERE ID=%d", $taking_id));
			
		if(empty($marked_for_review)) $marked_for_review = array("question_ids"=>array(), "question_nums"=>array());
		else $marked_for_review = unserialize($marked_for_review);
		
		if($_POST['act'] == 'mark') {
			$marked_for_review['question_ids'][] = $_POST['question_id'];
			$marked_for_review['question_nums'][] = $_POST['question_num']; 
		}
		else {
			// unmark
			foreach($marked_for_review['question_ids'] as $cnt=>$id) {
				if($id == $_POST['question_id']) unset($marked_for_review['question_ids'][$cnt]);
			}
			
			foreach($marked_for_review['question_nums'] as $cnt=>$num) {
				if($num == $_POST['question_num']) unset($marked_for_review['question_nums'][$cnt]);
			}
		}	
		
		// now save
		$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_TAKEN_EXAMS." SET marked_for_review=%s WHERE ID=%d",
			serialize($marked_for_review), $taking_id));
	} // end mark_review
}

// add/edit question 
function watupro_question() {
	global $wpdb, $user_ID;
	
	// check access
	$multiuser_access = 'all';
	if(watupro_intel()) $multiuser_access = WatuPROIMultiUser::check_access('exams_access');
	if($multiuser_access == 'own') {
			// make sure this is my quiz
			$quiz = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", $_GET['quiz']));
			if($quiz->editor_id != $user_ID) wp_die(__('You can only manage the questions on your own quizzes.','watupro'));
	}
	
	$action = 'new';
	if($_REQUEST['action'] == 'edit') $action = 'edit';
	
	$question= $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_QUESTIONS." WHERE ID=%d", @$_GET['question']));
	$all_answers = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".WATUPRO_ANSWERS." WHERE question_id=%d ORDER BY sort_order", @$_GET['question']));
	$ans_type = ($action =='new') ? get_option('watupro_answer_type'): $question->answer_type;
	$answer_count = 4;
	if($action == 'edit' and $answer_count < count($all_answers)) $answer_count = count($all_answers) ;	
	// true false is always 2
	if(!empty($question->ID) and $question->answer_type == 'radio' and $question->truefalse) { $truefalse = true; $answer_count = 2;}
	
	// select question categories
	$qcats=$wpdb->get_results("SELECT * FROM ".WATUPRO_QCATS." ORDER BY name");
	
	// select exam	
	$exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", $_GET['quiz']));
	
	if(watupro_intel() and $exam->is_personality_quiz) $grades = WTPGrade :: get_grades($exam);
	if(watupro_intel() and $ans_type == 'matrix') {
		$matches = array();
		foreach($all_answers as $answer) {
			list($left, $right) = explode('{{{split}}}', $answer->answer);
			$matches[] = array("id"=>$answer->ID, "left"=>$left, "right"=>$right);
		}
		$answer_count = 0;
	}
	
	if(@file_exists(get_stylesheet_directory().'/watupro/question_form.php')) require get_stylesheet_directory().'/watupro/question_form.php';
	else require WATUPRO_PATH."/views/question_form.php";
}

function watupro_questions() {
	global $wpdb, $user_ID;
	
	$multiuser_access = 'all';
	if(watupro_intel()) $multiuser_access = WatuPROIMultiUser::check_access('exams_access');
	if($multiuser_access == 'own') {
			// make sure this is my quiz
			$quiz = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", $_GET['quiz']));
			if($quiz->editor_id != $user_ID) wp_die(__('You can only manage the questions on your own quizzes.','watupro'));
	}
	
	if(!empty($_GET['export'])) watupro_export_questions();
	if(!empty($_POST['watupro_import'])) watupro_import_questions();
	
	$action = 'new';
	if(!empty($_GET['action']) and $_GET['action'] == 'edit') $action = 'edit';
	
	if(isset($_POST['ok'])) {
		// add new category?
		if(!empty($_POST['new_cat'])) {
			$wpdb->query($wpdb->prepare("INSERT INTO ".WATUPRO_QCATS." (name, editor_id) VALUES (%s, %d) ", $_POST['new_cat'], $user_ID));
			$_POST['cat_id'] = $wpdb->insert_id;
		}	
		
		// only 'radio' questuons can be truefalse
		if($_POST['answer_type'] != 'radio') $_POST['truefalse'] = 0;
		
		if($action == 'edit') { 
			WTPQuestion::edit($_POST, $_POST['question']);			
		} 
		else  {
			$_POST['question'] = WTPQuestion::add($_POST);
			$action='edit';
		}
		
		// when we have selected "exact" feedback we need to match feedback/explanation to answers
		$explanations = array();
		if(!empty($_POST['explain_answer']) and !empty($_POST['do_elaborate_explanation']) and @$_POST['elaborate_explanation'] == 'exact') {
			$explanations = explode("{{{split}}}", $_POST['explain_answer']);
		}
		
		// adding answers
		$question_id = $_POST['question'];
		if($question_id > 0) {
			// select old answers
			$old_answers = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".WATUPRO_ANSWERS." WHERE question_id=%d ORDER BY ID", $question_id));	
			
			// handle matrix
			if(watupro_intel() and $_POST['answer_type'] == 'matrix') WatuPROIQuestion :: save_matrix($question_id, $old_answers);		
			
			// the $counter will skip over empty answers, $sort_order_counter will track the provided answers order.
			$counter = 1;
			$sort_order_counter = 1;
			$correctArry = @$_POST['correct_answer'];
			$pointArry = @$_POST['point'];
						
			if(!empty($_POST['answer']) and is_array($_POST['answer'])) {
				foreach ($_POST['answer'] as $key => $answer_text) {
					$correct=0;
					if( @in_array($counter, $correctArry) ) $correct=1;
					$point = $pointArry[$key];
					$grade_id_key = $key + 1;
					
					// correct answers must always have positive number of points
					if($correct and $point <=0) $point = 1;
					
					// actually add or save the answer
					if($answer_text!=="") {
						if(empty($point)) $point = 0;
	
						// is there old answer?					
						if(isset($old_answers[$counter-1])) {
							$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_ANSWERS." SET 
								answer=%s, correct=%s, point=%s, sort_order=%d, explanation=%s, grade_id=%s
								WHERE ID=%d",
								$answer_text, $correct, $point, $sort_order_counter, @$explanations[$key], @implode('|',$_POST['grade_id_'.$grade_id_key]), $old_answers[$counter-1]->ID));						
						} 
						else { 
							$wpdb->query($wpdb->prepare("INSERT INTO ".WATUPRO_ANSWERS." (question_id, answer, correct, point, sort_order, explanation, grade_id)
								VALUES(%d, %s, %s, %s, %d, %s, %s)", $question_id, $answer_text, $correct, $point, $sort_order_counter, 
									@$explanations[$key], @implode('|',$_POST['grade_id_'.$grade_id_key])));
						}
						$sort_order_counter++;
						// for truefalse questions don't save more than 2 answers
						if(!empty($_POST['truefalse']) and $sort_order_counter > 2) break; // break the foreach						
					}
					$counter++;
				} // end foreach $_POST['answer']
			
				// any old answers to cleanup?
				if($sort_order_counter <= sizeof($old_answers)) {				
					$answers_to_del = array_slice($old_answers, $sort_order_counter-1);
					
					$ans_del_ids = array(0);
					foreach($answers_to_del as $a) $ans_del_ids[] = $a->ID;
					$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_ANSWERS." WHERE ID IN (".implode(',', $ans_del_ids).") AND question_id=%d", $question_id));
				}
			} // end if $_POST['answer']
			
			do_action('watupro_saved_question', $question_id);
			
			// should I redirect to edit a choice in rich text editor?
			if(!empty($_POST['goto_rich_text'])) {
				watupro_redirect("admin.php?page=watupro_edit_choice&id=".$_POST['goto_rich_text']);
			}
		} // end if $question_id
	} // end adding/saving question
	
	// delete question
	if(!empty($_GET['action']) and $_GET['action'] == 'delete') {
		$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_ANSWERS." WHERE question_id=%d", $_REQUEST['question']));
		$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_QUESTIONS." WHERE ID=%d", $_REQUEST['question']));	
		$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_STUDENT_ANSWERS." WHERE question_id=%d", $_REQUEST['question']));
	}
	
	// mass delete questions
	if(!empty($_POST['mass_delete'])) {
		$qids = is_array($_POST['qids']) ? $_POST['qids'] : array(0);
		$qid_sql = implode(", ", $qids);
		
		$wpdb->query("DELETE FROM ".WATUPRO_QUESTIONS." WHERE ID IN ($qid_sql)");
		$wpdb->query("DELETE FROM ".WATUPRO_ANSWERS." WHERE question_id IN ($qid_sql)");
		$wpdb->query("DELETE FROM ".WATUPRO_STUDENT_ANSWERS." WHERE question_id IN ($qid_sql)");
	}
	
	// save question hints settings
	if(!empty($_POST['hints_settings'])) {
		if(empty($_POST['enable_question_hints'])) {
			$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_EXAMS." SET question_hints='' WHERE ID=%d", $_GET['quiz']));
		} 
		else {
			$per_quiz = intval($_POST['hints_per_quiz']);
			$per_question = intval($_POST['hints_per_question']);
			$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_EXAMS." SET question_hints='$per_quiz|$per_question' WHERE ID=%d", $_GET['quiz']));
		}
	}
	
	// mass change question category
	if(!empty($_POST['mass_change_category'])) {
		$qids = is_array($_POST['qids']) ? $_POST['qids'] : array(0);
		$qid_sql = implode(", ", $qids);
		
		$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_QUESTIONS." SET cat_id=%d 
			WHERE ID IN ($qid_sql) AND exam_id=%d", $_POST['mass_cat_id'], $_GET['quiz']));
	}
	
	// select exam
	$exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", $_GET['quiz']));
	$exam_name = stripslashes($exam->name);
	
	// reorder questions
	if(!empty($_GET['move'])) {
		WTPQuestion::reorder($_GET['move'], $_GET['quiz'], $_GET['dir']);
		watupro_redirect("admin.php?page=watupro_questions&quiz=".$_GET['quiz']);
	}
	
	// filter by category SQL
	$filter_sql = "";
	if(!empty($_GET['filter_cat_id'])) {
		 if($_GET['filter_cat_id']==-1) $filter_sql .= " AND Q.cat_id = 0 ";
		 else $filter_sql .= $wpdb->prepare(" AND Q.cat_id = %d ", $_GET['filter_cat_id']);
	}
	
	// filter by tag 
	if(!empty($_GET['filter_tag'])) {
		$tags = explode(",", $_GET['filter_tag']);
		
		foreach($tags as $tag) {
			$tag = trim($tag);
			$filter_sql .= " AND Q.tags LIKE '%|".$tag."|%'";
		}
	}
	
	// filter by ID
	if(!empty($_GET['filter_id'])) {
		// cleanup everything that is not comma or number
		$_GET['filter_id'] = preg_replace('/[^0-9\s\,]/', '', $_GET['filter_id']);
		if(!empty($_GET['filter_id'])) $filter_sql .= " AND Q.ID IN ($_GET[filter_id]) ";
	}
	
	// Retrieve the questions
	$all_question = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS Q.ID, Q.question, C.name as cat, 
		Q.answer_type as answer_type, Q.is_inactive as is_inactive, 
		Q.importance as importance, Q.truefalse as truefalse,
		(SELECT COUNT(*) FROM ".WATUPRO_ANSWERS." WHERE question_id=Q.ID) AS answer_count
				FROM `".WATUPRO_QUESTIONS."` AS Q
				LEFT JOIN ".WATUPRO_QCATS." AS C ON C.ID=Q.cat_id 
				WHERE Q.exam_id=".intval($_GET['quiz'])." $filter_sql ORDER BY Q.sort_order, Q.ID");
	$num_questions=sizeof($all_question);			
	
	if(empty($filter_sql)) WTPQuestion::fix_sort_order($all_question);
	
	// strip per page. We have good reasons to NOT use SQL limit here (the fix_sort_order function is the reason)
	$page_limit = 50;
	$offset = empty($_GET['offset']) ? 0 : intval($_GET['offset']);
	$all_question = array_slice($all_question, $offset, $page_limit);
	
	// select question categories
	$qcats = $wpdb -> get_results("SELECT * FROM ".WATUPRO_QCATS." ORDER BY name");
	
	// hints related stuff
	$enable_question_hints = $hints_per_quiz = $hints_per_question = 0;
	if(!empty($exam->question_hints)) {
		$enable_question_hints = true;
		list($hints_per_quiz, $hints_per_question) = explode("|", $exam->question_hints);
	}
	
	if(@file_exists(get_stylesheet_directory().'/watupro/questions.php')) require get_stylesheet_directory().'/watupro/questions.php';
	else require WATUPRO_PATH."/views/questions.php";
}

// manage question categories
function watupro_question_cats() {
	global $wpdb, $user_ID;
	
	$multiuser_access = 'all';
	if(watupro_intel()) $multiuser_access = WatuPROIMultiUser::check_access('qcats_access');
	
	$error = false;
	
	if(!empty($_POST['add'])) {
		if(!WTPCategory::add($_POST['name'], $_POST['description'])) $error = __('Another category with this name already exists.', 'watupro');
	}
	
	if(!empty($_POST['save'])) {
		if($multiuser_access == 'own') {
			$cat = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_QCATS." WHERE ID=%d", $_POST['id']));
			if($cat->editor_id != $user_ID) wp_die(__('You can manage only your own categories', 'watupro'));
		}		
		if(!WTPCategory::save($_POST['name'], $_POST['id'], $_POST['description'])) $error = __('Another category with this name already exists.', 'watupro');
	}
	
	if(!empty($_POST['del'])) {
		if($multiuser_access == 'own') {
			$cat = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_QCATS." WHERE ID=%d", $_POST['id']));
			if($cat->editor_id != $user_ID) wp_die(__('You can manage only your own categories', 'watupro'));
		}		
		WTPCategory::delete($_POST['id']);
	}
	
	// select all question categories	
	$own_sql = ($multiuser_access == 'own') ? $wpdb->prepare(" WHERE editor_id = %d ", $user_ID) : "";
	$cats = $wpdb->get_results("SELECT * FROM ".WATUPRO_QCATS." $own_sql ORDER BY ID");	
	
	if(@file_exists(get_stylesheet_directory().'/watupro/question_cats.php')) require get_stylesheet_directory().'/watupro/question_cats.php';
	else require WATUPRO_PATH."/views/question_cats.php";
}

// edit a selected choice with rich text editor
function watupro_edit_choice() {
	global $wpdb;
	
	// select choice
	$choice = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_ANSWERS." WHERE ID=%d", $_GET['id']));
	
	// select question
	$question = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_QUESTIONS." WHERE ID=%d", $choice->question_id));	
	
	if(!empty($_POST['ok'])) {
		$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_ANSWERS." SET answer=%s WHERE ID=%d",
			$_POST['answer'], $choice->ID));			
			
		// redirect to questions page
		watupro_redirect("admin.php?page=watupro_question&question=".$question->ID."&action=edit&quiz=".$question->exam_id);				
	}
	
	// select quiz
	$quiz = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", $question->exam_id));
	
	if(watupro_intel() and $quiz->is_personality_quiz) {
		$exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", $question->exam_id));		
		$grades = WTPGrade :: get_grades($exam);	
	}
	
	if(@file_exists(get_stylesheet_directory().'/watupro/edit-choice.html.php')) require get_stylesheet_directory().'/watupro/edit-choice.html.php';
	else require WATUPRO_PATH."/views/edit-choice.html.php";
}

// parses any occurencies of {{{answerto-...}}} mask.
// this mask is used to include the answer of a specific question
function watupro_parse_answerto($content, $taking_id) {
	global $wpdb;
	
	if(!strstr($content, '{{{answerto-')) return $content;
	
	// select all user answers of this taking
	$answers = $wpdb->get_results($wpdb->prepare("SELECT answer, question_id FROM ".WATUPRO_STUDENT_ANSWERS."
		WHERE taking_id=%d", $taking_id));
	
	$matches = array();
	preg_match_all("/{{{answerto-([^}}}])*}}}/", $content, $matches);
	
	foreach($matches[0] as $cnt=>$match) {
		// extract the question number
		$qid = str_replace('{{{answerto-','',$match);
		$qid = str_replace('}}}','',$qid);
		
		foreach($answers as $answer) {
			if($answer->question_id == $qid) {
				$content = str_replace('{{{answerto-'.$qid.'}}}', stripslashes($answer->answer), $content);
				break;
			}
		}		
	} // end foreach matches
	
	return $content;
} // end parse_answerto