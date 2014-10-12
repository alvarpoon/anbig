<?php 
class WTPGrade {
	// calculate grade
	// personality grade calculation is passed to the intelligence module
	static function calculate($exam_id, $achieved, $percent, $cat_id = 0, $user_grade_ids = null) {
		global $wpdb;		
		
		$exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", $exam_id));
		$grade = __('None', 'watupro');
		$grade_obj = (object)array("title"=>__('None', 'watupro'), "description"=>"");
		$do_redirect = false;
		$certificate_id=0;
		
		$grades = self :: get_grades($exam, $cat_id);		
		
		// for the sake of grade calculation, $achieved won't be below zero
		// if($achieved < 0 ) $achieved = 0; 		
		if( count($grades) ) {			
			// calculate by percentage in Intelligence
			if(watupro_intel()) {				
				if(!empty($exam->is_personality_quiz)) {					
					return WTPIGrade :: calculate($user_grade_ids); 
				}
			}			
			
			foreach($grades as $grow ) { 
				$match_criteria = $achieved;
				   
				// from Intelligence - calculate by %
				if(!empty($exam->grades_by_percent)) $match_criteria = $percent;			
				if( $grow->gfrom <= $match_criteria and $match_criteria <= $grow->gto ) {
					list($grade, $grade_obj, $certificate_id, $do_redirect) = self :: match_grade($grow); 					               
					break;
				}
			}
		}
		
		return array($grade, $certificate_id, $do_redirect, $grade_obj);
	}

	// small helper to return the data, used also by the Intelligence module grade object	
	static function match_grade($grow) {
		$do_redirect = false;					
		$grade = $grow->gtitle;		
		// redirect?
		if(preg_match("/^http:\/\//i", $grade) or preg_match("/^https:\/\//i", $grade)) {
			$do_redirect = $grade;
		}				
		
		if(!empty($grow->gdescription)) $grade.="<p>".stripslashes($grow->gdescription)."</p>";
		
		return array($grade, $grow,  $grow->certificate_id, $do_redirect); 
	}
	
