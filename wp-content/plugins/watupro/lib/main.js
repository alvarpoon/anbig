// main-min.js minified by http://jscompress.com/
var WatuPRO={};
WatuPRO.forceSubmit = false; // used in the timer
WatuPRO.confirmOnSubmit = false; // whether to request confirmation when exam is submitted
WatuPRO.dontPromtUnanswered = false; // whether to prompt the user for unanswered question
WatuPRO.dontScroll = false; // whether to auto-scroll as user goes from page to page
WatuPRO.inCategoryPages = false;

WatuPRO.changeQCat = function(item) {
	if(item.value=="-1") jQuery("#newCat").show();
	else jQuery("#newCat").hide();
}

// initialize vars
WatuPRO.current_question = 1;
WatuPRO.total_questions = 0;
WatuPRO.mode = "show";

WatuPRO.checkAnswer = function(e, questionID) {
	this.answered = false;
	var questionID = questionID || WatuPRO.qArr[WatuPRO.current_question-1];
    
  this.answered = this.isAnswered(questionID); 
  
	if(!this.answered && e) {		
		// if required, don't let go further
		if(jQuery.inArray(questionID, WatuPRO.requiredIDs)!=-1) {
			alert(watupro_i18n.answering_required);
			return false;
		}		
		
		if(!this.dontPromtUnanswered && !confirm(watupro_i18n.did_not_answer)) {			
			e.preventDefault();
			e.stopPropagation();
			return false;
		}
	}
	return true;
}

// checks if a question is answered
WatuPRO.isAnswered = function(questionID) {
	var isAnswered = false;
	if(questionID==0) return true;
	var answerType = jQuery('#answerType'+questionID).val();	
	
	if(answerType == 'sort') return true; // sorting are always answered in some way
	if(answerType == 'matrix') {
		isAnswered = true;
		jQuery('.answerof-' + questionID).each( function(){
				if( jQuery(this).val() == '') isAnswered = false;
		}); 
		if(isAnswered) return true; // all are non-empty
	}
		
	if(answerType=='textarea') {
      // in this case it's answered in the textarea  - checking for WP-editor for the future, not currently supported      
      if(jQuery('#textarea_q_'+questionID).attr('class') == 'wp-editor-area') {
    		if(tinyMCE.get('textarea_q_'+questionID).getContent()) return true;
    	}
    	else if(jQuery("#textarea_q_"+questionID).val()!="") return true;    	
  }

	// now browse through these with multiple answers
	jQuery(".answerof-" + questionID).each(function(i) {
		if(answerType=='radio' || answerType=='checkbox') {			
			if(this.checked) isAnswered=true;
		}
		
		if(answerType=='gaps') {
			if(this.value) isAnswered=true;
		}		
	});
	
	return isAnswered;
}

// will serve for next and previous at the same time
WatuPRO.nextQuestion = function(e, dir, gotoQuestion) {
	var dir = dir || 'next';
	var gotoQuestion = gotoQuestion || 0;
	
	if(dir=='next') {
		if(!WatuPRO.checkAnswer(e)) return false;
	}
	
	this.stopAudios();
	
	// back to top	only if the page is scrolled a bit already
	if(!WatuPRO.dontScroll && dir != 'goto' && jQuery('body').scrollTop() > 250) {	
		jQuery('html, body').animate({
	   		scrollTop: jQuery('#watupro_quiz').offset().top -100
	   }, 100);   
	}   

	if(!this.inCategoryPages) jQuery("#question-" + WatuPRO.current_question).hide();

   questionID=jQuery("#qID_"+WatuPRO.current_question).val();	
	
	if(dir=='next') WatuPRO.current_question++;
	else if(dir == 'goto') WatuPRO.current_question = gotoQuestion;
	else WatuPRO.current_question--;
	
	jQuery("#question-" + WatuPRO.current_question).show();
	
	this.hilitePage(WatuPRO.current_question, this.answered);	

	// show/hide next/submit button
	if(WatuPRO.total_questions <= WatuPRO.current_question) {		
		jQuery("#next-question").hide();		
		jQuery('#action-button').show();
		if(jQuery('#WTPReCaptcha').length) jQuery('#WTPReCaptcha').show(); 
	}
	else {
		jQuery("#next-question").show();		
		if(jQuery('#WTPReCaptcha').length) jQuery('#WTPReCaptcha').hide();
	}
	
	// show/hide previous button
	if(WatuPRO.current_question>1) jQuery('#prev-question').show();
	else jQuery('#prev-question').hide();
	
	// show/hide liveResult toggle if any
	if(jQuery('#questionWrap-'+WatuPRO.current_question).is(':hidden')) {
		jQuery('#liveResultBtn').hide();
	} else {
		if(jQuery('#liveResultBtn').length)  jQuery('#liveResultBtn').show();
	}
	
	// in the backend call ajax to store incomplete taking
	if(!WatuPRO.store_progress) return false;
	var data = {"exam_id": WatuPRO.exam_id, "question_id": questionID, 'action': 'watupro_store_details', 'watupro_questions': jQuery('#quiz-'+WatuPRO.exam_id+' input[name=watupro_questions]').val(), "current_question" : WatuPRO.current_question};
	data=WatuPRO.completeData(data);
	jQuery.post(WatuPRO.siteURL, data);
}

