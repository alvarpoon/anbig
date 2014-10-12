<div class="wrap">
<h1><?php echo ($action == 'new') ? sprintf(__('Add %s', 'watupro'), __('Quiz', 'watupro')) :  sprintf(__('Edit %s', 'watupro'), __('Quiz', 'watupro'))?></h1>

<?php watupro_display_alerts(); ?>

<p><a href="admin.php?page=watupro_exams"><?php _e("Back to Quizzes List", 'watupro')?></a> 
	<?php if(!empty($dquiz->ID)):?>| <a href="admin.php?page=watupro_copy_exam&id=<?php echo $dquiz->ID?>"><?php _e("Copy into another quiz", 'watupro')?></a>
	| <a href="admin.php?page=watupro_questions&quiz=<?php echo $dquiz->ID?>"><?php _e('Manage Questions', 'watupro')?></a>
	| <a href="admin.php?page=watupro_grades&quiz=<?php echo $dquiz->ID?>"><?php if(empty($dquiz->is_personality_quiz)) _e('Manage Grades', 'watupro');
	else _e('Manage Personality Types', 'watupro');?></a><?php endif;?>
</p>

<form name="post" action="admin.php?page=watupro_exam" method="post" id="post" onsubmit="return WatuPROValidateExam(this);">
<div id="poststuff">
	<h2 class="nav-tab-wrapper">
		<a class='nav-tab nav-tab-active' href='#' onclick="watuproChangeTab(this, 'namedesc');return false;"><?php _e('Name and Description', 'watupro')?></a>
		<a class='nav-tab' href='#' onclick="watuproChangeTab(this, 'settings');return false;"><?php _e('General Settings', 'watupro')?></a>
		<a class='nav-tab' href='#' onclick="watuproChangeTab(this, 'useremailsettings');return false;"><?php _e('User and Email Related Settings', 'watupro')?></a>
		<?php if(watupro_intel()):?>
			<a class='nav-tab' href='#' onclick="watuproChangeTab(this, 'intel');return false;"><?php _e('Intelligence Module Settings', 'watupro')?></a>
			<a class='nav-tab' href='#' onclick="watuproChangeTab(this, 'advanced');return false;"><?php _e('Other Advanced Settings', 'watupro')?></a>
		<?php endif;?>
		<a class='nav-tab' href='#' onclick="watuproChangeTab(this, 'finalscreen');return false;"><?php printf(__('Final Page / %s Result', 'watupro'), __('Quiz', 'watupro'))?></a>	
	</h2>
	
	<div class="postbox watupro-tab-div" id="namedesc">
		<div class="postbox" id="titlediv">
		    <h3><?php printf(__('%s Name', 'watupro'), __('Quiz', 'watupro')) ?></h3>
		    
		    <div class="inside">
		    <textarea name='name' rows="1" cols="100" id="title"><?php echo stripslashes(@$dquiz->name); ?></textarea>
		    </div>
		</div>
		<div class="inside">
			 <p><input type="checkbox" name="is_inactive" value="1" <?php if(!empty($dquiz->ID) and empty($dquiz->is_active)) echo 'checked'?>> <?php printf(__('Deactivate this %s.', 'watupro'), __('quiz', 'watupro'))?></p>   
			 
			 <?php if(!$is_published):?>
			 	<p><input type="checkbox" name="auto_publish" value="1"> <?php _e('Automatically publish this quiz in new post once I hit the "Save" button. (The new post will be auto-generated with the quiz title used for post title.)', 'watupro')?></p>
			 <?php endif;?>
		</div>
	
		 <h3 class="hndle"><?php _e('Optional description', 'watupro')?></h3>    
		 <div class="inside">
		 	
		 	<?php echo wp_editor(stripslashes(@$dquiz->description), 'description');?>
		 	<p><?php printf(__('If provided, the description will be shown when starting the quiz. It can also be used in certificates. You can use the {{{button}}} tag <a href="%s" target="_blank">(more info)</a> to make the quiz start with a start button.', 'watupro'), 'http://blog.calendarscripts.info/create-start-button-in-watupro-using-the-button-tag/');?></p>
		 	
		 	<p><input type="checkbox" name="published_odd" value="1" <?php if(!empty($dquiz->published_odd)) echo 'checked'?> onclick="this.checked ? jQuery('#publishedURL').show() : jQuery('#publishedURL').hide();"> 
		 		<?php printf(__('This %s is published in custom field or other non-standard way (<a href="%s" target="_blank">what is this?</a>)', 'watupro'), __('quiz', 'watupro'), 'http://blog.calendarscripts.info/watupro-quizzes-published-in-custom-fields')?>
				<span id="publishedURL" style="display:<?php echo empty($dquiz->published_odd) ? 'none' : 'inline';?>"><?php _e('URL:', 'watupro')?> <input type="text" name="published_odd_url" size="40" value="<?php echo $dquiz->published_odd_url?>"></span>		 	
		 	</p>
		 </div>
		</div>
	</div><!-- end namedesc-->	
	
	<div class="postbox watupro-tab-div" id="settings" style="display:none;">
	    <div class="inside">	        
	     <h3 class="hndle"><span><?php _e('Quiz Settings', 'watupro') ?></span> </h3> 
	    <p> <?php _e('Randomization:', 'watupro')?> <select name="randomize_questions">
				<option value="0" <?php if(empty($dquiz->randomize_questions)) echo "selected"?>><?php _e('Display questions and answers in the way I entered them','watupro')?></option>    
				<option value="1" <?php if(!empty($dquiz->randomize_questions) and $dquiz->randomize_questions==1) echo "selected"?>><?php _e('Randomize questions and answers','watupro')?></option>
				<option value="2" <?php if(!empty($dquiz->randomize_questions) and $dquiz->randomize_questions==2) echo "selected"?>><?php _e('Randomize questions but NOT answers','watupro')?></option>
				<option value="3" <?php if(!empty($dquiz->randomize_questions) and $dquiz->randomize_questions==3) echo "selected"?>><?php _e('Randomize answers but NOT questions','watupro')?></option>
	    </select>  </p>
		 <p><input type="checkbox" id="groupByCat" name="group_by_cat" value="1" <?php if(@$dquiz->group_by_cat) echo "checked"?> onclick="this.checked ? this.form.randomize_cats.disabled = false : this.form.randomize_cats.disabled = true;"> <?php _e("Show questions grouped by category (useful if you have categorized your questions)", 'watupro')?>
		 &nbsp; <input type="checkbox" id="randomizeCats" name="randomize_cats" value="1" <?php if(!empty($dquiz->randomize_cats)) echo 'checked'?> <?php if(empty($dquiz->group_by_cat)) echo 'disabled'?>> <?php _e('Randomize categories', 'watupro')?></p>  
		 <p> <?php _e("Pagination:", 'watupro')?> <select name="single_page" onchange="watuPROChangePagination(this.value);">
		 	<option value="1" <?php if(@$dquiz->single_page==WATUPRO_PAGINATE_ALL_ON_PAGE) echo "selected"?>><?php _e('All questions on single page', 'watupro');?></option>
		 	<option value="2" <?php if(@$dquiz->single_page==WATUPRO_PAGINATE_PAGE_PER_CATEGORY) echo "selected"?>><?php _e('One page per question category', 'watupro');?></option>
		 	<option value="0" <?php if(@$dquiz->single_page==WATUPRO_PAGINATE_ONE_PER_PAGE) echo "selected"?>><?php _e('Each question on its own page', 'watupro');?></option>
		 	<option value="3" <?php if(@$dquiz->single_page==WATUPRO_PAGINATE_CUSTOM_NUMBER) echo "selected"?>><?php _e('Custom number per page', 'watupro');?></option>
		 </select>
		 	<span id="watuPROCustomPerPage" style="display:<?php echo (@$dquiz->single_page==WATUPRO_PAGINATE_CUSTOM_NUMBER) ? 'inline' : 'none'; ?>"><input type="text" name="custom_per_page" value="<?php echo @$dquiz->custom_per_page?>" size="4"> <?php _e('per page', 'watupro')?></span>
		 </p>  
		 
		 <div id="disallowPrevious" <?php if((!empty($dquiz->ID) and $single_page) or !empty($dquiz->single_page)) echo "style='display:none;'"?>>		  
		 <input type="checkbox" name="disallow_previous_button" value="1" <?php if(!empty($dquiz->disallow_previous_button)) echo "checked"?>> <?php _e('Disallow previous button', 'watupro')?> &nbsp;
		 <input type="checkbox" name="live_result" value="1" <?php if(!empty($dquiz->live_result)) echo "checked"?>> <?php _e('Answer to each question can be seen immediately by pressing a button', 'watupro')?></div>
		 
		 <div id="alwaysShowSubmit" <?php if(!empty($dquiz->ID) and $dquiz->single_page==1) echo "style='display:none;'"?>>
			<p><input type="checkbox" name="show_pagination" value="1" <?php if(!empty($dquiz->show_pagination)) echo "checked"?>> <?php _e('Show numbered question paginator', 'watupro')?> &nbsp;</p>		 
		 	<p><input type="checkbox" name="submit_always_visible" value="1" <?php if(!empty($dquiz->submit_always_visible)) echo "checked"?>> <?php _e('Show submit button on each page', 'watupro')?></p>	 
		 </div>
		 
		 <div id="autoStoreProgress" <?php if(!empty($dquiz->ID) and $dquiz->single_page==1) echo "style='display:none;'"?>>
		 	<p><input type="checkbox" name="store_progress" value="1" <?php if(!empty($dquiz->store_progress)) echo "checked"?> onclick="if(this.checked) this.form.enable_save_button.checked=false;"> <?php _e('Automatically store user progress as they go from page to page (causes server requests)', 'watupro')?></p>
		 </div>
		 
		 
		 <p>	<input type="checkbox" name="enable_save_button" value="1" <?php if(!empty($dquiz->enable_save_button)) echo "checked"?> onclick="if(this.form.store_progress.checked) return false;"> <?php _e('Enable save button to allow users continue their quiz later.', 'watupro')?></p>
		 
		 <p><input type="checkbox" name="flag_for_review" value="1" <?php if(!empty($advanced_settings['flag_for_review']) and $advanced_settings['flag_for_review']=='1') echo 'checked'?>> <?php _e('Allow users to flag questions for review. In this case they will be prompted to review their flagged questions before submitting the quiz.', 'watupro')?></p>
		 
		 <?php if(!empty($recaptcha_public) and !empty($recaptcha_private)):?>
		 	<p><input type="checkbox" name="require_captcha" value="1" <?php if(!empty($dquiz->require_captcha)) echo "checked"?>> <?php _e('Require image validation (reCaptcha) to submit this exam', 'watupro');?></p>  
		 <?php endif;?>
	    
	    <p><?php _e('Set time limit of', 'watupro')?> <input type="text" name="time_limit" size="4" value="<?php echo @$dquiz->time_limit?>"> <?php _e('minutes (Leave it blank or enter 0 to not set any time limit.)', 'watupro')?></p>
	    <p><?php _e('Pull', 'watupro')?> <input type="text" name="pull_random" size="4" value="<?php echo @$dquiz->pull_random?>"> <?php _e('random questions', 'watupro')?> 
			[ <input type="checkbox" name="random_per_category" value="1" <?php if(!empty($dquiz->random_per_category)) echo "checked"?>> <?php _e('per category', 'watupro')?> ]   
	    <?php _e('each time when showing the exam (Leave it blank or enter 0 to show all questions)', 'watupro')?></p>
	    
	    <p><?php _e('Show max', 'watupro')?> <input type="text" name="num_answers" size="4" value="<?php echo @$dquiz->num_answers?>"> <?php _e('random answers to each question. Leave blank or enter 0 to show all answers (default). The correct answer will always be shown.', 'watupro')?></p>
	    
	    <p><label><input type="checkbox" name="grades_by_percent" value="1" <?php if(!empty($dquiz->grades_by_percent)) echo 'checked'?>> <?php _e('Calculate grades by % correct answers instead of points collected', 'watupro')?></label></p>
	    
	    <p><?php _e('Allow up to', 'watupro')?> <input type="text" name="takings_by_ip" value="<?php echo @$dquiz->takings_by_ip?>" size="4"> <?php _e('submissions by IP address. (Enter 0 for unlimited submissions)', 'watupro')?></p>
			
		<p><input type="checkbox" name="dont_display_question_numbers" value="1" <?php if(!empty($advanced_settings['dont_display_question_numbers'])) echo 'checked'?>> <?php _e('Do not display question numbers.', 'watupro')?></p>		
				
	   			
			<h3><span><?php printf(__('%s Category (Optional)', 'watupro'), __('Quiz', 'watupro'))?></span></h3>
			
			<label><?php _e('Select category:', 'watupro')?></label> <select name="cat_id">
				<option value="0" <?php if(empty($dquiz->ID) or $dquiz->cat_id==0) echo "selected"?>><?php _e('- Uncategorized -', 'watupro')?></option>
				<?php foreach($cats as $cat):?>
					<option value="<?php echo $cat->ID?>" <?php if(!empty($dquiz->ID) and $dquiz->cat_id==$cat->ID) echo "selected"?>><?php echo $cat->name;?></option>
				<?php endforeach;?>		
			</select>
			<br />		
			
	                <h3><span><?php printf(__('Schedule %s (Optional)', 'watupro'), __('quiz', 'watupro')) ?></span></h3>
	                
	                <input type="checkbox" name="is_scheduled" value="1" <?php if(@$dquiz->is_scheduled==1) echo "checked"?>> <?php printf(__('Schedule this %s', 'watupro'), __('quiz', 'watupro'))?><br>
	                <br>               
			
			<label><?php _e('Schedule from:', 'watupro')?></label> &emsp;
	                <input type="text" name="schedule_from" class="watuproDatePicker" value="<?php echo $schedule_from?>">
	                &nbsp;
	                <select name="schedule_from_hour">
	                    <?php $i=0;
	                    while ($i<24): ?>
	                        <option value="<?php echo $i?>" <?php if(date("G",strtotime(@$dquiz->schedule_from))==$i) echo "selected"?>><?php printf("%02d", $i); ?></option>
	                    <?php  $i++;
	                    endwhile; ?>
	                    
	                </select>:
	                
	                <select name="schedule_from_minute">
	                    <?php $i=0;
	                    while ($i<60):  ?>
	                        <option value="<?php echo $i?>" <?php if(date("i",strtotime(@$dquiz->schedule_from))==$i) echo "selected"?>><?php printf("%02d", $i)?></option>
	                    <?php $i++;
	                    endwhile; ?>
	                    
	                </select>
							
						 &nbsp;&nbsp;&nbsp;	
	                
	                <label><?php _e('Schedule to:', 'watupro')?></label> &emsp;
	                <input type="text" name="schedule_to" class="watuproDatePicker" value="<?php echo $schedule_to?>">
	                &nbsp;
	                <select name="schedule_to_hour">
	                    <?php $i=0;
	                    while ($i<24):?>
	                        <option value="<?php echo $i?>" <?php if(date("G",strtotime(@$dquiz->schedule_to))==$i) echo "selected"?>><?php printf("%02d", $i); ?></option>
	                    <?php $i++;
	                    endwhile; ?>
	                    
	                </select>:
	                
	                <select name="schedule_to_minute">
	                    <?php $i=0;
	                    while ($i<60): ?>
	                        <option value="<?php echo $i?>" <?php if(date("i",strtotime(@$dquiz->schedule_to))==$i) echo "selected"?>><?php printf("%02d", $i)?></option>
	                    <?php $i++;
	                    endwhile; ?>                    
	                </select>
			<br />
		</div>
	</div>
	
	<div class="postbox watupro-tab-div" id="useremailsettings" style="display:none;">	 
	 <div class="inside">
	 		<h3><?php _e('User and Email Related Settings', 'watupro') ?></h3>
	    <p><input id="requieLoginChk" type="checkbox" name="require_login" value="1" <?php if(!empty($dquiz->require_login)) echo "checked"?> onclick="this.checked?jQuery('#loginMode').show():jQuery('#loginMode').hide();"> <?php _e("Require user log-in", 'watupro')?></p>
	    <div id="loginMode" style="margin-left:20px;display:<?php echo @$dquiz->require_login?'block':'none';?>"> 
	    	<fieldset>
	    	<legend><b><?php _e('Logged user options', 'watupro')?></b></legend>
	    	<p><?php _e('Registered <a href="users.php" target="_blank">users</a> will be able to take this exam. You can add the users yourself or let them register themselves. For the second option you need to make sure sure that "Anyone can register" is checked in your <a href="options-general.php" target="_blank">general settings</a> page.', 'watupro')?></p>
	        
	        <p><input type="checkbox" name="take_again" value="1" <?php if(!empty($dquiz->take_again)) echo "checked"?> onclick="this.checked?jQuery('#timesToTake').show():jQuery('#timesToTake').hide();"> <?php printf(__('Allow users to submit the %s multiple times:', 'watupro'), __('quiz', 'watupro'))?> 
	        		<div id='timesToTake' style="margin-left:50px;<?php if(empty($dquiz->take_again)) echo 'display:none;'?>">
	        			<?php _e('Allow', 'watupro')?> <input type="text" size="4" name="times_to_take" value="<?php echo @$dquiz->times_to_take?>"> <?php _e('times (For unlimited times enter 0)', 'watupro')?>
						<?php if(watupro_intel()):?>
							<?php _e("but require an interval of at least", 'watupro')?> <input type="text" size="4" name="retake_after" value="<?php echo @	$dquiz->retake_after?>"> <?php printf(__('hours before the %s can be resubmitted.', 'watupro'), __('quiz', 'watupro'))?>
						<?php endif;?>
						<p><?php if(empty($grades)): echo "<b>".__('Once you create grades, you will be able to further restrict re-submitting the exam.', 'watupro')."</b>";
						else:
							_e('Re-submitting is allowed only if some of the following grades is last achieved (leave all unchecked to not set any grade-related limitation):', 'watupro');
							foreach($grades as $grade):?>
								<input type="checkbox" name="retake_grades[]" value="<?php echo $grade->ID?>" <?php if(!empty($dquiz->retake_grades) and strstr($dquiz->retake_grades, "|".$grade->ID."|")) echo "checked"?>> <?php echo $grade->gtitle?> &nbsp;&nbsp;&nbsp;
							<?php endforeach;
							endif;?></p> 
					</div>       
	        </p>
	        </fieldset>
		</div>    
		
		<p><input type="checkbox" name="email_admin" value="1" <?php if(!empty($dquiz->email_admin)) echo "checked"?> onclick="this.checked?jQuery('#wadminEmail').show():jQuery('#wadminEmail').hide();"> <?php _e("Send me email with details when someone takes the exam", 'watupro')?>
			<div id="wadminEmail" style="display:<?php echo !empty($dquiz->email_admin)?'block':'none'?>"><?php _e('Email address(es) to send to:', 'watupro')?> <input type="text" name="admin_email" value="<?php echo !empty($dquiz->email_admin)?$dquiz->admin_email:get_option('admin_email');?>" size="40"></div>
		</p>
		
		<p><input type="checkbox" name="email_taker" value="1" <?php if(!empty($dquiz->email_taker)) echo "checked"?>> <?php _e('Send email to the user with their results', 'watupro')?></p>
			<p><?php if(empty($grades)): echo "<b>".__('Once you create grades, you will be able to further configure this.', 'watupro')."</b>";
						else:
							_e('The email will be sent only if some of the following grades is achieved (leave all unchecked to not set any grade-related limitation):', 'watupro');
							foreach($grades as $grade):?>
								<input type="checkbox" name="email_grades[]" value="<?php echo $grade->ID?>" <?php if(!empty($advanced_settings['email_grades']) and in_array($grade->ID, $advanced_settings['email_grades'])) echo "checked"?>> <?php echo $grade->gtitle?> &nbsp;&nbsp;&nbsp;
							<?php endforeach;
							endif;?></p> 
	    	
	    	<input type="hidden" name="show_answers" value="<?php echo @$dquiz->show_answers?>">
		 </div>
		 		    	
	    	<div class="inside">
				<h3><?php _e('Ask for user contact details', 'watupro');?></h3>	    	
	    	
		    	<p><?php _e('For logged in users some of this data might be prepopulated.', 'watupro')?></p>
		    	
		    	<p><?php _e('Should we ask the user for contact details?', 'watupro')?> <input type="radio" name="ask_for_contact_details" value="" <?php if(empty($advanced_settings['ask_for_contact_details'])) echo 'checked'?> onclick="jQuery('#askForContactDetails').hide();"> <?php _e("Don't ask", 'watupro')?> &nbsp; <input type="radio" name="ask_for_contact_details" value="start"  <?php if(!empty($advanced_settings['ask_for_contact_details']) and $advanced_settings['ask_for_contact_details']=='start') echo 'checked'?> onclick="jQuery('#askForContactDetails').show();"> <?php _e("Ask at the beginning", 'watupro')?> &nbsp;
		    	<input type="radio" name="ask_for_contact_details" value="end"  <?php if(!empty($advanced_settings['ask_for_contact_details']) and $advanced_settings['ask_for_contact_details']=='end') echo 'checked'?> onclick="jQuery('#askForContactDetails').show();"> <?php _e("Ask at the end", 'watupro')?> &nbsp;</p>
		    	
		    	<div id="askForContactDetails" style="display:<?php echo empty($advanced_settings['ask_for_contact_details']) ? 'none' : 'block';?>">
			    	<p><label><?php _e('Ask for email:', 'watupro')?></label> <input type="radio" name="ask_for_email" value="" <?php if(empty($advanced_settings['contact_fields']['email'])) echo 'checked'?>> <?php _e('No', 'watupro')?> &nbsp;
			    	<input type="radio" name="ask_for_email" value="yes" <?php if(!empty($advanced_settings['contact_fields']['email']) and $advanced_settings['contact_fields']['email']=='yes') echo 'checked'?>> <?php _e('Yes', 'watupro')?> &nbsp;
			    	<input type="radio" name="ask_for_email" value="required" <?php if(!empty($advanced_settings['contact_fields']['email']) and $advanced_settings['contact_fields']['email']=='required') echo 'checked'?>> <?php _e('Required', 'watupro')?> &nbsp;|&nbsp; <?php _e('Label:', 'watupro')?> 
			    	<input type="text" name="ask_for_email_label" value="<?php echo empty($advanced_settings['contact_fields']['email_label']) ? __('Your email address:', 'watupro') : $advanced_settings['contact_fields']['email_label'];?>"> </p>
			    	
			    	<p><label><?php _e('Ask for name:', 'watupro')?></label> <input type="radio" name="ask_for_name" value="" <?php if(empty($advanced_settings['contact_fields']['name'])) echo 'checked'?>> <?php _e('No', 'watupro')?> &nbsp;
			    	<input type="radio" name="ask_for_name" value="yes" <?php if(!empty($advanced_settings['contact_fields']['name']) and $advanced_settings['contact_fields']['name']=='yes') echo 'checked'?>> <?php _e('Yes', 'watupro')?> &nbsp;
			    	<input type="radio" name="ask_for_name" value="required" <?php if(!empty($advanced_settings['contact_fields']['name']) and $advanced_settings['contact_fields']['name']=='required') echo 'checked'?>> <?php _e('Required', 'watupro')?> &nbsp;|&nbsp; <?php _e('Label:', 'watupro')?> 
			    	<input type="text" name="ask_for_name_label" value="<?php echo empty($advanced_settings['contact_fields']['name_label']) ? __('Your name:', 'watupro') : $advanced_settings['contact_fields']['name_label'];?>"> </p>
			    	
			    	<p><label><?php _e('Ask for company name:', 'watupro')?></label> <input type="radio" name="ask_for_company" value="" <?php if(empty($advanced_settings['contact_fields']['company'])) echo 'checked'?>> <?php _e('No', 'watupro')?> &nbsp;
			    	<input type="radio" name="ask_for_company" value="yes" <?php if(!empty($advanced_settings['contact_fields']['company']) and $advanced_settings['contact_fields']['company']=='yes') echo 'checked'?>> <?php _e('Yes', 'watupro')?> &nbsp;
			    	<input type="radio" name="ask_for_company" value="required" <?php if(!empty($advanced_settings['contact_fields']['company']) and $advanced_settings['contact_fields']['company']=='required') echo 'checked'?>> <?php _e('Required', 'watupro')?> &nbsp;|&nbsp; <?php _e('Label:', 'watupro')?> 
			    	<input type="text" name="ask_for_company_label" value="<?php echo empty($advanced_settings['contact_fields']['company_label']) ? __('Company name:', 'watupro') : $advanced_settings['contact_fields']['company_label'];?>"> </p>
			    	
			    	<p><label><?php _e('Ask for phone:', 'watupro')?></label> <input type="radio" name="ask_for_phone" value="" <?php if(empty($advanced_settings['contact_fields']['phone'])) echo 'checked'?>> <?php _e('No', 'watupro')?> &nbsp;
			    	<input type="radio" name="ask_for_phone" value="yes" <?php if(!empty($advanced_settings['contact_fields']['phone']) and $advanced_settings['contact_fields']['phone']=='yes') echo 'checked'?>> <?php _e('Yes', 'watupro')?> &nbsp;
			    	<input type="radio" name="ask_for_phone" value="required" <?php if(!empty($advanced_settings['contact_fields']['phone']) and $advanced_settings['contact_fields']['phone']=='required') echo 'checked'?>> <?php _e('Required', 'watupro')?> &nbsp;|&nbsp; <?php _e('Label:', 'watupro')?> 
			    	<input type="text" name="ask_for_phone_label" value="<?php echo empty($advanced_settings['contact_fields']['phone_label']) ? __('Phone:', 'watupro') : $advanced_settings['contact_fields']['phone_label'];?>"> </p>
				</div>
		   </div>			
	</div>
	
	<?php if(watupro_intel()): 
			if(@file_exists(get_stylesheet_directory().'/watupro/i/exam_form_intelligence.php')) require get_stylesheet_directory().'/watupro/i/exam_form_intelligence.php';
			else require WATUPRO_PATH."/i/views/exam_form_intelligence.php";
			?>
			<div class="postbox watupro-tab-div" id="advanced" style="display:none;">
				<?php $_GET['exam_id'] = @$dquiz->ID; 
				watupro_advanced_exam_settings();?>
			</div>
			<?php
	endif;?>
	
	<style type="text/css"> #gradecontent p{border-bottom:1px dotted #ccc;padding-bottom:3px;} #gradecontent label{padding: 5px 10px;} #gradecontent textarea{width:96%;margin-left:10px;} #gradecontent p img.gradeclose{ border:0 none; float:right; } </style>
	
	<div id="finalscreen" class="watupro-tab-div postbox" style="display:none;">				
		<div class="inside">
			<h3><?php _e('Final Screen', 'watupro') ?></h3>
			
			<p><input type="checkbox" name="shareable_final_screen" value="1" <?php if(!empty($dquiz->shareable_final_screen)) echo 'checked'?> onclick="this.checked ? jQuery('#shareableFinalScreenExtra').show() : jQuery('#shareableFinalScreenExtra').hide();"> <?php _e('Enable individual shareable URL for the final screen.', 'watupro');?> <?php printf(__('Or better use our new <a href="%s" target="_blank">social sharing addon</a>', 'watupro'), 'http://blog.calendarscripts.info/social-sharing-addon-for-watupro/')?></p>
			<div id="shareableFinalScreenExtra" style="display:<?php echo empty($dquiz->shareable_final_screen) ? 'none' : 'block';?>">
				<p><?php _e('You can use the variable', 'watupro')?> <b>%%watupro-share-url%%</b> <?php _e('to display a link to the shareable URL in the final screen itself.', 'watupro')?></p>	
				<p><input type="checkbox" value="1" name="redirect_final_screen" <?php if(!empty($dquiz->redirect_final_screen)) echo 'checked'?>> <?php printf(__('Automatically redirect the user to the shareable final screen url when they complete the exam. (<a href="%s" target="_blank">why?</a>)', 'watupro'), 'http://blog.calendarscripts.info/shareable-final-screen-url-in-watupro/');?></p>
			</div>
		
			<?php wp_editor($final_screen, "content"); ?>
			
			<p><input type="checkbox" name="use_different_email_output" value="1" <?php if(!empty($dquiz->email_output)) echo "checked"?> onclick="this.checked?jQuery('#emailOutput').show():jQuery('#emailOutput').hide()"> <?php _e('Use different output for the email that is sent to the user/admin', 'watupro')?></p>
			
			<div id="emailOutput" style="display:<?php echo empty($dquiz->email_output)?'none':'block';?>">
				<p><label><?php _e('Email subject:', 'watupro');?></label> <textarea name="email_subject" cols="80" rows="1"><?php echo stripslashes(@$dquiz->email_subject);?></textarea></p>
				<p><?php _e('If you leave it empty, default subject will be used. You can use the variable %%QUIZ_NAME%% to include the quiz name', 'watupro')?></p>			
			
				<p><label><?php _e('Email contents:', 'watupro');?></label><?php wp_editor(stripslashes(@$dquiz->email_output),"email_output"); ?></p>
				<p><?php printf(__('By default this content is used for both the email sent to user, and the email sent to admin. You can however use the %s tag to make the email contents different. The content before the %s tag will be sent to the user (if the corresponding checkbox is checked) and the content after the %s tag - to the admin.', 'watupro'), '{{{split}}}', '{{{split}}}', '{{{split}}}')?></p>
			</div>
			
			<?php if(@file_exists(get_stylesheet_directory().'/watupro/usable-variables.php')) require get_stylesheet_directory().'/watupro/usable-variables.php';
			else require WATUPRO_PATH."/views/usable-variables.php";?>
		</div>
	</div>
	
	<p class="submit">
	<?php wp_nonce_field('watupro_create_edit_quiz'); ?>
	<input type="hidden" name="action" value="<?php echo $action; ?>" />
	<input type="hidden" name="quiz" value="<?php echo @$_REQUEST['quiz']; ?>" />
	<input type="hidden" id="user-id" name="user_ID" value="<?php echo (int) @$user_ID ?>" />
	<span id="autosave"></span>
	<input type="submit" name="submit" value="<?php _e('Save All Settings', 'watupro') ?>" style="font-weight: bold;" tabindex="4" />
	</p>

