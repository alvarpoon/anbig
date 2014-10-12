<?php
function watupro_takings() {	
	global $wpdb, $wp_roles, $user_ID;
	$roles = $wp_roles->roles;	
	$multiuser_access = 'all';
	if(watupro_intel()) $multiuser_access = WatuPROIMultiUser::check_access('exams_access');
	
	// select user groups
	$groups=$wpdb->get_results("SELECT * FROM ".WATUPRO_GROUPS." ORDER BY name");
	
	// shows data for a taken exam
	$ob=empty($_GET['ob'])?"id":$_GET['ob'];
	$dir=!empty($_GET['dir'])?$_GET['dir']:"DESC";
	$odir=($dir=='ASC')?'DESC':'ASC';
	$offset=empty($_GET['offset'])?0:$_GET['offset'];
	
	// select exam
	$exam=$wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d",
		$_GET['exam_id']));
		
	if(!empty($_POST['cleanup']) or !empty($_POST['blankout'])) {
		if($multiuser_access == 'own' and $exam->editor_id != $user_ID) wp_die(__('You can manage only the results on exams created by you.', 'watupro'));
		
		if(!empty($_POST['cleanup'])) {		
			// now cleanup
			$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_TAKEN_EXAMS." WHERE exam_id=%d", $exam->ID));
			$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_STUDENT_ANSWERS." WHERE exam_id=%d", $exam->ID));
		}
		
		if(!empty($_POST['blankout'])) {
			$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_TAKEN_EXAMS." 
				SET details='data removed', catgrades='data removed' WHERE exam_id=%d", $exam->ID));
				
			$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_STUDENT_ANSWERS." 
				SET question_text='data removed', snapshot='data removed' WHERE exam_id=%d", $exam->ID));	
		}		
	}	
		
	// check access	
	if($multiuser_access == 'own') {
		if($exam->editor_id != $user_ID) wp_die(__('You can only view results on your own quizzes.','watupro'));
	}	
		
	// search/filter
	$filters=array();
	$joins=array();
	$filter_sql = $left_join_sql = $role_join_sql = $group_join_sql = $left_join = "";
	$join_sql="LEFT JOIN {$wpdb->users} tU ON tU.ID=tT.user_id";
	
	// add filters and joins
	
	// display name
	if(!empty($_GET['dn'])) {
		switch($_GET['dnf']) {
			case 'contains': $like="%$_GET[dn]%"; break;
			case 'starts': $like="$_GET[dn]%"; break;
			case 'ends': $like="%$_GET[dn]"; break;
			case 'equals':
			default: $like=$_GET['dn']; break;			
		}
		
		$joins[]=$wpdb->prepare(" display_name LIKE %s ", $like);
	}
	
	// email
	if(!empty($_GET['email'])) {
		switch($_GET['emailf']) {
			case 'contains': $like="%$_GET[email]%"; break;
			case 'starts': $like="$_GET[email]%"; break;
			case 'ends': $like="%$_GET[email]"; break;
			case 'equals':
			default: $like=$_GET['email']; break;			
		}
		
		$joins[]=$wpdb->prepare(" user_email LIKE %s ", $like);
		$filters[]=$wpdb->prepare(" ((user_id=0 AND email LIKE %s) OR (user_id!=0 AND user_email LIKE %s)) ", $like, $like);
		$left_join = 'LEFT'; // when email is selected, do left join because it might be without logged user
	}
	
	// WP user role - when selected role the join always becomes right join
	if(!empty($_GET['role'])) {
		$left_join = '';
		$blog_prefix = $wpdb->get_blog_prefix();
		$role_join_sql = "JOIN {$wpdb->usermeta} tUM ON tUM.user_id = tU.id 
			AND tUM.meta_key = '{$blog_prefix}capabilities' AND tUM.meta_value LIKE '%:".'"'.$_GET['role'].'"'.";%'";
	}
	
	// Watupro user group
	if(!empty($_GET['ugroup'])) {
		$left_join = '';		
		$group_join_sql = "JOIN {$wpdb->usermeta} tUM2 ON tUM2.user_id = tU.id 
			AND tUM2.meta_key = 'watupro_groups' AND tUM2.meta_value LIKE '%:".'"'.$_GET['ugroup'].'"'.";%'";
	}
	else $group_join_sql = "LEFT JOIN {$wpdb->usermeta} tUM2 ON tUM2.user_id = tU.id 
			AND tUM2.meta_key = 'watupro_groups' ";
	
	// IP
	if(!empty($_GET['ip'])) {
		switch($_GET['ipf']) {
			case 'contains': $like="%$_GET[ip]%"; break;
			case 'starts': $like="$_GET[ip]%"; break;
			case 'ends': $like="%$_GET[ip]"; break;
			case 'equals':
			default: $like=$_GET['ip']; break;			
		}
		
		$filters[]=$wpdb->prepare(" ip LIKE %s ", $like);
	}
	
	// Date
	if(!empty($_GET['date'])) {
		switch($_GET['datef']) {
			case 'after': $filters[]=$wpdb->prepare(" date>%s ", $_GET['date']); break;
			case 'before': $filters[]=$wpdb->prepare(" date<%s ", $_GET['date']); break;
			case 'equals':
			default: $filters[]=$wpdb->prepare(" date=%s ", $_GET['date']); break;
		}
	}
	
	// Points
	if(!empty($_GET['points'])) {
		switch($_GET['pointsf']) {
			case 'less': $filters[]=$wpdb->prepare(" points<%d ", $_GET['points']); break;
			case 'more': $filters[]=$wpdb->prepare(" points>%d ", $_GET['points']); break;
			case 'equals':
			default: $filters[]=$wpdb->prepare(" points=%d ", $_GET['points']); break;
		}
	}
	
	// % correct
	if(!empty($_GET['percent_correct'])) {
		switch($_GET['percentf']) {
			case 'less': $filters[]=$wpdb->prepare(" percent_correct < %d ", $_GET['percent_correct']); break;
			case 'more': $filters[]=$wpdb->prepare(" percent_correct > %d ", $_GET['percent_correct']); break;
			case 'equals':
			default: $filters[]=$wpdb->prepare(" percent_correct = %d ", $_GET['percent_correct']); break;
		}
	}
	
	// passed taking ID from the manage user-certificates page
	if(!empty($_GET['taking_id'])) {
		$filters[] = $wpdb->prepare(" tT.ID=%d ", $_GET['taking_id']);
	}
	
	// Grade
	if(!empty($_GET['grade'])) {
		$filters[]=$wpdb->prepare(" grade_id=%d ", $_GET['grade']);
	}
	
	// construct filter & join SQLs
	if(sizeof($filters)) {
		$filter_sql=" AND ".implode(" AND ", $filters);
	}
	
	if(sizeof($joins)) {
		$join_sql=" $left_join JOIN {$wpdb->users} tU ON tU.ID=tT.user_id AND "
			.implode(" AND ", $joins);
	}
	
	$limit_sql="LIMIT $offset,10";
	
	if(!empty($_GET['export'])) $limit_sql="";
		
	// select takings
	$in_progress = empty($_GET['in_progress']) ? 0 : 1; // completed or "in progress" takings 
	$q="SELECT SQL_CALC_FOUND_ROWS tT.*, tU.display_name as display_name, tU.user_email as user_email,
	tUM2.meta_value as user_groups
	FROM ".WATUPRO_TAKEN_EXAMS." tT 
	$join_sql $role_join_sql $group_join_sql
	WHERE tT.exam_id={$exam->ID} AND tT.in_progress=$in_progress $filter_sql
	ORDER BY $ob $dir $limit_sql";
	// echo $q;
	$takings=$wpdb->get_results($q);
	$count=$wpdb->get_var("SELECT FOUND_ROWS()");
	
	// fill user groups
	foreach($takings as $cnt=>$taking) {
		if(empty($taking->user_groups)) continue;
		$ugroups = unserialize($taking->user_groups);		
		$ugroup_names = array();
		foreach($groups as $group) {
			if(in_array($group->ID, $ugroups)) $ugroup_names[] = $group->name;
		}
		
		$takings[$cnt]->user_groups = implode(', ', $ugroup_names);
	} // end filling user groups info
	
	// select number of in_progress takings unless we are showing them now
	if(!$in_progress){
		$num_unfinished = $wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) FROM ".WATUPRO_TAKEN_EXAMS."
			WHERE exam_id = %d AND in_progress=1", $exam->ID));
	}	
	
	if(!empty($_GET['export'])) {
		$_record = new WTPRecord();
		$_record->export($takings, $exam);
	}
		
	// grades for the dropdown	
	$grades = WTPGrade :: get_grades($exam);	
		
	// this var will be added to links at the view
	$filters_url="dn=".@$_GET['dn']."&dnf=".@$_GET['dnf']."&email=".@$_GET['email']."&emailf=".
		@$_GET['emailf']."&ip=".@$_GET['ip']."&ipf=".@$_GET['ipf']."&date=".@$_GET['date'].
		"&datef=".@$_GET['datef']."&points=".@$_GET['points']."&pointsf=".@$_GET['pointsf'].
		"&grade=".@$_GET['grade']."&role=".@$_GET['role']."&ugroup=".@$_GET['ugroup'].
		"&percent_correct=".@$_GET['percent_correct']."&percentf=".@$_GET['percentf'];			
		
	$display_filters=(!sizeof($filters) and !sizeof($joins) and empty($role_join_sql) and empty($_GET['ugroup'])) ? false : true;	
	
	wp_enqueue_script('thickbox',null,array('jquery'));
	wp_enqueue_style('thickbox.css', '/'.WPINC.'/js/thickbox/thickbox.css', null, '1.0');
	
	if(@file_exists(get_stylesheet_directory().'/watupro/takings.php')) require get_stylesheet_directory().'/watupro/takings.php';
	else require WATUPRO_PATH."/views/takings.php";
}

function watupro_delete_taking() {
	global $wpdb;
	
	do_action('watupro_deleted_taking', $_GET['id']);
	
	$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_TAKEN_EXAMS." WHERE id=%d", $_GET['id']));
		
	// delete from student_answers
	$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_STUDENT_ANSWERS." WHERE taking_id=%d", $_GET['id']));	
	
	exit;	
}