// go to specific question (from the paginator)
WatuPRO.goto = function(e, j) {
	// highlight the buttons
	var questionID=jQuery("#qID_"+WatuPRO.current_question).val();	
	var isAnswered = this.isAnswered(questionID);
	this.hilitePage(j, isAnswered);	
	this.nextQuestion(e, 'goto', j);
	
	if(this.inCategoryPages) {
		// get current category page
		var curCatPage = jQuery('#question-' + WatuPRO.current_question).parent().attr('id');
		curCatPage = curCatPage.replace('catDiv','');
		this.curCatPage = parseInt(curCatPage) + 1; // always go "previous"		
		var numPages = jQuery('.watupro_catpage').length;
		this.nextCategory(numPages, false, true);		
	}		
}

// mark the position on the paginator
WatuPRO.hilitePage = function(j, isAnswered) {
	if(jQuery('ul.watupro-paginator').length == 0) return false;	
	
	if(isAnswered) {
		jQuery('ul.watupro-paginator li.active').removeClass('unanswered');
		jQuery('ul.watupro-paginator li.active').addClass('answered');
	} else {
		jQuery('ul.watupro-paginator li.active').addClass('unanswered');
		jQuery('ul.watupro-paginator li.active').removeClass('answered');
	}
	
	jQuery('ul.watupro-paginator li.active').removeClass('active');
	jQuery('#WatuPROPagination'+j).addClass('active');		
}

// hilite the whole paginator
WatuPRO.hilitePaginator = function(numQuestions) {
	for(i=1; i<=numQuestions; i++) {
		var questionID=jQuery("#qID_"+i).val();	
		var isAnswered = this.isAnswered(questionID);		
		this.hilitePage(i+1, isAnswered);	
	}
}