</form>
</div>

<script type="text/javascript" >
function watuPROChangePagination(val) {	
	jQuery('#alwaysShowSubmit').show();
	jQuery('#watuPROCustomPerPage').hide();
	jQuery('#autoStoreProgress').hide();
	
	if(val != 1) {jQuery('#disallowPrevious').show(); jQuery('#autoStoreProgress').show();}
	else {jQuery('#disallowPrevious').hide();}
	
	if(val==2) {jQuery('#groupByCat').attr('checked', true); jQuery('#randomizeCats').removeAttr('disabled');}
	if(val==1) jQuery('#alwaysShowSubmit').hide();
	if(val == 3) jQuery('#watuPROCustomPerPage').show();
}

jQuery(document).ready(function() {
    jQuery('.watuproDatePicker').datepicker({
        dateFormat : 'yy-mm-dd'
    });
});

function watuproChangeTab(lnk, tab) {
	jQuery('.watupro-tab-div').hide();
	jQuery('#' + tab).show();
	
	jQuery('.nav-tab-active').addClass('nav-tab').removeClass('nav-tab-active');
	jQuery(lnk).addClass('nav-tab-active');
}

function WatuPROValidateExam(frm) {
	if(frm.name.value == '') {
		alert("<?php printf(__('Please enter %s name', 'watupro'), __('quiz', 'watupro'))?>");
		frm.name.focus();
		return false; 
	}
	
	if(frm.published_odd.checked && frm.published_odd_url.value == '') {
		alert("<?php printf(__('If the %s is published in custom field you must provide the URL to the page where it is published.', 'watupro'), __('quiz', 'watupro'))?>");
		frm.published_odd_url.focus();
		return false;
	}
	
	return true;
}
</script>