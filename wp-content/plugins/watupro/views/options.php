<div class="wrap">
<h2><?php _e("Watu PRO Settings", 'watupro'); ?></h2>

<p><?php _e('Go to', 'watupro')?> <a href="admin.php?page=watupro_exams"><?php printf(__("Manage Your %s", 'watupro'), __('Quizzes', 'watupro'))?></a></p>

<form name="post" action="" method="post" id="post">
<div id="poststuff">
<div id="postdiv" class="postarea">

<?php showWatuProOption('single_page', 'Show all questions in a <strong>single page</strong> (This global setting can be overwritten for every quiz)'); ?>

<p><input type="checkbox" name="debug_mode" value="1" id="delete_db"  <?php if(get_option('watupro_debug_mode')) echo 'checked'?> /> <?php _e('Enable debug mode to see SQL errors. (Useful in case you have any problems)', 'watupro')?></p>

<p><input type="checkbox" name="always_load_scripts" value="1" <?php if(get_option('watupro_always_load_scripts')) echo 'checked'?> /> <?php _e('Load WatuPRO javascripts on all pages (select this only if quizzes do not work otherwise).', 'watupro')?></p>

<!--p><input type="checkbox" name="delete_db" value="1" id="delete_db"  <?php if($delete_db) echo 'checked'?> onclick="this.checked ? jQuery('#reallyDeleteDB').show() : jQuery('#reallyDeleteDB').hide();" />
<label for="delete_db"><?php _e('Delete Watu PRO data when uninstalling the plugin. This will not delete anything if you only deactivate the plugin.', 'watupro')?></label>
	<span id="reallyDeleteDB" style="display:<?php echo $delete_db ? 'inline' : 'none';?>"><?php _e('Are you sure? Type "yes" to confirm.','watupro');?> <input type="text" name="really_delete_db" value="<?php echo $delete_db ? get_option('watupro_really_delete_db') : ''?>" size="5"></span>
</p-->

<p><?php _e('Send the automated emails from this address:', 'watupro')?> <input type="text" name="watupro_admin_email" value="<?php echo watupro_admin_email()?>" size="30"><br>
<?php _e('This defaults to your main admin email. However to set a friendlier sender name you can overwrite it here by entering your name in format: <b>Your Name &lt;email@domain.com&gt;</b>', 'watupro')?></p>


<div class="postbox">
<h3 class="hndle"><span><?php _e('Default Answer Type', 'watupro') ?></span></h3>
<div class="inside" style="padding:8px">
<?php 
	$single = $multi = $openend = '';
	if( get_option('watupro_answer_type') =='radio') $single='checked="checked"';
    elseif( get_option('watupro_answer_type') =='textarea') $openend='checked="checked"';
	else $multi = 'checked="checked"';
?>
<label>&nbsp;<input type='radio' name='answer_type' <?php print $single?> id="answer_type_r" value='radio' /><?php _e("Single Answer", 'watupro')?> </label>
&nbsp;&nbsp;&nbsp;
<label>&nbsp;<input type='radio' name='answer_type' <?php print $multi?> id="answer_type_c" value='checkbox' /><?php _e("Multiple Answers", 'watupro')?></label>
&nbsp;&nbsp;&nbsp;
<label>&nbsp;<input type='radio' name='answer_type' <?php print $openend?> id="answer_type_c" value='textarea' /><?php _e("Open End", 'watupro')?></label>
</div>
</div>

<div class="postbox">
	<h3 class="hndle"><span><?php _e('Roles', 'watupro') ?></span></h3>
	<div class="inside">		
	<h4><?php _e('Roles that can manage exams', 'watupro')?></h4>
	
	<p><?php _e('By default only Administrator and Super admin can manage Watu PRO exams. You can enable other roles here.', 'watupro')?></p>
	<p><?php foreach($roles as $key=>$r):
					if($key=='administrator') continue;
					$role = get_role($key);?>
					<input type="checkbox" name="manage_roles[]" value="<?php echo $key?>" <?php if(!empty($role->capabilities['watupro_manage_exams'])) echo 'checked';?>> <?php echo $role->name?> &nbsp;
				<?php endforeach;?></p>
	<?php if(watupro_intel() and current_user_can('administrator')):?>
		<p><a href="admin.php?page=watupro_multiuser" target="_blank"><?php _e('Fine-tune these settings.', 'watupro')?></a></p>
	<?php endif;?>
	<p><?php _e('Only administrator or superadmin can change this!', 'watupro')?></p>
	</div>