// final submit exam method
// examMode - 1 is single page, 2 per category, 0 - per question
WatuPRO.submitResult = function(e) {   
	// if we are on paginated quiz and not on the last page, ask if you are sure to submit	
	var okToSubmit = true;	
	this.curCatPage = this.curCatPage || 1;

	if(this.examMode == 0 && this.total_questions > this.current_question) okToSubmit = false;
	if(this.examMode == 2 && this.curCatPage < this.numCats) okToSubmit = false;
	
	// any questions marked for review?
	if(!WatuPRO.forceSubmit) {
		try {
			// this function is in mark-review.js and exists only when flag for review is allowed
			if(!watuproCheckPendingReview()) return false; 
		}
		catch(err) {/*alert(err);*/};	
	}
	
	if(!WatuPRO.forceSubmit && !okToSubmit && !confirm(watupro_i18n.not_last_page)) return false;
	
	// requires confirmation on submit?
	if(!WatuPRO.forceSubmit && okToSubmit && WatuPRO.confirmOnSubmit) {
		if(!confirm(watupro_i18n.confirm_submit)) return false;
	}
	
	// check for missed required questions
	if(!WatuPRO.forceSubmit) {
		for(i=0; i<WatuPRO.requiredIDs.length; i++) {			 	
			 if(!this.isAnswered(WatuPRO.requiredIDs[i])) {
			 		alert(watupro_i18n.missed_required_question);
			 		return false;
			 }
		}  	
	}
		
	// if recapctha is there we have to make sure it's shown
	if(jQuery('#WTPReCaptcha').length && !jQuery('#WTPReCaptcha').is(':visible')) {
		alert(watupro_i18n.complete_captcha);
		jQuery('#WTPReCaptcha').show();
		return false;
	}
	
	// if name/email is asked for, it shouldn't be empty
	if(!this.validateEmailName()) return false;

	// hide timer when submitting
	if(jQuery('#timerDiv').length>0) {
		jQuery('#timerDiv').hide();
		clearTimeout(WatuPRO.timerID);
	}
	
	// all OK, let's hide the form
	jQuery('#quiz-'+WatuPRO.exam_id).hide();
	jQuery('#submittingExam'+WatuPRO.exam_id).show();
	jQuery('html, body').animate({
   		scrollTop: jQuery('#watupro_quiz').offset().top - 50
   	}, 1000);   
	
	// change text and disable submit button
	jQuery("#action-button").val(watupro_i18n.please_wait);
	jQuery("#action-button").attr("disabled", true);
	
	var data = {"action":'watupro_submit', "quiz_id": this.exam_id, 'question_id[]': this.qArr,		
		"watupro_questions":  jQuery('#quiz-'+this.exam_id+' input[name=watupro_questions]').val(),
		"post_id" : this.post_id};		
	data = this.completeData(data);
	
	data['start_time']=jQuery('#startTime').val();
	
	// no ajax? In this case only return true to allow submitting the form	
	if(e && e.no_ajax && e.no_ajax.value == 1) return true;	
	
	// if captcha is available, add to data
	if(jQuery('#WTPReCaptcha').length>0) {
		jQuery('#quiz-'+WatuPRO.exam_id).show();
		data['recaptcha_challenge_field'] = jQuery('#quiz-' + WatuPRO.exam_id + ' input[name=recaptcha_challenge_field]').val();
		data['recaptcha_response_field'] = jQuery('#quiz-' + WatuPRO.exam_id + ' input[name=recaptcha_response_field]').val();
	}
	
	// don't do ajax call if no_ajax
	if(!e || !e.no_ajax || e.no_ajax.value != 1) {
		try{
		    jQuery.ajax({ "type": 'POST', "url": this.siteURL, "data": data, "success": WatuPRO.success, "error": WatuPRO.errHandle, "cache": false, dataType: "text"  });
		}catch(err){ alert(err)}
	}
}

// adds the question answers to data
WatuPRO.completeData = function(data) {
   for(x=0; x<WatuPRO.qArr.length; x++) {
    var questionID = WatuPRO.qArr[x];  
		var ansgroup = '.answerof-'+WatuPRO.qArr[x];
		var fieldName = 'answer-'+WatuPRO.qArr[x];
		var ansvalues= Array();
		var i=0;
    var answerType = jQuery('#answerType'+questionID).val();
    
    if(answerType == 'textarea') {
    	if(jQuery('#textarea_q_'+WatuPRO.qArr[x]).attr('class') == 'wp-editor-area') {
    		ansvalues[0]=tinyMCE.get('textarea_q_'+WatuPRO.qArr[x]).getContent()
    	}
    	else ansvalues[0]=jQuery('#textarea_q_'+WatuPRO.qArr[x]).val();    	
    }    
	  else {	  	
	  	jQuery(ansgroup).each( function(){
				if( jQuery(this).is(':checked') || jQuery(this).is(':selected') || answerType=='gaps' || answerType=='sort' || answerType=='matrix') {
					ansvalues[i] = this.value;
					i++;
				}
			}); 
	  }  
		
		data[fieldName+'[]'] = ansvalues;
		
		// user feedback?
		if(jQuery('#watuproUserFeedback' + questionID).length) {
			var feedback = jQuery('#watuproUserFeedback' + questionID).val();
			data['feedback-' + questionID] = feedback;
		}
		
		// get hints. For now lets use whole hints. If later this causes a problem we'll move to hints number and get contents on server
		var hints = '';
		if(jQuery('#questionHints'+questionID).length	) hints = jQuery('#questionHints'+questionID).html();
		data['question_' + questionID + '_hints'] = hints;
	} // end foreach question
	
	// user email if any	
	if(jQuery('#watuproTakerEmail' + WatuPRO.exam_id).length) data['taker_email'] = jQuery('#watuproTakerEmail' + WatuPRO.exam_id).val();
	if(jQuery('#watuproTakerName' + WatuPRO.exam_id).length) data['taker_name'] = jQuery('#watuproTakerName' + WatuPRO.exam_id).val();
	if(jQuery('#watuproTakerPhone' + WatuPRO.exam_id).length) data['taker_phone'] = jQuery('#watuproTakerPhone' + WatuPRO.exam_id).val();
	if(jQuery('#watuproTakerCompany' + WatuPRO.exam_id).length) data['taker_company'] = jQuery('#watuproTakerCompany' + WatuPRO.exam_id).val();
	
	return data;
}

