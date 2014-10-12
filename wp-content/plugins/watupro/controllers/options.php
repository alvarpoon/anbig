<?php
function watupro_options() {
    global $wpdb, $wp_roles;
    $roles = $wp_roles->roles;		
		
		if(isset($_REQUEST['submit']) and $_REQUEST['submit']) {
			if(empty($_POST['currency'])) $_POST['currency'] = $_POST['custom_currency'];			
			
			$options = array('single_page', 'answer_type', 'delete_db', 
				'paypal', 'other_payments', 'currency', 'recaptcha_public', 
				'recaptcha_private', 'accept_stripe', 'stripe_public', 'stripe_secret', 'really_delete_db',
				'accept_paypoints', 'paypoints_price', 'paypoints_button', 'debug_mode',
				'nodisplay_myquizzes', 'nodisplay_mycertificates', 'nodisplay_reports_tests',
				'nodisplay_reports_skills', 'nodisplay_reports_history', 
				'nodisplay_paid_quizzes', 'nodisplay_mysettings', 'always_load_scripts');
			foreach($options as $opt) {				
				if(!empty($_POST[$opt])) update_option('watupro_' . $opt, $_POST[$opt]);
				else update_option('watupro_' . $opt, 0);
			}
			
			update_option('watupro_admin_email', $_POST['watupro_admin_email']);
			
			// add/remove capabilities
			if(current_user_can('manage_options')) {					
				foreach($roles as $key=>$role) {
					$r=get_role($key);
					
					if(@in_array($key, $_POST['manage_roles'])) {
	    				if(empty($r->capabilities['watupro_manage_exams'])) $r->add_cap('watupro_manage_exams');
					}
					else $r->remove_cap('watupro_manage_exams');
				}	
			} // end if administrator	
		}
		
		if(watupro_intel()) {
			$currency = get_option('watupro_currency');
			$currencies=array('USD'=>'$', "EUR"=>"&euro;", "GBP"=>"&pound;", "JPY"=>"&yen;", "AUD"=>"AUD",
		   "CAD"=>"CAD", "CHF"=>"CHF", "CZK"=>"CZK", "DKK"=>"DKK", "HKD"=>"HKD", "HUF"=>"HUF",
		   "ILS"=>"ILS", "MXN"=>"MXN", "NOK"=>"NOK", "NZD"=>"NZD", "PLN"=>"PLN", "SEK"=>"SEK",
		   "SGD"=>"SGD", "ZAR" => "ZAR");
		   $currency_keys = array_keys($currencies);		   
			$accept_stripe = get_option('watupro_accept_stripe');
			$payment_errors = get_option("watupro_errorlog");
			$payment_errors = substr($payment_errors, 0, 10000);
			$other_payments = get_option('watupro_other_payments');
			$other_payments = empty($other_payments) ? "" : $other_payments;
		}
		
		// exams in watu light?
		if($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix. "watu_master"."'") == $wpdb->prefix. "watu_master") {	
			$watu_exams=$wpdb->get_results("SELECT * FROM ".$wpdb->prefix. "watu_master ORDER BY ID");
			
			if(!empty($_POST['copy_exams'])) {
				$num_copied=0;
				foreach($watu_exams as $exam) {
					// transfer the answer display settings in the best possible way
					$exam->live_result = 0;
					if($exam->show_answers == 1) $exam->final_screen .= "\n\n<p>%%ANSWERS%%</p>";
					if($exam->show_answers == 2) $exam->live_result = 1;		
					
					// randomize questions and/or answers?
					$randomize_questions = 0;
					if($exam->randomize and $exam->randomize_answers) $randomize_questions = 1;
					if($exam->randomize and !$exam->randomize_answers) $randomize_questions = 2;
					if(!$exam->randomize and $exam->randomize_answers) $randomize_questions = 3;
					
					$wpdb->query($wpdb->prepare("INSERT INTO ".WATUPRO_EXAMS." SET 
						name=%s, description=%s, final_screen=%s, added_on=%s, is_active=1,
						show_answers=0, email_output='', live_result=%d, randomize_questions=%d,
						require_login=%d, email_admin=%d", 
						stripslashes($exam->name), stripslashes($exam->description), 
						stripslashes($exam->final_screen), date("Y-m-d"), 
						$exam->live_result, $randomize_questions, $exam->require_login, $exam->notify_admin));
						
					$id=$wpdb->insert_id;
					// echo $id.'a';
					
					if($id) {
						$num_copied++;
						
						// copy questions and choices
						$questions=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."watu_question 
							WHERE exam_id=%d ORDER BY ID", $exam->ID));
						foreach($questions as $question) {
							$wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."watupro_question SET
								exam_id=%d, question=%s, answer_type=%s, sort_order=%d", 
								$id, stripslashes($question->question), stripslashes($question->answer_type), $question->sort_order));
							$qid=$wpdb->insert_id;
							
							if($qid) {
								$choices=$wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}watu_answer 
									WHERE question_id=%d ORDER BY ID", $question->ID));
								foreach($choices as $choice) {
									$wpdb->query($wpdb->prepare("INSERT INTO {$wpdb->prefix}watupro_answer SET
										question_id=%d, answer=%s, correct=%s, point=%d, sort_order=%d",
										$qid, stripslashes($choice->answer), $choice->correct, $choice->point, $choice->sort_order));
								}	
							}	
						}				
						
						// copy grades
						$grades=$wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}watu_grading WHERE exam_id=%d ORDER BY ID", $exam->ID));
						
						foreach($grades as $gct=>$grade) {
							$wpdb->query($wpdb->prepare("INSERT INTO {$wpdb->prefix}watupro_grading SET
								exam_id=%d, gtitle=%s, gdescription=%s, gfrom=%d, gto=%d",  
								$id, stripslashes($grade->gtitle), stripslashes($grade->gdescription), $grade->gfrom, $grade->gto));
							$grade_id = $wpdb->insert_id;
							$grades[$gct]->new_grade_id = $grade_id;
						} // end foreach grade
						
						// replace shortcodes?
						if(!empty($_POST['replace_watu_shortcodes'])) {
							$wpdb->query("UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, '[WATU ".$exam->ID."]', '[watupro ".$id."]')");
						}				
						
						// copy takings?
						if(!empty($_POST['copy_takings'])) {
							$takings = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}watu_takings 
								WHERE exam_id=%d ORDER BY ID", $exam->ID));
							
							foreach($takings as $taking) {
								// figure out the taking grade ID
								$taking_grade_id = 0;
								foreach($grades as $grade) {
									if($taking->grade_id == $grade->ID) $taking_grade_id = $grade->new_grade_id;
								}				
								
								$wpdb->query($wpdb->prepare("INSERT INTO ".WATUPRO_TAKEN_EXAMS." SET
									user_id=%d, exam_id=%d, date=%s, points=%s, details=%s, result=%s, ip=%s, grade_id=%d",
									$taking->user_id, $id, $taking->date, $taking->points, stripslashes($taking->snapshot),
									stripslashes($taking->result), $taking->ip, $taking_grade_id));
							}	
						}		
						
					} // end if exam $id	
				} // end foreach exam
		
				$copy_message= sprintf(__("%d %s successfully copied.", 'watupro'), $num_copied, __('quizzes', 'watupro'));		
				
			} // end if copy exams
		} // end if there is watu table
		
		$delete_db = get_option('watupro_delete_db');
		
		// save no_ajax
		if(!empty($_POST['save_ajax_settings'])) {
			$ids = empty($_POST['no_ajax']) ? array(0) : $_POST['no_ajax'];
			
			$wpdb->query("UPDATE ".WATUPRO_EXAMS." SET no_ajax=1 WHERE id IN (".implode(', ', $ids).")");
			$wpdb->query("UPDATE ".WATUPRO_EXAMS." SET no_ajax=0 WHERE id NOT IN (".implode(', ', $ids).")");
			
			update_option('watupro_max_upload', intval($_POST['max_upload']));
			update_option('watupro_upload_file_types', $_POST['upload_file_types']);
		}
		
		// select all quizzes for No Ajax option
		$quizzes = $wpdb->get_results("SELECT ID, name, no_ajax FROM ".WATUPRO_EXAMS." ORDER BY name");
		
		if(@file_exists(get_stylesheet_directory().'/watupro/options.php')) require get_stylesheet_directory().'/watupro/options.php';
		else require WATUPRO_PATH."/views/options.php";   
}

// user options
function watupro_my_options() {
	global $wpdb, $user_ID;
	
	if(!empty($_POST['ok'])) {
		update_user_meta($user_ID, "watupro_no_quiz_mails", @$_POST['no_quiz_mails']);
	}
	
	if(@file_exists(get_stylesheet_directory().'/watupro/my-options.html.php')) require get_stylesheet_directory().'/watupro/my-options.html.php';
	else require WATUPRO_PATH."/views/my-options.html.php";
}