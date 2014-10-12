<?php
function watupro_certificates() {
	global $wpdb, $user_ID;
	wp_enqueue_style('style.css', plugins_url('/watupro/style.css'), null, '1.0');
	
	$multiuser_access = 'all';
	if(watupro_intel()) $multiuser_access = WatuPROIMultiUser::check_access('certificates_access');
	
	switch(@$_GET['do']) {
		case 'add':
			if(!empty($_POST['ok'])) {
				$wpdb->query($wpdb->prepare("INSERT INTO ".WATUPRO_CERTIFICATES." (title, html, require_approval, 
					require_approval_notify_admin, approval_notify_user, approval_email_subject, approval_email_message, editor_id)
					VALUES (%s, %s, %d, %d, %d, %s, %s, %d)", $_POST['title'], $_POST['html'], @$_POST['require_approval'], @$_POST['require_approval_notify_admin'],
					@$_POST['approval_notify_user'], $_POST['approval_email_subject'], $_POST['approval_email_message'], $user_ID));
				echo "<meta http-equiv='refresh' content='0;url=admin.php?page=watupro_certificates' />"; 
				exit;
			}
		   			
			if(@file_exists(get_stylesheet_directory().'/watupro/certificate.php')) require get_stylesheet_directory().'/watupro/certificate.php';
			else require WATUPRO_PATH."/views/certificate.php";
		break;
	
		case 'edit':
			if($multiuser_access == 'own') {
				$certificate=$wpdb->get_row($wpdb->prepare("SELECT * FROM 
					".WATUPRO_CERTIFICATES." WHERE ID=%d", $_GET['id']));
				if($certificate->editor_id != $user_ID) wp_die(__('You can manage only your own certificates', 'watupro'));	
			}		
		
			if(!empty($_POST['del'])) {
	           $wpdb->query($wpdb->prepare("DELETE FROM 
					".WATUPRO_CERTIFICATES." WHERE ID=%d", $_GET['id']));
	
				echo "<meta http-equiv='refresh' content='0;url=admin.php?page=watupro_certificates' />"; 
				exit;
			}
	
			if(!empty($_POST['ok'])) {
				$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_CERTIFICATES." SET
					title=%s, html=%s, require_approval= %d, require_approval_notify_admin=%d, approval_notify_user=%d,
					approval_email_subject=%s, approval_email_message=%s
					WHERE ID=%d", $_POST['title'], $_POST['html'], @$_POST['require_approval'], @$_POST['require_approval_notify_admin'],
					@$_POST['approval_notify_user'], $_POST['approval_email_subject'], $_POST['approval_email_message'], $_GET['id']));
				echo "<meta http-equiv='refresh' content='0;url=admin.php?page=watupro_certificates' />"; 
				exit;
			}
	
			$certificate=$wpdb->get_row($wpdb->prepare("SELECT * FROM 
					".WATUPRO_CERTIFICATES." WHERE ID=%d", $_GET['id']));
	
			if(@file_exists(get_stylesheet_directory().'/watupro/certificate.php')) require get_stylesheet_directory().'/watupro/certificate.php';
			else require WATUPRO_PATH."/views/certificate.php";
		break;
	
		default:
			if(!empty($_POST['save_pdf_settings'])) {
				update_option('watupro_generate_pdf_certificates', @$_POST['generate_pdf_certificates']);
				update_option('watupro_docraptor_test_mode', $_POST['docraptor_test_mode']);
				update_option('watupro_pdf_engine', @$_POST['pdf_engine']);
				if(!empty($_POST['docraptor_key'])) update_option('watupro_docraptor_key', $_POST['docraptor_key']);
			}		
		
			// select my certificates
			$own_sql = ($multiuser_access == 'own') ? $wpdb->prepare(" WHERE editor_id = %d ", $user_ID) : "";
			$certificates=$wpdb->get_results("SELECT * FROM ".WATUPRO_CERTIFICATES." $own_sql ORDER BY title");
				
			$generate_pdf_certificates = get_option('watupro_generate_pdf_certificates');	
			$docraptor_key = get_option('watupro_docraptor_key');
			$docraptor_test_mode = get_option('watupro_docraptor_test_mode');
			$pdf_engine = get_option('watupro_pdf_engine');
	
			if(@file_exists(get_stylesheet_directory().'/watupro/certificates.php')) require get_stylesheet_directory().'/watupro/certificates.php';
			else require WATUPRO_PATH."/views/certificates.php";
		break;
	}
}

// shows the certificates earned by a student
function watupro_my_certificates() {
	global $wpdb, $user_ID;
	
	// admin can see this for every student
	if(!empty($_GET['user_id']) and current_user_can(WATUPRO_MANAGE_CAPS)) $user_id = $_GET['user_id'];
	else $user_id = $user_ID;
	
	$user = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->users} WHERE ID=%d", $user_id));
	
	if(!empty($_GET['set_public_access'])) {
		$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_USER_CERTIFICATES." SET
			public_access=%d WHERE id=%d AND user_id=%d", $_GET['public_access'], $_GET['id'], $user_id));
		watupro_redirect("admin.php?page=watupro_my_certificates");	
	}
	
	$certificates = $wpdb->get_results($wpdb->prepare("SELECT tC.*, tE.name as exam_name, tG.gtitle as grade, 
		tT.points as points, tT.end_time as end_time, tT.id as taking_id, tUS.ID as us_id, tUS.public_access as public_access
		FROM ".WATUPRO_USER_CERTIFICATES." tUS 
		JOIN ".WATUPRO_CERTIFICATES." tC ON tUS.certificate_id = tC.ID 
		JOIN ".WATUPRO_GRADES." tG ON tG.certificate_id=tC.id 
		JOIN ".WATUPRO_TAKEN_EXAMS." tT ON tT.grade_id = tG.ID AND tT.ID = tUS.taking_id
		JOIN ".WATUPRO_EXAMS." tE ON tE.ID = tT.exam_id
		WHERE tUS.user_id = %d AND tUS.pending_approval=0 
		ORDER BY tT.ID DESC", $user_id));
		
	// cleanup duplicates - we only need certificates shown for the latest taking
	/*$final_certificates = array();	
	$certificate_ids = array();
	
	foreach($certificates as $certificate) {
		if(in_array($certificate->ID, $certificate_ids)) continue;		
		$final_certificates[] = $certificate;
		$certificate_ids[] = $certificate->ID;
	}
	
	$certificates = $final_certificates;*/
	
	if(@file_exists(get_stylesheet_directory().'/watupro/my_certificates.php')) require get_stylesheet_directory().'/watupro/my_certificates.php';
	else require WATUPRO_PATH."/views/my_certificates.php";
}


function watupro_view_certificate() {
	global $wpdb, $user_ID;
	
	// select certificate
	$certificate=$wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_CERTIFICATES." WHERE ID=%d", $_GET['id']));
	
	if(empty($certificate->ID)) {
		wp_die(__("no such certificate", "watupro"));
	}
	$output=stripslashes($certificate->html);
	
	// no taking id? only admin can see it then
	if(empty($_GET['taking_id'])) {
		if(!current_user_can(WATUPRO_MANAGE_CAPS)) 
			wp_die( __('You do not have sufficient permissions to access this page', 'watupro').' 1' );
	}
	else {
		// find taking 
		$taking=$wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_TAKEN_EXAMS."
			WHERE ID=%d", $_GET['taking_id']));

		// find user_certificate record and see if the current user is allowed to see the certificate
		$user_certificate = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_USER_CERTIFICATES."
			WHERE taking_id=%d AND certificate_id=%d AND user_id=%d", $taking->ID, $certificate->ID, $taking->user_id));
		if(empty($user_certificate)) wp_die(__('Such certificate was never earned.', 'watupro'));	
		
		if(!is_user_logged_in() and empty($user_certificate->public_access)) {
			watupro_redirect( wp_login_url(site_url("?watupro_view_certificate=1&taking_id=".$_GET['taking_id']."&id=".$_GET['id'])) );
		}
			
		if(empty($user_certificate->public_access) and ($taking->user_id!=$user_ID or $user_certificate->pending_approval) and !current_user_can(WATUPRO_MANAGE_CAPS)) {
			wp_die( __('You do not have sufficient permissions to access this page', 'watupro').' 2' );
		}
		
		$user_id = $taking->user_id;
	
		// select exam
		$exam=$wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", $taking->exam_id));
		
		// select grade
		$grade = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_GRADES." WHERE ID=%d", $taking->grade_id));
		
		$user_info=get_userdata($user_id);
		
		if(empty($taking->name)) {
			$name=(empty($user_info->first_name) or empty($user_info->last_name)) ? $user_info->display_name:
				$user_info->first_name." ".$user_info->last_name;
		}
		else $name = $taking->name;		
		
		// replace {{{name-field}}} and {{{email-field}}}
		$name_field = empty($taking->name) ? $name : $taking->name;
		$email_field = empty($taking->email) ? $user_info->user_email : $taking->email;
		$output = str_replace('{{{name-field}}}', $name_field, $output);
		$output = str_replace('{{{email-field}}}', $email_field, $output);
	
		$output=str_replace("%%GRADE%%", $taking->result, $output);
		$output=str_replace("%%GTITLE%%", $grade->gtitle, $output);
		$output=str_replace("%%GDESC%%", stripslashes($grade->gdescription), $output);
		$output=str_replace("%%QUIZ_NAME%%", $exam->name, $output);
		$output=str_replace("%%DESCRIPTION%%", stripslashes($exam->description), $output);
		$output=str_replace("%%USER_NAME%%", $name, $output);
		$output=str_replace("%%EMAIL%%", $email_field, $output);
		$output=str_replace("%%POINTS%%", $taking->points, $output);		
		$taken_date = date(get_option('date_format'), strtotime($taking->date));
	   $output=str_replace("%%DATE%%", $taken_date, $output);
	   $output=str_replace("%%ID%%", sprintf('%04d', $user_certificate->ID), $output);
		$output = watupro_parse_answerto($output, $taking->ID);	  	  	 
	   $output = apply_filters('watupro_content', $output);
	}
	
	if(get_option('watupro_generate_pdf_certificates') == "1") {
		$pdf_engine = get_option('watupro_pdf_engine');
		// $test_mode = 1;
		// generate through docRaptor
		if(empty($pdf_engine) or $pdf_engine == 'docraptor') {
			if(empty($user_certificate->pdf_output)) {			
				$api_key = get_option('watupro_docraptor_key');
				$test_mode = get_option('watupro_docraptor_test_mode');
				include_once(WATUPRO_PATH.'/lib/docraptor/DocRaptor.class.php');
				$docraptor = new DocRaptor($api_key);
				$docraptor->setDocumentContent($output)->setDocumentType('pdf')->setTest($test_mode)->setName('certificate.pdf');
				$content = $docraptor->fetchDocument();		
				
				// store in DB to avoid more queries
				$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_USER_CERTIFICATES." SET pdf_output = %s WHERE ID = %d", $content, $user_certificate->ID));
			}
			else {			
				$content = $wpdb->get_var($wpdb->prepare("SELECT BINARY pdf_output FROM ".WATUPRO_USER_CERTIFICATES." WHERE ID=%d", $user_certificate->ID));			
			}
		}
		
		if(!empty($pdf_engine) and $pdf_engine = 'pdf-bridge') {
			$output = '<html>
			<head><title>'.$certificate->title.'</title>
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /></head>
			<body>'.$output.'</body>
			</html>';
		//	die($output);
			$content = apply_filters('pdf-bridge-convert', $output);		
			echo $content;
			exit;	
		}	
		
		header("Content-Length: ".strlen($content)); 
		header('Content-type: application/pdf');
		echo $content;
		exit;
		
	} // end pdf certificate
	
	// else output HTML
	?>
	<html>
	<head><title><?php echo $certificate->title;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /></head>
	<body><?php echo $output;?></body>
	</html>
	<?php 
	exit;
}