</div>

<div class="postbox">
	<h3 class="hndle"><span><?php _e('ReCaptcha Settings', 'watupro') ?></span></h3>
	<div class="inside">
		<p><label><?php _e('ReCaptcha Public Key:', 'watupro')?></label> <input type="text" name='recaptcha_public' value="<?php echo get_option('watupro_recaptcha_public')?>" size="50"></p>
		<p><label><?php _e('ReCaptcha Private Key:', 'watupro')?></label> <input type="text" name='recaptcha_private' value="<?php echo get_option('watupro_recaptcha_private')?>" size="50"></p>
		<p><?php _e('Setting up <a href="http://www.google.com/recaptcha/intro/index.html" target="_blank">ReCaptcha</a> is optional. If you choose to do so you will be able to require image validation on chosen exams to avoid spam box submissions.', 'watupro');?></p>
	</div>
</div>

<div class="postbox">
	<h3 class="hndle"><span><?php _e('User Pages', 'watupro') ?></span></h3>
	<div class="inside">
		<p><input type="checkbox" name="nodisplay_myquizzes" value="1" <?php if(get_option('watupro_nodisplay_myquizzes')) echo 'checked'?>> <?php printf(__('Do not display "My %s" page in user dashboard.', 'watupro'), __('Quizzes', 'watupro'))?></p>
		<p><input type="checkbox" name="nodisplay_mycertificates" value="1" <?php if(get_option('watupro_nodisplay_mycertificates')) echo 'checked'?>> <?php _e('Do not display "My certificates" page in user dashboard.', 'watupro')?></p>
		<p><input type="checkbox" name="nodisplay_mysettings" value="1" <?php if(get_option('watupro_nodisplay_mysettings')) echo 'checked'?>> <?php printf(__('Do not display "%s Settings" page in user dashboard.', 'watupro'), __('Quiz', 'watupro'))?></p>
		
		<?php if(watupro_module('reports')):?>
			<h2><?php _e('Reporting module tabs:', 'watupro');?></h2>
			<p><?php _e('In the "Quiz Reports" page do not display these tabs:', 'watupro')?></p>
			<p><input type="checkbox" name="nodisplay_reports_tests" value="1" <?php if(get_option('watupro_nodisplay_reports_tests')) echo 'checked'?>> <?php _e('Tests', 'watupro')?> &nbsp; <input type="checkbox" name="nodisplay_reports_skills" value="1" <?php if(get_option('watupro_nodisplay_reports_skills')) echo 'checked'?>> <?php _e('Skills', 'watupro')?> &nbsp;
			<input type="checkbox" name="nodisplay_reports_history" value="1" <?php if(get_option('watupro_nodisplay_reports_history')) echo 'checked'?>> <?php _e('History', 'watupro')?> &nbsp;</p>
		<?php endif;?>
	</div>
</div>

<?php if(watupro_intel()): 
	if(@file_exists(get_stylesheet_directory().'/watupro/i/payment-options.html.php')) require get_stylesheet_directory().'/watupro/i/payment-options.html.php';
	else require WATUPRO_PATH."/i/views/payment-options.html.php";
endif;?>

<p class="submit">
<input type="hidden" id="user-id" name="user_ID" value="<?php echo (int) @$user_ID ?>" />
<span id="autosave"></span>
<input type="submit" name="submit" value="<?php _e('Save Options', 'watupro') ?>" style="font-weight: bold;" />
</p>

</div>
</div>
</form>

