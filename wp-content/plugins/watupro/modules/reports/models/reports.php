<?php
class WTPReports {
	public static $add_scripts = false;	
	public static $user = array();
	
	static function admin_menu() {
		$cap_level = current_user_can(WATUPRO_MANAGE_CAPS)?WATUPRO_MANAGE_CAPS:'read';		
		
		add_submenu_page('my_watupro_exams', __("Quiz Reports", 'watupro'), __("Quiz Reports", 'watupro'), $cap_level, 'watupro_reports', 
				array(__CLASS__, "dispatch"));		
				
		// hidden pages
		add_submenu_page(NULL, __("Stats Per Question", 'watupro'), __("Status Per Question", 'watupro'), $cap_level, 'watupro_question_stats',
			array('WatuPROStats', 'per_question'));		
		add_submenu_page(NULL, __("All Question Answers", 'watupro'), __("All Question Answers", 'watupro'), $cap_level, 'watupro_question_answers',
			array('WatuPROStats', 'all_answers'));		
		add_submenu_page(NULL, __("Chart By Grade", 'watupro'), __("Chart By Grade", 'watupro'), $cap_level, 'watupro_question_chart',
			array('WatuPROStats', 'chart_by_grade'));		
		add_submenu_page(NULL, __("Stats per Category", 'watupro'), __("Stats per Category", 'watupro'), $cap_level, 'watupro_cat_stats',
			array('WatuPROStats', 'per_category'));				
	}
	
	// decides which tab to load
	static function dispatch() {
		global $user_ID;
		
		// define user ID
		if(!empty($_GET['user_id']) and is_numeric($_GET['user_id']) and current_user_can(WATUPRO_MANAGE_CAPS)) $report_user_id = intval($_GET['user_id']);	
		else $report_user_id = $user_ID;	
		
		// select user to display info
		$user = get_userdata($report_user_id);
		if($user_ID != $report_user_id) echo '<p>'.__('Showing quiz reports for ', 'watupro').' <b>'.$user->data->user_nicename.'</b></p>';
		
		switch(@$_GET['tab']) {
			case 'tests': self::tests($report_user_id); break; // exams taken
			case 'skills': self::skills($report_user_id); break; // question categories
			case 'time': self::time($report_user_id); break;
			case 'history': self::history($report_user_id); break;
			default: self::overview($report_user_id); break;
		}
	}
	
