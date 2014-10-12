<?php
function watupro_ajax() {
	switch($_POST['do']) {
		case 'mark_review':
			// mark question for review
			WatuPROQuestions :: mark_review();
		break;
	}
	exit;
}