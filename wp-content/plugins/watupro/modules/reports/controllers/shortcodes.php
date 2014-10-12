<?php
class WatuPROReportShortcodes {
	static function report($attr) {
		global $user_ID;
		
		$content = '';		
		watupro_vc_scripts();
		
		// reports for who?
		$user_id = @$attr[1];		
		if(empty($user_id)) $user_id = $user_ID;		
		if(empty($user_id)) return __('This content is only for logged in users', 'watupro');
		
		$type = @$attr[0];
		$type = strtolower($type);
		if(!in_array($type, array("overview", "tests", "skills", "history"))) $type = 'overview';		
		
		ob_start();
		switch($type) {
			case 'overview': WTPReports::overview($user_id, false); break;
			case 'tests': WTPReports::tests($user_id, false); break;
			case 'skills': WTPReports::skills($user_id, false); break;
			case 'history': WTPReports::history($user_id, false); break;
		}
		
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
	
	// stats per question
	static function per_question($atts) {
		$_GET['exam_id'] = empty($atts[0]) ? 0 : intval($atts[0]);
		
		ob_start();
		WatuPROStats :: per_question(true);
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
	
	// stats per question
	static function per_category($atts) {
		$_GET['exam_id'] = empty($atts[0]) ? 0 : intval($atts[0]);
		
		ob_start();
		WatuPROStats :: per_category(true);
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
	
	// all question answers (details of stats per question)
	static function all_answers($atts) {
		$_GET['exam_id'] = empty($atts[0]) ? 0 : intval($atts[0]);
		$_GET['id'] = empty($atts[1]) ? 0 : intval($atts[1]);
		
		ob_start();
		WatuPROStats :: all_answers(true);
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
	
	static function chart_by_grade($atts) {
		$_GET['exam_id'] = empty($atts[0]) ? 0 : intval($atts[0]);
		
		ob_start();
		WatuPROStats :: chart_by_grade(true);
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
	
	// displays poll-like results per question
	static function poll($atts) {	
		global $wpdb;	
		$question_id = $atts['question_id'];		
		$mode = empty($atts['mode']) ? 'answers' : $atts['mode']; // correct or answers
		$correct_color = empty($atts['correct_color']) ? 'green' : $atts['correct_color'];
		$wrong_color = empty($atts['wrong_color']) ? 'red' : $atts['wrong_color'];
		
		// select question
		$question = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_QUESTIONS." WHERE ID=%d", $question_id));		
		if(empty($question)) return __('n/a', 'watupro');
		
		// mode can be "answers" only if the question is checkbox or radio
		if($question->answer_type!='checkbox' and $question->answer_type!='radio') $mode = 'correct';
		
		ob_start();
		
		if($mode == 'correct') {
			$poll = WatuPROStats :: poll_correct($question->ID);
			// chart	
			$num_wrong = $poll['total'] - $poll['correct'];
			$percent_wrong = 100 - $poll['percent'];
			include(WATUPRO_PATH."/modules/reports/views/poll-chart-correct.html.php");		
		}
		else {
			// showing poll-like stats where num / % is matched to each answers
			$answers = WatuPROStats :: poll_answers($question);
			include(WATUPRO_PATH."/modules/reports/views/poll-chart-answers.html.php");
		}
		
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
}