WatuPRO.success = function(r) {  
	 // first check for recaptcha error, if yes, do not replace the HTML
	 // but display the error in alert and return false;
	 if(r.indexOf('WATUPRO_CAPTCHA:::')>-1) {
	 		parts = r.split(":::");
	 		alert(parts[1]);
	 		jQuery("#action-button").val(watupro_i18n.try_again);
			jQuery("#action-button").removeAttr("disabled");
	 		return false;
	 }
	 
	 // redirect?
	 if(r.indexOf('WATUPRO_REDIRECT:::')>-1) {
	 		parts = r.split(":::");
	 		window.location = parts[1];
	 		return true;
	 }

   jQuery('#watupro_quiz').html(r); 
   
   // parse mathjax
   if (typeof MathJax != 'undefined') MathJax.Hub.Queue(["Typeset",MathJax.Hub,"watupro_quiz"]);
   
   // compatibility with the simple designer plugin
   jQuery('.wtpsd-category-tabs').hide();
}

WatuPRO.errHandle = function(xhr, msg){ 
	jQuery('#watupro_quiz').html('Error Occured:'+msg+" "+xhr.statusText);
	jQuery("#action-button").val(watupro_i18n.try_again);
	jQuery("#action-button").removeAttr("disabled");
}

// initialization
WatuPRO.initWatu = function() {	
	WatuPRO.total_questions = jQuery(".watu-question").length;
	
	// different behavior if we have preloaded page
	if(!WatuPRO.pagePreLoaded) {
		jQuery("#question-1").show();		
	} else WatuPRO.goto(null, WatuPRO.current_question);

	if(WatuPRO.total_questions == 1) {		
		jQuery("#next-question").hide();
		jQuery("#prev-question").hide();
		jQuery("#show-answer").hide();

	} else {
		//jQuery("#next-question").click(WatuPRO.nextQuestion);
	}
}

WatuPRO.takingDetails = function(id, adminURL) {
	adminURL = adminURL || "";
	tb_show(watupro_i18n.taking_details, adminURL + "admin-ajax.php?action=watupro_taking_details&id="+id, adminURL + "admin-ajax.php");
}