	// if %%CATGRADES%% is used, this calculates and replaces them on the final screen
	static function replace_category_grades($final_screen, $taking_id, $exam_id) {
		global $wpdb;
		
		if(!strstr($final_screen, '%%CATGRADES%%')) return false;
		
		$exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", $exam_id));
		$advanced_settings = unserialize(stripslashes($exam->advanced_settings));
		
		if(empty($exam->gradecat_design)) return false; // no need to go further if gradecat design is not created
		
		$catgrades = array();
		
		// select the student_answers details of this taking and group by category
		$answers = $wpdb->get_results( $wpdb->prepare("SELECT tA.*, tQ.cat_id as cat_id 
			FROM ".WATUPRO_STUDENT_ANSWERS." tA JOIN ".WATUPRO_QUESTIONS." tQ ON tA.question_id=tQ.ID 
			WHERE tA.taking_id=%d", $taking_id) ); 
		$cat_ids = array(0);
		foreach($answers as $answer) {
			if(!in_array($answer->cat_id, $cat_ids)) $cat_ids[] = $answer->cat_id;
		}	
		
		// now select the categories
		$cats = $wpdb -> get_results("SELECT * FROM ".WATUPRO_QCATS." WHERE ID IN (".implode(",", $cat_ids).") ORDER BY name");
		if( !empty($advanced_settings['sorted_categories']) and 
			 (empty($advanced_settings['gradecat_order']) or $advanced_settings['gradecat_order'] == 'same')
			) {
			$final_cats = array();
			$sorted_cats = $advanced_settings['sorted_categories'];
			
			asort($sorted_cats); // sort by the order number
			// print_r($sorted_cats);
			foreach($sorted_cats as $key => $val) {
				foreach($cats as $cat) {
					if($cat->name == $key) $final_cats[] = $cat;
				}
			}
			
			$cats = $final_cats;
		}
		
		// for each category calculate the grade and add to $catgrades
		foreach($cats as $cat) {
			$total = $correct = $percentage = $points = 0;
			$catgrade = stripslashes($exam->gradecat_design);
			
			foreach($answers as $answer) {
				if($answer->cat_id != $cat->ID) continue;
				$total ++;
				if($answer->is_correct) $correct++;
				$points += $answer->points;
			}
			
			// percentage and grade
			$percent = $total ? round($correct / $total, 2) * 100 : 0;
			list($grade, $certificate_id, $do_redirect, $grade_obj) = self::calculate($exam_id, $points, $percent, $cat->ID);
			
			// now replace in the $catgrade text
			$catgrade = str_replace("%%CATEGORY%%", stripslashes($cat->name), $catgrade);
			$catgrade = str_replace("%%CATDESC%%", stripslashes($cat->description), $catgrade);
			$catgrade = str_replace("%%CORRECT%%", $correct, $catgrade);
			$catgrade = str_replace("%%TOTAL%%", $total, $catgrade);
			$catgrade = str_replace("%%POINTS%%", $points, $catgrade);
			$catgrade = str_replace("%%PERCENTAGE%%", $percent, $catgrade);
			$catgrade = str_replace("%%GTITLE%%", @$grade_obj->gtitle, $catgrade);
			$catgrade = str_replace("%%GDESC%%", wpautop(stripslashes(@$grade_obj->gdescription)), $catgrade);
			
			// add to $catgrades
			$catgrades[] = array("percent"=>$percent, 'points'=>$points, "html"=>$catgrade, "name"=>$cat->name);
		}
		
		// should we reorder?		
		if(!empty($advanced_settings['gradecat_order']) and $advanced_settings['gradecat_order']!= 'same') {
			$catgrades = self :: reorder_catgrades($catgrades, $advanced_settings['gradecat_order']);
		} 
		
		$catgrades_str = '';
		foreach($catgrades as $catgrade) {
			$catgrades_str .= $catgrade['html'].' ';
		}
		return $catgrades_str;
	}
	
	// gets the proper grades for a quiz based on whether it uses default or its own grades
	static function get_grades($exam, $cat_id = 0) {
		global $wpdb;
		
		$grades_quiz_id = $exam->reuse_default_grades ? 0 : $exam->ID;
		if($exam->reuse_default_grades) {
			$grades_quiz_id = 0;
			$grade_type_sql = $wpdb->prepare(" AND percentage_based = %d ", $exam->grades_by_percent); 
		}
		else {
			$grades_quiz_id = $exam->ID;
			$grade_type_sql = '';
		}
		
		$grades = $wpdb->get_results(" SELECT * FROM `".WATUPRO_GRADES."` 
			WHERE exam_id=$grades_quiz_id AND cat_id=$cat_id $grade_type_sql 
			ORDER BY gfrom DESC");
			
		return $grades;	
	} // end get_grades
	
	// reorder %%CATGRADES%% depending to user selection
	static function reorder_catgrades($catgrades, $order) {
		if($order == "best") usort($catgrades, array(__CLASS__, 'sort_catgrades_best'));
		else usort($catgrades, array(__CLASS__, 'sort_catgrades_worst'));
		return $catgrades;
	}
	
	// sort catgrades best on top
	static function sort_catgrades_best($cat2, $cat1) {
		if($cat1['percent'] == $cat2['percent'] and $cat1['points'] == $cat2['points']) return 0;
		
		// only in this case we'll check points
		if($cat1['percent'] == $cat2['percent']) {
			return ($cat1['points'] < $cat2['points']) ? -1 : 1;
		}
		
		// percent not equal, so we'll compare by percent
		return ($cat1['percent'] < $cat2['percent']) ? -1 : 1;
	} // end sort best
	
		// sort cagrades worst on top
	static function sort_catgrades_worst($cat1, $cat2) {
		return self :: sort_catgrades_best($cat2, $cat1);
	} // end sort worst
}