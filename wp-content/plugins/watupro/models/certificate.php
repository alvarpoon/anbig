<?php
class WatuPROCertificate {
	// returns certificate link and inserts the certificate in user-certificates table
	static function assign($exam, $taking_id, $certificate_id, $user_id) {
		global $wpdb;		
		
		// select certificate
		$cert = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_CERTIFICATES." WHERE ID=%d", $certificate_id));
		if(empty($cert)) return "";
		
		if(!empty($cert->require_approval)) {
			$pending_approval = 1;
			$certificate_text = "";
			if($cert->require_approval_notify_admin) self :: pending_approval_notify($cert, $user_id, $exam);
		}
		else {
			$certificate_text = "<p>".__('You can now ', 'watupro')."<a href='".site_url("?watupro_view_certificate=1&taking_id=$taking_id&id=".$certificate_id)."' target='_blank'>".__('print your certificate', 'watupro')."</a></p>";
			$pending_approval = 0;
		}
		
		// select quiz ID
		$quiz_id = $wpdb->get_var($wpdb->prepare("SELECT exam_id FROM ".WATUPRO_TAKEN_EXAMS." 
			WHERE ID=%d", $taking_id));
		
		// delete any previous records for this user
		$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_USER_CERTIFICATES." 
			WHERE user_id=%d AND certificate_id = %d AND exam_id=%d", $user_id, $certificate_id, $quiz_id));	
	       
	   // store in user certificates
	   $sql = "INSERT INTO ".WATUPRO_USER_CERTIFICATES." (user_id, certificate_id, exam_id, taking_id, pending_approval) 
	    	VALUES (%d, %d, %d, %d, %d) ";
	   $wpdb->query($wpdb->prepare($sql, $user_id, $certificate_id, $exam->ID, $taking_id, $pending_approval));
    
 	   return $certificate_text;
	}
	
	// send notification email to admin when someone earns a certificate that requires approval
	static function pending_approval_notify($cert, $user_id, $exam) {
		global $wpdb;
		$user = get_userdata($user_id);
		
		$subject = __('A certificate is earned and is pending approval.', 'watupro');
		$message = __('The user "%%user-name%%" has earned the certificate "%%certificate-name%%".  
		To view users who are pending approvals on this certificate visit %%url%%', 'watupro');
		$message = str_replace('%%user-name%%', $user->user_nicename, $message);
		$message = str_replace('%%certificate-name%%', $cert->title, $message); 
		$message = str_replace('%%url%%', admin_url('admin.php?page=watupro_user_certificates&id='.$cert->ID), $message);
		
		// send email
		$admin_email = watupro_admin_email();
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf8' . "\r\n";
		$headers .= 'From: '. $admin_email . "\r\n";		
		// echo "$admin_email, $subject, $message";
		wp_mail($admin_email, $subject, $message, $headers);
	}
	
	// sends approval notification to the user when their assigned certificate is approved
	static function approval_notify($certificate, $user_certificate_id) {
		global $wpdb; 
		
		// select user certificate along with taking date
		$user_certificate = $wpdb->get_row($wpdb->prepare("SELECT tUC.*, tT.date as date FROM ".WATUPRO_USER_CERTIFICATES." tUC
			JOIN ".WATUPRO_TAKEN_EXAMS." tT ON tT.ID = tUC.taking_id 
			WHERE tUC.ID = %d AND tUC.certificate_id=%d", $user_certificate_id, $certificate->ID));
				
		$admin_email = watupro_admin_email();
		$user = get_userdata($user_certificate->user_id);
		$exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_EXAMS." WHERE ID=%d", $user_certificate->exam_id));		
			
		// replace email subject and contents	
		$date = date( get_option('date_format'), strtotime($user_certificate->date));
		
		$subject = str_replace('{{quiz-name}}', stripslashes($exam->name), stripslashes($certificate->approval_email_subject));
		$subject = str_replace('{{certificate}}', stripslashes($certificate->title), $subject);
		$subject = str_replace('{{date}}', $date, $subject);
		
		$message = str_replace('{{quiz-name}}', stripslashes($exam->name), stripslashes($certificate->approval_email_message));
		$message = str_replace('{{certificate}}', stripslashes($certificate->title), $message);
		$message = str_replace('{{date}}', $date, $message);
		$message = str_replace('{{url}}', site_url("?watupro_view_certificate=1&taking_id=".$user_certificate->taking_id."&id=".$certificate->ID), $message);
		
		$message = apply_filters('watupro_content', $message);
		
		// send email
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf8' . "\r\n";
		$headers .= 'From: '. $admin_email . "\r\n";		
		// echo "{$user->user_email}, $subject, $message";
		wp_mail($user->user_email, $subject, $message, $headers);
	}
}