// show next page when quiz is paginated per category
WatuPRO.nextCategory = function(numCats, dir, noHiliteQuestion) {	
	this.curCatPage = this.curCatPage || 1;
	noHiliteQuestion = noHiliteQuestion || 0;
	   
	// check for missed required questions
	if(!WatuPRO.forceSubmit) {
		for(i=0; i<WatuPRO.requiredIDs.length; i++) {		
			// is this question in the currently displayed category?
			if(!jQuery('#catDiv' + this.curCatPage + ' #watupro-required-question-' + 	WatuPRO.requiredIDs[i]).length) continue;
			 	
			 if(!this.isAnswered(WatuPRO.requiredIDs[i])) {
			 		alert(watupro_i18n.missed_required_question);
			 		return false;
			 }
		}  	
	}
	
	this.stopAudios();

	 if(dir) this.curCatPage++;
	 else this.curCatPage--;
	 
	 jQuery('.watupro_catpage').hide();	
	 jQuery('#catDiv' + this.curCatPage).show();	 
	 
	 if(this.curCatPage >= numCats) {	 	  
	 	  jQuery('#watuproNextCatButton').hide();
	 	  jQuery('#action-button').show();
	 	  if(jQuery('#WTPReCaptcha').length) jQuery('#WTPReCaptcha').show(); 
	 }	 
	 else {	 		
	 	  jQuery('#watuproNextCatButton').show();
	 	  if(jQuery('#WTPReCaptcha').length) jQuery('#WTPReCaptcha').hide(); 
	 }
	 
	 if(this.curCatPage <= 1) jQuery('#watuproPrevCatButton').hide();
	 else jQuery('#watuproPrevCatButton').show();
	 
	 if(!WatuPRO.dontScroll) {
		 jQuery('html, body').animate({
	   		scrollTop: jQuery('#watupro_quiz').offset().top - 50
	   	}, 1000);   
	 } 	
	 
	 //this.inCategoryPages = true;
	 //this.hilitePage(this.curCatPage, false);	
	 // if paginator is available, let's figure out the current 1st question and move the paginator there
	 if(jQuery('ul.watupro-paginator').length && !noHiliteQuestion) {	 	
	 	var curQuestionDiv = jQuery( "#catDiv" + this.curCatPage +" div.watu-question:first" ).attr('id');	 	
	 	var parts = curQuestionDiv.split('-');
	 	var qNum = parts[1];
	 	jQuery('ul.watupro-paginator li.active').removeClass('active');
		jQuery('#WatuPROPagination'+qNum).addClass('active');		
	 }
	 
	 if(this.store_progress) this.saveResult(false);
}

// displays result immediatelly after replying
WatuPRO.liveResult = function() {
	questionID=jQuery("#qID_"+WatuPRO.current_question).val();	
	if(!WatuPRO.isAnswered(questionID)) {
		alert(watupro_i18n.please_answer);
		return false;
	}	
	
	jQuery('#questionWrap-'+WatuPRO.current_question).hide();
	jQuery('#liveResult-'+WatuPRO.current_question).show();
	jQuery('#liveResultBtn').hide();
	
	// now send ajax request and load the result
	var data = {"action":'watupro_liveresult', "quiz_id": WatuPRO.exam_id, 'question_id': questionID, 
		'question_num': WatuPRO.current_question, "watupro_questions":  jQuery('#quiz-'+WatuPRO.exam_id+' input[name=watupro_questions]').val() };
	data=WatuPRO.completeData(data);
	
	jQuery.post(WatuPRO.siteURL, data, function(msg){
	 	jQuery('#liveResult-'+WatuPRO.current_question).html(msg);
	 	// parse mathjax
   	if (typeof MathJax != 'undefined') MathJax.Hub.Queue(["Typeset",MathJax.Hub,"liveResult-"+WatuPRO.current_question]);
   	
   	// if the question is marked for review, unmark
   	try { watuproUnmarkReview(questionID); } catch(err) {};
	});
}

// checks for maximum allowed selections
WatuPRO.maxSelections = function(id, num, chk) {
	// count the current selected items
	var cnt = jQuery(".answerof-"+id+":checked").length;
	if(cnt > num) {
		chk.checked = false;
		return false;
	}
	
	return true;
}

WatuPRO.saveResult = function(e) {
	data = {'action' : 'watupro_store_all', 'question_ids' : this.qArr, "exam_id": this.exam_id, 'watupro_questions': jQuery('#quiz-'+this.exam_id+' input[name=watupro_questions]').val()};
	data=this.completeData(data);
	jQuery.post(WatuPRO.siteURL, data, function(msg){
	 	if(e) alert(watupro_i18n.selections_saved);
	});
}

// question hints
WatuPRO.getHints = function(qid) {
	var numHints =  jQuery('#questionHints' + qid + ' .watupro-hint').length; // num hints shown so far in the question
	var numHintsTotal = jQuery('div.watupro-hint').length; // num hints shown so far in the whole quiz
	data = {'action' : 'watupro_get_hints', 'qid': qid, "exam_id": this.exam_id, "num_hints" : numHints, "num_hints_total" : numHintsTotal};
	
	jQuery.post(WatuPRO.siteURL, data, function(msg) {
		parts = msg.split("|WATUPRO|");
		if(parts[0] == 'ERROR') alert(parts[1]);		
		else jQuery('#questionHints' + qid).append(parts[1]);
		if(parts[2] && parts[2] == 'nomorehints') jQuery('#questionHintLink' + qid).hide();
		WatuPRO.saveResult(false); // save result so the revealed hint is stored as seen
	});
} // end getHints

