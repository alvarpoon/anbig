<?php
// add/edit exam
function watupro_exam() {
	global $wpdb, $user_ID;	
	$multiuser_access = 'all';
	if(watupro_intel()) $multiuser_access = WatuPROIMultiUser::check_access('exams_access');
	
	if(isset($_REQUEST['submit'])) {
		// prepare advanced settings - email grades and contact info fields
		$advanced_settings = $wpdb->get_var($wpdb->prepare("SELECT advanced_settings FROM ".WATUPRO_EXAMS."
			WHERE id=%d",  @$_REQUEST['quiz']));
			
		if(!empty($advanced_settings)) $advanced_settings = unserialize( stripslashes($advanced_settings));
		else $advanced_settings = array();
		
		// email grades
		$advanced_settings['email_grades'] = @$_POST['email_grades'];
		
		// flag for review
		$advanced_settings['flag_for_review'] = @$_POST['flag_for_review'];
		
		// dont display question numbers
		$advanced_settings['dont_display_question_numbers'] = @$_POST['dont_display_question_numbers'];
		
		// contact fields	
		$advanced_settings['contact_fields'] = array();
		$advanced_settings['contact_fields']['email'] = $_POST['ask_for_email'];
		$advanced_settings['contact_fields']['email_label'] = $_POST['ask_for_email_label'];		
		$advanced_settings['contact_fields']['name'] = $_POST['ask_for_name'];
		$advanced_settings['contact_fields']['name_label'] = $_POST['ask_for_name_label'];
		$advanced_settings['contact_fields']['phone'] = $_POST['ask_for_phone'];
		$advanced_settings['contact_fields']['phone_label'] = $_POST['ask_for_phone_label'];
		$advanced_settings['contact_fields']['company'] = $_POST['ask_for_company'];
		$advanced_settings['contact_fields']['company_label'] = $_POST['ask_for_company_label'];
		
		$advanced_settings['ask_for_contact_details'] = $_POST['ask_for_contact_details'];
		
		$_POST['advanced_settings'] = serialize($advanced_settings);
		
		if($_REQUEST['action'] == 'edit') { //Update goes here
			$exam_id = $_REQUEST['quiz'];

			if($multiuser_access == 'own') {
				$editor_id = $wpdb->get_var($wpdb->prepare("SELECT editor_id FROM ".WATUPRO_EXAMS." WHERE ID=%d", $exam_id));				
				if($editor_id != $user_ID) wp_die('You can edit only your own exams','watupro');
			}					
			
			if(empty($_POST['use_different_email_output'])) $_POST['email_output']='';
			WTPExam::edit($_POST, $exam_id);
			if(!empty($_POST['auto_publish'])) watupro_auto_publish($exam_id);
			$wp_redirect = admin_url('admin.php?page=watupro_exams&message=updated');	
			
			// save advanced settings
			if($exam_id and watupro_intel()) {
				$_GET['exam_id'] = $exam_id;
				$_POST['ok'] = true;
				watupro_advanced_exam_settings();
			}
		} else {
			// add new exam
			$exam_id=WTPExam::add($_POST);			
			if($exam_id == 0 ) $wp_redirect = admin_url('admin.php?page=watupro_exams&message=fail');
			if($exam_id and !empty($_POST['auto_publish'])) watupro_auto_publish($exam_id);
			$wp_redirect = admin_url('admin.php?page=watupro_questions&message=new_quiz&quiz='.$exam_id);
		}
		
   echo "<meta http-equiv='refresh' content='0;url=$wp_redirect' />"; 
    exit;
	}
	
	$action = 'new';
	if($_REQUEST['action'] == 'edit') $action = 'edit';
	
	// global answer_display
	$answer_display=get_option('watupro_show_answers');
	// global single page display
	$single_page = get_option('watupro_single_page');
	
	$dquiz = array();
	$grades = array();
	
	if($action == 'edit') {
		$dquiz = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", $_GET['quiz']));
		$single_page = $dquiz->single_page;

		if($multiuser_access == 'own' and $dquiz->editor_id != $user_ID) wp_die('You can edit only your own exams','watupro');		
		
		$grades = WTPGrade :: get_grades($dquiz);	
		$final_screen = stripslashes($dquiz->final_screen);
		$schedule_from = $dquiz->schedule_from;
		list($schedule_from) = explode(" ", $schedule_from);
		$schedule_to = $dquiz->schedule_to;
		list($schedule_to) = explode(" ", $schedule_to);
		
		$advanced_settings = unserialize( stripslashes($dquiz->advanced_settings));		
	} else {
		$final_screen = __("<p>You have completed %%QUIZ_NAME%%.</p>\n\n<p>You scored %%SCORE%% correct out of %%TOTAL%% questions.</p>\n\n<p>You have collected %%POINTS%% points.</p>\n\n<p>Your obtained grade is <b>%%GRADE%%</b></p>\n\n<p>Your answers are shown below:</p>\n\n%%ANSWERS%%", 'watupro');
		$schedule_from = date("Y-m-d");
		$schedule_to = date("Y-m-d");
	}
	
	// select certificates if any
	$certificates=$wpdb->get_results("SELECT * FROM ".WATUPRO_CERTIFICATES." ORDER BY title");
	$cnt_certificates=sizeof($certificates);
	
	// categories if any
	$cats=$wpdb->get_results("SELECT * FROM ".WATUPRO_CATS." ORDER BY name");
	
	// select other exams
	$other_exams=$wpdb->get_results("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID!='".@$dquiz->ID."' ORDER BY name");
	
	if(watupro_intel()) {
		require_once(WATUPRO_PATH."/i/models/dependency.php");
		$dependencies = WatuPRODependency::select(@$dquiz->ID);	
	}
	
	// check if recaptcha keys are in place
	$recaptcha_public = get_option('watupro_recaptcha_public');
	$recaptcha_private = get_option('watupro_recaptcha_private');
	
	// is this quiz currently published?
	if(!empty($_GET['quiz'])) {
		$quiz_id = intval($_GET['quiz']);
		$is_published = $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE post_content LIKE '%[watupro ".$quiz_id."]%' 
				AND post_status='publish' AND post_title!=''");
	} 
	else $is_published = false;
	
	wp_enqueue_script('jquery-ui-datepicker');
	wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
	if(@file_exists(get_stylesheet_directory().'/watupro/exam_form.php')) require get_stylesheet_directory().'/watupro/exam_form.php';
	else require WATUPRO_PATH."/views/exam_form.php";
}

// list exams
function watupro_exams() {
	global $wpdb, $user_ID;
	$multiuser_access = 'all';
	if(watupro_intel()) $multiuser_access = WatuPROIMultiUser::check_access('exams_access');

	if(!empty($_REQUEST['action']) and $_REQUEST['action'] == 'delete') {		
		if($multiuser_access == 'own') {
			// make sure this is my quiz
			$quiz = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", $_GET['quiz']));
			if($quiz->editor_id != $user_ID) wp_die(__('You can delete only your own quizzes.','watupro'));
		}
		
		$wpdb->get_results("DELETE FROM ".WATUPRO_EXAMS." WHERE ID='$_GET[quiz]'");
		$wpdb->get_results("DELETE FROM ".WATUPRO_ANSWERS." WHERE question_id IN (SELECT ID FROM ".WATUPRO_QUESTIONS." WHERE exam_id='$_GET[quiz]')");
		$wpdb->get_results("DELETE FROM ".WATUPRO_QUESTIONS." WHERE exam_id='$_GET[quiz]'");		
		$wpdb->get_results("DELETE FROM ".WATUPRO_GRADES." WHERE exam_id='$_GET[quiz]'");
	}
	
	$ob = empty($_GET['ob']) ? "Q.ID" : $_GET['ob'];
	$dir = empty($_GET['dir']) ? "DESC" : $_GET['dir'];
	$odir = ($dir == 'ASC') ? 'DESC' : 'ASC';
	
	$offset = empty($_GET['offset']) ? 0 : $_GET['offset'];
	$limit_sql = $wpdb->prepare(" LIMIT %d, 50 ", $offset);
	
	// filters
	$filter_sql = $filter_params = "";
	if(isset($_GET['cat_id']) and $_GET['cat_id']!= -1) {
		$filter_sql .= $wpdb->prepare(" AND Q.cat_id = %d ", $_GET['cat_id']);
		$filter_params .= "&cat_id=$_GET[cat_id]";
	}
	if(!empty($_GET['title'])) {
		$_GET['title'] = esc_sql($_GET['title']);
		$filter_sql .= " AND Q.name LIKE '%$_GET[title]%' ";
		$filter_params .= "&title=$_GET[title]";
	}
	
	$editor_sql = ($multiuser_access == 'own') ? $wpdb->prepare(" AND Q.editor_id = %d", $user_ID) : '';
	
	$exams = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS Q.*, tC.name as cat, tU.user_login as author,
	(SELECT COUNT(*) FROM ".WATUPRO_QUESTIONS." WHERE exam_id=Q.ID) AS question_count,
	(SELECT COUNT(*) FROM ".WATUPRO_TAKEN_EXAMS." WHERE exam_id=Q.ID AND in_progress=0) AS taken
	FROM ".WATUPRO_EXAMS." AS Q LEFT JOIN ".WATUPRO_CATS." as tC ON tC.id=Q.cat_id
	LEFT JOIN {$wpdb->users} tU ON tU.ID = Q.editor_id
	WHERE Q.ID > 0 $filter_sql $editor_sql
	ORDER BY $ob $dir $limit_sql");
	
	$count = $wpdb->get_var("SELECT FOUND_ROWS()");
	
	// now select all posts that have watupro shortcode in them
	$posts=$wpdb->get_results("SELECT * FROM {$wpdb->posts} 
		WHERE post_content LIKE '%[watupro %]%'
		AND (post_status='publish' OR post_status='private')
		AND post_title!=''
		ORDER BY post_date DESC");	
		
	// match posts to exams
	foreach($exams as $cnt=>$exam) {
		foreach($posts as $post) {
			if(stristr($post->post_content,"[watupro ".$exam->ID."]")) {
				$exams[$cnt]->post=$post;			
				break;
			}
		}
	}

	// select exam categories
	$cats = $wpdb->get_results("SELECT * FROM ".WATUPRO_CATS." ORDER BY name");
	
	if(@file_exists(get_stylesheet_directory().'/watupro/exams.php')) require get_stylesheet_directory().'/watupro/exams.php';
	else require WATUPRO_PATH."/views/exams.php";
}

// open form to copy quiz
function watupro_copy_exam() {
	global $wpdb, $user_ID;
	$multiuser_access = 'all';
	if(watupro_intel()) $multiuser_access = WatuPROIMultiUser::check_access('exams_access');
	$own_sql = ($multiuser_access == 'own') ? $wpdb->prepare(" AND editor_id=%d ", $user_ID) : "";
	
	$exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", $_GET['id']));
	$grades = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".WATUPRO_GRADES." WHERE  exam_id=%d order by ID ", $exam->ID) );
	$questions = $wpdb->get_results($wpdb->prepare("SELECT cat_id, question, ID FROM ".WATUPRO_QUESTIONS." WHERE exam_id=%d ORDER BY sort_order, ID", $exam->ID));
	$cids = array(0);
	foreach($questions as $question) {
		if(!in_array($question->cat_id, $cids)) $cids[] = $question->cat_id;
	}
	$cidsql = implode(", ", $cids);
	
	// select question categories to group questions by cats
	$qcats = $wpdb->get_results("SELECT * FROM ".WATUPRO_QCATS." WHERE ID IN ($cidsql) ORDER BY name"); 
	// add Uncategorized
	$qcats[] = (object) array("ID"=>0, "name"=>__('Uncategorized', 'watupro'));
	
	$other_exams=$wpdb->get_results("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID!='".$exam->ID."' $own_sql ORDER BY name");
	
	if(!empty($_POST['copy_exam'])) {		
		try {
			$copy_to=($_POST['copy_option']=='new')?0:$_POST['copy_to'];
			WTPExam::copy($exam->ID, $copy_to);
			$_SESSION['flash'] =__("The exam was successfully copied!", 'watupro');
			watupro_redirect("admin.php?page=watupro_exams");
		}
		catch(Exception $e) {
			$error=$e->getMessage();
		}	 
	}
	
	if(@file_exists(get_stylesheet_directory().'/watupro/copy-exam-form.html.php')) require get_stylesheet_directory().'/watupro/copy-exam-form.html.php';
	else require WATUPRO_PATH."/views/copy-exam-form.html.php";
}

// replace title & meta tags on shared URLs
// called on template_redirect from init.php
function watupro_share_redirect() {
	global $post, $wpdb;
	
	if(empty($_GET['waturl'])) return false;
	
	$url = @base64_decode($_GET['waturl']); 
	list($exam_id, $tid) = explode("|", $url); 
	if(!is_numeric($exam_id) or !is_numeric($tid)) return false;
		
	// select taking
	$taking = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_TAKEN_EXAMS." WHERE ID=%d", $tid));
	if(empty($taking->grade_id)) return false;
	
	// select exam
	$shareable = $wpdb->get_var($wpdb->prepare("SELECT shareable_final_screen FROM ".WATUPRO_EXAMS." WHERE ID=%d", $taking->exam_id)); 
	if(!$shareable) return false;
	
	// select grade
	$grade = $wpdb->get_row($wpdb->prepare("SELECT gtitle, gdescription FROM ".WATUPRO_GRADES." WHERE ID=%d", $taking->grade_id));
	
	$post->post_title = stripslashes($grade->gtitle);
	$post->post_excerpt = stripslashes($taking->result);
}

// auto publish quiz in post
// some data comes directly from the $_POST to save unnecessary DB query
function watupro_auto_publish($quiz_id) {	
	$post = array('post_content' => '[watupro '.$quiz_id.']', 'post_name'=> $_POST['name'], 
		'post_title'=>$_POST['name'], 'post_status'=>'publish');
	wp_insert_post($post);
}