// template redirect for viewing certificate
function watupro_certificate_redirect() {
	if(empty($_GET['watupro_view_certificate'])) return true;
	watupro_view_certificate();
}

// view and manage users who earned certificates
function watupro_user_certificates() {
	global $wpdb, $user_ID;
	
	$certificate = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_CERTIFICATES." WHERE ID=%d", $_GET['id']));
	
	// check access	
	$multiuser_access = 'all';
	if(watupro_intel()) $multiuser_access = WatuPROIMultiUser::check_access('certificates_access');
	if($multiuser_access == 'own') {
		if($certificate->editor_id != $user_ID) wp_die(__('You can manage only your own certificates', 'watupro'));	
	}	
	
	if(!empty($_GET['approve'])) {
		$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_USER_CERTIFICATES." SET pending_approval = 0 WHERE ID=%d", $_GET['user_certificate_id']));
		
		// send email to user?
		if($certificate->approval_notify_user) WatuPROCertificate :: approval_notify($certificate, $_GET['user_certificate_id']);
		
		watupro_redirect("admin.php?page=watupro_user_certificates&id=".$_GET['id']);
	}
	
	if(!empty($_GET['delete'])) {
		$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_USER_CERTIFICATES." WHERE ID=%d", $_GET['user_certificate_id']));
	}
	
	// select users
	$users = $wpdb->get_results($wpdb->prepare("SELECT tUC.ID as user_certificate_id, tU.user_nicename as user_nicename, tU.user_email as user_email, 
	tE.name as exam_name, tUC.pending_approval as pending_approval, tT.ID as taking_id, tT.date as taking_date, tT.result as taking_result,
	tE.ID as exam_id
	FROM ".WATUPRO_USER_CERTIFICATES." tUC 
	JOIN {$wpdb->users} tU ON tUC.user_id = tU.ID  
	JOIN ".WATUPRO_TAKEN_EXAMS." tT ON tT.user_id = tU.ID AND tT.ID = tUC.taking_id
	JOIN ".WATUPRO_EXAMS." tE ON tE.ID = tT.exam_id AND tE.ID = tUC.exam_id
	WHERE tUC.certificate_id=%d
	ORDER BY tT.ID DESC", $certificate->ID));
	
	$dateformat = get_option('date_format');
	
	$is_admin = true;
	wp_enqueue_script('thickbox',null,array('jquery'));
	wp_enqueue_style('thickbox.css', '/'.WPINC.'/js/thickbox/thickbox.css', null, '1.0');
	
	if(@file_exists(get_stylesheet_directory().'/watupro/users-earned-certificate.html.php')) require get_stylesheet_directory().'/watupro/users-earned-certificate.html.php';
	else require WATUPRO_PATH."/views/users-earned-certificate.html.php";
}