// start button function 
WatuPRO.startButton = function() {
	if(!WatuPRO.validateEmailName(true)) return false;
	
	// no ajax, but there is contact data requested in the beginning? In this case the data is outside the form and we have to add it
	if(jQuery('#watuproContactDetails-' + WatuPRO.exam_id + '-start').length 
		&& jQuery('#quiz-' + WatuPRO.exam_id + ' input[name=no_ajax]').length
		&& jQuery('#quiz-' + WatuPRO.exam_id + ' input[name=no_ajax]').val() == 1) {			
			jQuery('#watuproContactDetails-' + WatuPRO.exam_id + '-start input').each(function(i, fld){
				fld.type = 'hidden';
				jQuery('#quiz-' + WatuPRO.exam_id).append(fld);						
			});			
	}		
	
	jQuery('#quiz-' + this.exam_id).show();
	jQuery('#description-quiz-' + this.exam_id).hide();
}

// validate email and name for quizzes that have such required fields
WatuPRO.validateEmailName = function(skipAutoGenerated) {
	if(WatuPRO.forceSubmit) return true;
	
	// if we are at the end of the quiz and there is contact data requested there, we have to show it
	// instead of submitting the quiz
	if(jQuery('#watuproContactDetails-' + WatuPRO.exam_id + '-end').length) {
		// when this happens and we have skipAutoGenerated means we come from button, but request is at the end
		// so we should not verify
		if(skipAutoGenerated) return true;
		
		if(jQuery('#watuproContactDetails-' + WatuPRO.exam_id + '-end').is(':hidden')) {
			// move to last page
			if(this.examMode == 0) this.current_question = this.total_questions;
		   if(this.examMode == 2) this.curCatPage = this.numCats;			
			
			// hide paginator if any
			jQuery('#quiz-' + WatuPRO.exam_id +' .watupro-paginator-wrap').hide();
			
			// hide questions
			jQuery('#quiz-' + WatuPRO.exam_id + ' div.watu-question').hide();
			
			// hide buttons
			jQuery('#quiz-' + WatuPRO.exam_id + ' input[type=button]').not('#action-button').hide();
			
			// show the div
			jQuery('#watuproContactDetails-' + WatuPRO.exam_id + '-end').show('slow');
			return false;
		}
	}
		
	// this shows whether we have to check the auto-generated email field
	var skipAutoGenerated = skipAutoGenerated | false;

	// if email is asked for, it shouldn't be empty
	if(jQuery('#watuproTakerEmail' + WatuPRO.exam_id).length) {		
		var emailVal = jQuery('#watuproTakerEmail' + WatuPRO.exam_id).val();
		if( (emailVal == '' || emailVal.indexOf('@') < 0 || emailVal.indexOf('.') < 1) 
			&& !this.emailIsNotRequired && !jQuery('#watuproTakerEmail' + WatuPRO.exam_id).hasClass('optional')
			&& (!skipAutoGenerated || !jQuery('#watuproTakerEmail' + WatuPRO.exam_id).hasClass('watupro-autogenerated'))) {
			alert(watupro_i18n.email_required);
			jQuery('#watuproTakerEmail' + WatuPRO.exam_id).focus();
			return false;
		}
	}
	
	// if name is asked for, it shouldn't be empty
	if(jQuery('#watuproTakerName' + WatuPRO.exam_id).length) {
		var nameVal = jQuery('#watuproTakerName' + WatuPRO.exam_id).val();
		if( nameVal == '' && !jQuery('#watuproTakerName' + WatuPRO.exam_id).hasClass('optional')) {
			alert(watupro_i18n.name_required);
			jQuery('#watuproTakerName' + WatuPRO.exam_id).focus();
			return false;
		}
	}
	
	// any other required fields that were empty?
	var canSubmit = true;
	jQuery('div.watupro-ask-for-contact-quiz-' + WatuPRO.exam_id + ' input.watupro-contact-required').each(function(i, obj){		
		if(obj.value == '') {			
			alert(watupro_i18n.field_required);
			obj.focus();
			canSubmit = false;
			return false;
		}
	});	
	if(!canSubmit) return false;
	
	return true;
}