<div class="wrap">
	<div class="postbox">
		<div class="inside">
			<h2><?php printf(__("Ajax in %s", 'watupro'), __('quizzes', 'watupro')); ?></h2>
			
			<p><?php printf(__('Here you can select %s which will be submitted by regular post submit rather than using Ajax. You may want to do this mostly for two reasons:', 'watupro'), __('quizzes', 'watupro'))?></p>
			
			<ol>
				<li><?php _e('To allow file uploads in "open end" questions.', 'watupro')?></li>
				<li><?php _e('To embed in the "Final screen" content from plugins that do not normally work well in Ajax mode.', 'watupro')?></li>
			</ol>
			
			<p><b><?php printf(__('The selected %s will NOT use Ajax when users submit them.', 'watupro'), __('quizzes', 'watupro'))?></b></p>
			
			<form name="post" action="" method="post" id="post">
			<div>
				<div class="postarea">
					<?php foreach($quizzes as $quiz):?>
						<input type="checkbox" name="no_ajax[]" value="<?php echo $quiz->ID?>" <?php if(!empty($quiz->no_ajax)) echo 'checked'?>> <?php echo stripslashes($quiz->name);?><br>
					<?php endforeach;?>
				</div>
				
				<div class="postarea">
					<h2><?php _e('Limitations for uploading files:', 'watupro')?></h2>
					
					<p><?php _e('In the quizzes that do NOT use ajax for submitting you will be able to allow file uploads for open end questions. Set your size and file type limitations here. Note that your server may also imply limitations on the uploaded file size.', 'watupro')?></p>
					
					<p><?php _e('Max uploaded file size:', 'watupro')?> <input type="text" name="max_upload" value="<?php echo get_option('watupro_max_upload')?>" size="4"> <?php _e('In KB. Keep it reasonably low.', 'watupro')?></p>
					
					<p><?php _e('Allowed file extensions:', 'watupro')?> <input type="text" name="upload_file_types" value="<?php echo get_option('watupro_upload_file_types')?>" size="50"> <?php _e('Separate with comma like this: "jpg, gif, png, doc". (Input them without quotes, all small letters)', 'watupro')?></p>
				</div>
				
				<p><input type="submit" name="save_ajax_settings" value="<?php _e('Save Ajax Related Settings', 'watupro')?>"></p>
			</div>
			</form>
		</div>	
	</div>
</div>		

<?php if(!empty($watu_exams) and sizeof($watu_exams)):?>
<div id="poststuff">
<div id="postdiv" class="postarea">
	<form method="post">
	<div class="postbox">
		<h3 class="hndle"><span><?php _e('Quizzes From Watu Basic', 'watupro') ?></span></h3>
		<div class="inside">
			<?php if(!empty($copy_message)):?>
				<p class="watupro-alert"><?php echo $copy_message?></p>
			<?php endif;?>		
		
			<p><?php printf(__("You have %d %s created in the basic free Watu plugin. Do you want to copy these exams in Watu PRO? You can do this any time, and multiple times.", 'watupro'), sizeof($watu_exams), __('quizzes', 'watupro'))?></p>
			
			<p><input type="checkbox" name="replace_watu_shortcodes" value="1"> <?php _e('Automatically replace all Watu shortcodes embedded in posts so they will be managed by WatuPRO', 'watupro')?></p>
			
			<p><input type="checkbox" name="copy_takings" value="1"> <?php _e('Copy also the results of users who submitted', 'watupro')?></p>
			
			<p class="submit"><input type="submit" name="copy_exams" value="<?php printf(__('Copy These %s to WatuPRO', 'watupro'), __('Quizzes','watupro'))?>" style="font-weight: bold;"></p>
			
			<p><strong><?php _e("Note: Watu should not be activated along with Watu PRO. Please deactivate the basic plugin if you have not already done this.", 'watupro')?></strong></p>
		</div>
	</div>	
	</form>
</div>
</div>		
<?php endif;?>


</div>

<?php
function showWatuProOption($option, $title) {
?>
<input type="checkbox" name="<?php echo $option; ?>" value="1" id="<?php echo $option?>" <?php if(get_option('watupro_'.$option)) print " checked='checked'"; ?> />
<label for="<?php echo $option?>"><?php _e($title, 'watupro') ?></label><br />

<?php
}