	static function overview($report_user_id, $has_tabs = true) {
		 global $wpdb;
		 
		 // all exams taken
		 $taken_exams = $wpdb->get_results($wpdb->prepare("SELECT tT.*, tE.cat_id as cat_id 
		 	FROM ".WATUPRO_TAKEN_EXAMS." tT JOIN ".WATUPRO_EXAMS." tE ON tT.exam_id = tE.id
		 	WHERE user_id=%d ORDER BY date", $report_user_id));	
		 	
		 // tests attempted var
		 $num_attempts = sizeof($taken_exams);	
		 	
		 // skills practiced (question categories)
		 $skills = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT(cat_id) FROM ".
		 	WATUPRO_QUESTIONS." WHERE ID IN (SELECT question_id FROM ".WATUPRO_STUDENT_ANSWERS." WHERE user_id=%d)
		 	AND is_inactive=0 AND is_survey=0", $report_user_id));
		 $num_skills = sizeof($skills);
		 		 
		 // certificates earned
		 $cnt_certificates = $wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) FROM ".WATUPRO_USER_CERTIFICATES."
		 WHERE user_id=%d", $report_user_id));
		 
		 // figure out num exams taken by exam category - select categories I have access to
		 $cat_ids = WTPCategory::user_cats($report_user_id);
		 $cats = $wpdb->get_results("SELECT * FROM ".WATUPRO_CATS." WHERE ID IN(".implode(",", $cat_ids).")", ARRAY_A);
		 $cats = array_merge( array(array("ID"=>0, "name"=>__('Uncategorized', 'watupro'))), $cats);
		 		 
		 $report_cats = array();
		 // for any categories that don't have zero, add them to report_cats along with time_spent
		 foreach($cats as $cnt=>$cat) {
		 		$num_attempts = 0;
		 		foreach($taken_exams as $taken_exam) {
		 				if($taken_exam->cat_id == $cat['ID']) $num_attempts++;
		 		}
		 		
		 		$cats[$cnt]['num_attempts'] = $num_attempts;
		 		if($num_attempts) $report_cats[] = $cats[$cnt];
		 }
		 
		 // now select question categories
		 $qcats = $wpdb->get_results($wpdb->prepare("SELECT COUNT(tA.ID) as cnt, tC.name as name
		 	FROM ".WATUPRO_QCATS." tC JOIN ".WATUPRO_QUESTIONS." tQ ON tQ.cat_id = tC.ID 
		 	JOIN ".WATUPRO_STUDENT_ANSWERS." tA ON tA.question_id = tQ.ID 	AND tA.user_id = %d 
		 	GROUP BY tC.ID ORDER BY tC.name", $report_user_id));
		 $question_cats = array(); // fill only these that has at least 1 answer
		 foreach($qcats as $qcat) {
		 	if($qcat->cnt > 0) $question_cats[] = $qcat;
		 }	
		 	
		 self::$add_scripts = true;
		 if(@file_exists(get_stylesheet_directory().'/watupro/reports/overview.php')) require get_stylesheet_directory().'/watupro/reports/overview.php';
		 else require WATUPRO_PATH."/modules/reports/views/overview.php";
		 self::print_scripts();
	}
	
	static function tests($report_user_id, $has_tabs = true) {
		// details about taken exams
		global $wpdb;
		
		// select all taken exams along with exam data
		$sql = "SELECT COUNT(tA.ID) as cnt_answers, tT.*, tE.name as name, tT.exam_id as exam_id,
			tE.published_odd as published_odd, tE.published_odd_url as published_odd_url 
		  FROM ".WATUPRO_TAKEN_EXAMS." tT, ".WATUPRO_EXAMS." tE, ".WATUPRO_STUDENT_ANSWERS." tA, ".WATUPRO_QUESTIONS." tQ 
			WHERE tT.user_id=%d AND tT.in_progress=0 AND tT.exam_id=tE.ID AND tA.taking_id = tT.id	
			AND tA.question_id=tQ.ID AND tQ.is_survey=0
			GROUP BY tT.ID ORDER BY tT.ID DESC";
		$exams = $wpdb->get_results($wpdb->prepare($sql, $report_user_id));
		
		$posts=$wpdb->get_results("SELECT * FROM {$wpdb->posts} 
		WHERE post_content LIKE '%[watupro %]%' 
		AND post_status='publish'
		ORDER BY post_date DESC"); 
		
		// match posts to exams
		foreach($exams as $cnt=>$exam) {
			$exams[$cnt]->time_spent = self::time_spent($exam);
			foreach($posts as $post) {
				if(stristr($post->post_content,"[WATUPRO ".$exam->exam_id."]")) {
					$exams[$cnt]->post=$post;			
					break;
				}
			}
		}
		
		if(!empty($_GET['export'])) {
			$newline=watupro_define_newline();
			$rows=array();			
			$rows [] = __('Quiz name', 'watupro'). "\t" . __('Time spent', 'watupro'). "\t"
				. __('Problems attempted', 'watupro'). "\t" . __('Grade', 'watupro') . "\t"
				. __('Points', 'watupro') . "\t" . __('Percent correct', 'watupro');
			foreach($exams as $exam) {
				$result = stripslashes($exam->result);
				$result = str_replace(array("\t", "\r", "\n"), array("   ", " ", " "), $result);				
				
				$rows[] = stripslashes($exam->name) . "\t" . self::time_spent_human($exam->time_spent) .
					"\t" . $exam->cnt_answers . "\t" . $result ."\t" . $exam->points ."\t" . $exam->percent_correct;
			}	
			
			$csv=implode($newline,$rows);
			
			$now = gmdate('D, d M Y H:i:s') . ' GMT';		
			header('Content-Type: ' . watupro_get_mime_type());
			header('Expires: ' . $now);
			header('Content-Disposition: attachment; filename="user-'.$report_user_id.'.csv"');
			header('Pragma: no-cache');
			echo $csv;
			exit;
		}
		
		wp_enqueue_script('thickbox',null,array('jquery'));
		wp_enqueue_style('thickbox.css', '/'.WPINC.'/js/thickbox/thickbox.css', null, '1.0');
		
		if(@file_exists(get_stylesheet_directory().'/watupro/reports/tests.php')) require get_stylesheet_directory().'/watupro/reports/tests.php';
		else require WATUPRO_PATH."/modules/reports/views/tests.php";
	}
	
	static function skills($report_user_id, $has_tabs = true) {
		global $wpdb;
		
		// select exam categories that I can access
		$cat_ids = WTPCategory::user_cats($report_user_id);
		$cat_id_sql=implode(",",$cat_ids);	
		$exam_cats = $wpdb->get_results("SELECT * FROM ".WATUPRO_CATS." WHERE ID IN ($cat_id_sql) ORDER BY name");
		
		// question categories
		$q_cats = $wpdb->get_results("SELECT * FROM ".WATUPRO_QCATS." ORDER BY name");		
		// add uncategorized
		$q_cats[] = (object)array("ID"=>0, "name"=>__('Uncategorized', 'watupro'));
		
		// exam category filter?
		$exam_cat_sql = (@$_POST['cat'] < 0)? $cat_id_sql : @$_POST['cat'];
		
		
		// now select all exams I have access to
		list($my_exams) = WTPExam::my_exams($report_user_id, $exam_cat_sql);
		
		$skill_filter = empty($_POST['skill_filter'])?"all":$_POST['skill_filter'];
		
		// practiced only?
		if($skill_filter == 'practiced') {
			 $final_exams = array();
			 foreach($my_exams as $exam) {
			 	  if(!empty($exam->taking->ID)) $final_exams[] = $exam;
			 }
			 $my_exams = $final_exams;
		}
		
		// proficiency filter selected? If yes, we'll need to limit exams
		// to those that are taken with at least $_POST['proficiency_goal'] % correct answers		
		if($skill_filter == 'proficient') {				
				$final_exams = array();
				foreach($my_exams as $exam) {					 
					 if(!empty($exam->taking->ID) and $exam->taking->percent_correct >= $_POST['proficiency_goal']) {
					 		$final_exams[] = $exam;
					 }
				} // end exams loop		 
				
				$my_exams = $final_exams;
		}
		
		// for each exam select match answers and fill % correct info by category
		$taking_ids = array(0);
		foreach($my_exams as $my_exam) {
			if(!empty($my_exam->taking->ID)) $taking_ids[] = $my_exam->taking->ID;
		} 
		$user_answers = $wpdb->get_results("SELECT tA.is_correct as is_correct, tA.taking_id as taking_id, tQ.cat_id as cat_id 
			FROM ".WATUPRO_STUDENT_ANSWERS." tA JOIN ".WATUPRO_QUESTIONS." tQ ON tQ.ID = tA.question_id
			WHERE tA.taking_id IN (".implode(',', $taking_ids).") ORDER BY tA.ID");
			
		foreach($my_exams as $cnt=>$my_exam) {
			if(empty($my_exam->taking->ID)) continue;
			
			$cats = array();
			foreach($user_answers as $answer) {
				if($answer->taking_id != $my_exam->taking->ID) continue;
				
				$correct_key = $answer->is_correct ? 'num_correct' : 'num_incorrect';
				if(isset($cats[$answer->cat_id][$correct_key])) $cats[$answer->cat_id][$correct_key]++;
				else $cats[$answer->cat_id][$correct_key] = 1;
			}		
			
			// now foreach cat calculate the correctness
			foreach($cats as $cat_id=>$cat) {
				$num_correct = isset($cat['num_correct']) ? $cat['num_correct'] : 0;
				$num_incorrect = isset($cat['num_incorrect']) ? $cat['num_incorrect'] : 0;
				$total = $num_correct + $num_incorrect;
				$percentage = $total ? round(100 * $num_correct / $total) : 0;
				$cats[$cat_id]['percentage'] = $percentage;
			}
			
			// finally add cats to exam
			$my_exams[$cnt]->cats = $cats;
		}	
		
		// group exams by question category
		$skills = array(); // skills equal question categories
		$num_proficient = 0;
		foreach($q_cats as $q_cat) {
			// skill filter (question category) selected in the drop-down?
			if((@$_POST['q_cat']>-1) and $q_cat->ID != @$_POST['q_cat']) continue;
			
			// now construct array of this category along with the exams in it
			// then add in $skills. $skills is the final array that we'll use in the view
			$exams = array();
			foreach($my_exams as $exam) {
				 $has_questions = $wpdb->get_var($wpdb->prepare("SELECT ID FROM ".WATUPRO_QUESTIONS." 
				 	WHERE exam_id=%d AND cat_id=%d AND is_inactive=0 AND is_survey=0", $exam->ID, $q_cat->ID));
				 	
				 if(!$has_questions) continue;
				 
				 $exams[] = $exam;	
			}	
			
			$skills[] = array("category"=>$q_cat, "exams"=>$exams, "id"=>$q_cat->ID);
			if(sizeof($exams)) $num_proficient++; // proficient in X skills
		}	
		
		// by default $skills is ordered by category (name). Do we have to reorder?
		// NOT SURE THIS MAKES SENSE, SO FOR NOW NYI
		if(!empty($_POST['sort_skills']) and $_POST['sort_skills']=='proficiency') {
			// Sort by sum of proficiency of latest taking of the exams in this category
			// let's create an array that'll contain only cat ID and cumulative proficiency
			// for easier sorting
			$cat_ids = array();
			foreach($skills as $skill) {
				 // NYI	
			}			
		}

		if(@file_exists(get_stylesheet_directory().'/watupro/reports/skills.php')) require get_stylesheet_directory().'/watupro/reports/skills.php';
		else require WATUPRO_PATH."/modules/reports/views/skills.php";
	}
	
	// history
	static function history($report_user_id, $has_tabs = true) {
		global $wpdb;
		$report_user_id = intval($report_user_id);
		
		// select taken exams and fill the details for them
		$taken_exams = $wpdb->get_results("SELECT tT.*, tE.name as exam_name, tP.ID as post_id,
			tE.published_odd as published_odd, tE.published_odd_url as published_odd_url 
			FROM ".WATUPRO_TAKEN_EXAMS." tT LEFT JOIN ".WATUPRO_EXAMS." tE ON tE.ID = tT.exam_id 
			LEFT JOIN {$wpdb->posts} tP ON tP.post_status='publish' AND tP.post_title != ''
			AND tP.post_content LIKE CONCAT('%[watupro ', tE.ID, ']%') 
			WHERE tT.user_id=$report_user_id ORDER BY tT.end_time DESC");		
			
		$details = $wpdb->get_results($wpdb->prepare("SELECT tA.*, tQ.cat_id as cat_id 
			FROM ".WATUPRO_STUDENT_ANSWERS." tA JOIN ".WATUPRO_QUESTIONS." tQ
			ON tQ.ID = tA.question_id AND tQ.is_inactive=0 AND tQ.is_survey = 0
			WHERE tA.user_id=%d", $report_user_id));
			
		$total_time = $total_problems = $total_skills = 0;
			
		foreach($taken_exams as $cnt=>$exam) {
			// add details
			$taken_exams[$cnt]->details = array();
			$taken_exams[$cnt]->num_problems = 0;
			$taken_exams[$cnt]->skills_practiced = array();
			foreach($details as $detail) {
				if($detail->taking_id != $exam->ID) continue; 
				$taken_exams[$cnt]->details[] = $detail;
				$taken_exams[$cnt]->num_problems++;
				if(!in_array($detail->cat_id, $taken_exams[$cnt]->skills_practiced)) $taken_exams[$cnt]->skills_practiced[] = $detail->cat_id; 
			}
			
			// calculate start time			
			list($date, $time) = explode(" ", $exam->start_time);			
			$date = explode("-",$date);
			$time = explode(":", $time);			
			$start_time = mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]);
			$taken_exams[$cnt]->start_time = $start_time;
			
			// calculate end time
			list($date, $time) = explode(" ", $exam->end_time);
			$date = explode("-",$date);
			$time = explode(":", $time);			
			$end_time = mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]);
			$taken_exams[$cnt]->end_time = $end_time;
			
			// fill the period property for later use (month, year)
			$taken_exams[$cnt]->period = date('F', $end_time)." ".$date[0];			
			$taken_exams[$cnt]->period_morris = date("Y-m", $end_time); 
			
			$time_spent = ($end_time - $start_time) ? ($end_time - $start_time) : 0;
			
			$taken_exams[$cnt]->time_spent = $time_spent;
			$total_time += $time_spent;
			
			$total_problems += $taken_exams[$cnt]->num_problems;
			
			// num skills
			$taken_exams[$cnt]->num_skills = sizeof($taken_exams[$cnt]->skills_practiced);
			$total_skills += $taken_exams[$cnt]->num_skills;
		}	
		
		// summary calculations
		$total_sessions = sizeof($taken_exams);
		$avg_time_spent = $total_sessions? ($total_time / $total_sessions) : 0;
		$avg_problems = round($total_sessions? ($total_problems / $total_sessions) : 0);
		$avg_skills = round($total_sessions? ($total_skills / $total_sessions) : 0);
		
		// group takings by month/year for the chart and table
		$periods = array();
		foreach($taken_exams as $exam) {
			if(!in_array($exam->period, $periods)) $periods[] = $exam->period;
		}
		
		// now fill logs array which is actually periods with exams in them
		$logs = array();
		$max_exams = 0; // max exams in a period, so we can build the chart
		foreach($periods as $period) {
			 $period_exams = array();
			 $time_spent = 0;			 
			 foreach($taken_exams as $exam) {
			 		if($exam->period != $period) continue; 
			 		$period_exams[] = $exam;
			 		$time_spent += $exam->time_spent;
			 }
			 
			 $num_exams = sizeof($period_exams);
			 if($num_exams > $max_exams) $max_exams = $num_exams;
			 $logs[] = array("period"=>$period, "exams"=>$period_exams, "time_spent"=>$time_spent, 
			 		"num_exams"=> $num_exams);
		}
		
		// for the char we need reversed logs and no more than 12		
		$chartlogs = array_reverse($logs);
		if(sizeof($chartlogs)>12) $chartlogs = array_slice($chartlogs, sizeof($chartlogs) - 12);
		
		// let's keep the chart up to 200px high. Find height in px for 1 exam in chart
		$one_exam_height = $max_exams ? (200 / $max_exams) : 0;
		
		$date_format = get_option("date_format");
		 
		if(@file_exists(get_stylesheet_directory().'/watupro/reports/history.php')) require get_stylesheet_directory().'/watupro/reports/history.php';
		else require WATUPRO_PATH."/modules/reports/views/history.php";	
	}
	
	// helper to calculate time spent in exam
	static function time_spent($exam) {
		return WTPRecord :: time_spent($exam);		
	} 
	
	static function time_spent_human($time_spent) {
		return WTPRecord :: time_spent_human($time_spent);		
	}	
	
	// register javascripts
	static function register_scripts() {
		wp_register_script('raphael', plugins_url('watupro/modules/reports/js/raphael-min.js'), null, '1.0', true);
		wp_register_script('g.raphael', plugins_url('watupro/modules/reports/js/g.raphael-min.js'), null, '1.0', true);
		wp_register_script('g.bar', plugins_url('watupro/modules/reports/js/g.bar-min.js'), null, '1.0', true);
		wp_register_script('g.line', plugins_url('watupro/modules/reports/js/g.line-min.js'), null, '1.0', true);
		wp_register_script('g.pie', plugins_url('watupro/modules/reports/js/g.pie-min.js'), null, '1.0', true);
		wp_register_script('g.dot', plugins_url('watupro/modules/reports/js/g.dot-min.js'), null, '1.0', true);
	}
	
	static function print_scripts() {		
		if ( ! self::$add_scripts ) return false; 
		wp_print_scripts('raphael');
		wp_print_scripts('g.raphael');
		wp_print_scripts('g.bar');
		wp_print_scripts('g.line');
		wp_print_scripts('g.pie');
		wp_print_scripts('g.dot');
	}
	
	// init module
	static function init() {
		self::register_scripts();
		
		add_shortcode( 'WATUPROR', array('WatuPROReportShortcodes', 'report') );
		add_shortcode( 'watupror-stats-per-question', array('WatuPROReportShortcodes', 'per_question') );  
		add_shortcode( 'watupror-stats-per-category', array('WatuPROReportShortcodes', 'per_category') );
		add_shortcode( 'watupror-question-answers', array('WatuPROReportShortcodes', 'all_answers') );
		add_shortcode( 'watupror-chart-by-grade', array('WatuPROReportShortcodes', 'chart_by_grade') );
		add_shortcode( 'watupror-poll', array('WatuPROReportShortcodes', 'poll') );
	}
}