WatuPRO.stopAudios = function() {
	// stop any audio players
	var audios = jQuery(".watu-question audio");
	if(audios) {
		for(i=0; i < audios.length; i++) {
			audios[i].pause();
		}
	}	
}

/********************************************************************************/
// Timer related functions
WatuPRO.InitializeTimer = function(timeLimit, examID, showQuestions) {	
	if(showQuestions && !WatuPRO.validateEmailName(true)) return false;	
	
	if(showQuestions) {
		jQuery('#watuproTimerForm'+examID+' input[name=watupro_start_timer]').val(1);
		
		// if there are email and name copy them to this form 
		if(jQuery('#watuproTakerEmail' + WatuPRO.exam_id).length) jQuery('#watuproTimerForm'+examID+' input[name=watupro_taker_email]').val(jQuery('#watuproTakerEmail' + WatuPRO.exam_id).val());
		if(jQuery('#watuproTakerName' + WatuPRO.exam_id).length) jQuery('#watuproTimerForm'+examID+' input[name=watupro_taker_name]').val(jQuery('#watuproTakerName' + WatuPRO.exam_id).val());		
		if(jQuery('#watuproTakerPhone' + WatuPRO.exam_id).length) jQuery('#watuproTimerForm'+examID+' input[name=watupro_taker_phone]').val(jQuery('#watuproTakerPhone' + WatuPRO.exam_id).val());
		if(jQuery('#watuproTakerCompany' + WatuPRO.exam_id).length) jQuery('#watuproTimerForm'+examID+' input[name=watupro_taker_company]').val(jQuery('#watuproTakerCompany' + WatuPRO.exam_id).val());
		
		document.getElementById('watuproTimerForm'+examID).submit();
		return false;
	}
	
	// make ajax call for two things:
	// 1. to get the server time
	// 2. if the user is logged in, to set it as their variable
	data={exam_id: WatuPRO.exam_id, 'action':'watupro_initialize_timer'};
	jQuery.post(WatuPRO.siteURL, data, function(msg){
		parts=msg.split("<!--WATUPRO_TIME-->");		
		jQuery('#startTime').val(parts[1]);
	});
	
    WatuPRO.secs = timeLimit;
    WatuPRO.StopTheClock();
    WatuPRO.StartTheTimer();	
    
    // scroll to the timer div
    jQuery('html, body').animate({
        scrollTop: jQuery("#timerDiv").offset().top - 100
    }, 500);
}

WatuPRO.StopTheClock = function() {
    if(WatuPRO.timerRunning);
    clearTimeout(WatuPRO.timerID);
    WatuPRO.timerRunning = false;
}

WatuPRO.StartTheTimer = function() {
    if (WatuPRO.secs<=0) {
        WatuPRO.StopTheClock();
        document.getElementById('timerDiv').innerHTML="<h2 style='color:red';>" + watupro_i18n.time_over + "</h2>";
        WatuPRO.forceSubmit = true;
				WatuPRO.submitResult();
    }
    else {
		// turn seconds into minutes and seconds
		if(WatuPRO.secs<60) secsText=WatuPRO.secs+" " + watupro_i18n.seconds;
		else {
			var secondsLeft=Math.round(WatuPRO.secs%60);
			
			var mins=Math.round((WatuPRO.secs-secondsLeft)/60);
		
			if(mins<60)	{
				secsText=mins+" " + watupro_i18n.minutes_and + " "+secondsLeft+" " + watupro_i18n.seconds;
			}
			else {
				var minsLeft=mins%60;
				var hours=(mins-minsLeft)/60;
								
				secsText=hours+watupro_i18n.hours+" "+minsLeft+" " +watupro_i18n.minutes_and+ " "
					+secondsLeft+" "+watupro_i18n.seconds;
			}			
		}

    document.getElementById('timerDiv').innerHTML = watupro_i18n.time_left + " " + secsText;
    WatuPRO.secs = WatuPRO.secs - 1;
    WatuPRO.timerRunning = true;
    WatuPRO.timerID = self.setTimeout("WatuPRO.StartTheTimer()", WatuPRO.delay);
  }
}
// end timer related functions
/**********************************************************************************/

jQuery(document).ready(WatuPRO.initWatu);