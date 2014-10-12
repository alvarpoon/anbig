<div class="wrap">
	<h1><?php echo empty($certificate->ID)?__("Create New", 'watupro'):__("Edit", 'watupro')?> <?php _e('Certificate', 'watupro')?></h1>

	<form method="post" onsubmit="return validate(this);">
	<div class="postbox" id="titlediv">
		<h3 class="hndle"><span><?php _e('Certificate Title', 'watupro') ?></span></h3>
		
		<div class="inside">
		<input type='text' name='title' id="title" value='<?php echo stripslashes(@$certificate->title); ?>' />
		</div>
	</div>

	<div id="<?php echo user_can_richedit() ? 'postdivrich' : 'postdiv'; ?>" class="postarea postbox">
	<h3 class="hndle"><span><?php _e('Certificate Text/HTML', 'watupro') ?></span></h3>
	<div class="inside">
	<?php wp_editor(stripslashes(@$certificate->html), 'html'); ?>

	<p><strong><?php _e('Usable Variables...', 'watupro') ?></strong></p>
	<table>
	<tr><th style="text-align:left;"><?php _e('Variable', 'watupro') ?></th><th style="text-align:left;"><?php _e('Value', 'watupro') ?></th></tr>
	<tr><td>%%USER_NAME%%</td><td><?php _e('Full user name, if provided, otherwise login will be used', 'watupro') ?></td></tr>
	<tr><td>%%POINTS%%</td><td><?php _e('Total points collected', 'watupro') ?></td></tr>
	<tr><td>%%GRADE%%</td><td><?php _e('The assigned grade after taking the exam. Or use %%GTITLE%% and %%GDESC%% to separate the grade title from the description.', 'watupro') ?></td></tr>
	<tr><td>%%QUIZ_NAME%%</td><td><?php _e('The name of the exam', 'watupro') ?></td></tr>
	<tr><td>%%DESCRIPTION%%</td><td><?php _e('The optional description.', 'watupro') ?></td></tr>
	<tr><td>%%DATE%%</td><td><?php _e('Date when exam is submitted.', 'watupro') ?></td></tr>
	<tr><td>%%ID%%</td><td><?php _e('Unique ID for this issued certificate.', 'watupro') ?></td></tr>
	</table>

	<p>&nbsp;</p>
	<h3><?php _e('Important!', 'watupro')?></h3>
	<ol>
        <li><?php _e("The certificate will be available only for logged in users, and when it's assigned to the grade they have achieved.", 'watupro')?></li>
        <li><?php _e("No CSS styles or header from your blog will be applied. Please include all the styling in the certificate.", 'watupro')?></li>
	</ol>
	
	<p>&nbsp;</p>
	<h3><?php _e('Admin approval', 'watupro')?></h3>
	<p><?php _e('By default the certificate is issued automatically when a grade assigned to it is achieved by the user. Here you can optionalliy configure admin approval.', 'watupro')?></p>
	
	<p><input type="checkbox" name="require_approval" value="1" <?php if(!empty($certificate->require_approval)) echo 'checked'?> onclick="this.checked ? jQuery('#certificateApprovalArea').show() : jQuery('#certificateApprovalArea').hide();"> <?php _e('This certificate requires admin approval', 'watupro')?></p>
	
	<div id="certificateApprovalArea" style="display:<?php echo empty($certificate->require_approval) ? 'none' : 'block';?>">
		<p><input type="checkbox" name="require_approval_notify_admin" value="1" <?php if(!empty($certificate->require_approval_notify_admin)) echo 'checked'?>> <?php _e('Notify me when a certificate approval requires my attention.', 'watupro')?></p>
		
		<p><input type="checkbox" name="approval_notify_user" value="1" <?php if(!empty($certificate->approval_notify_user)) echo 'checked'?> onclick="this.checked ? jQuery('#certificateUserNotifyArea').show() : jQuery('#certificateUserNotifyArea').hide();"> <?php _e('Notify the user when I approve their certificate.(You can configure the email contents.)', 'watupro')?></p>
		
		<div id="certificateUserNotifyArea" style="display:<?php echo empty($certificate->approval_notify_user) ? 'none' : 'block';?>">
			<p><label><?php _e('Email subject:', 'watupro')?></label> <input type="text" size="80" name="approval_email_subject" value='<?php echo stripslashes(@$certificate->approval_email_subject)?>'></p>
			<p><label><?php _e('Email message:', 'watupro')?></label> <?php echo wp_editor(stripslashes(@$certificate->approval_email_message), 'approval_email_message')?></p>
			<p><?php _e('You can use the following variables:', 'watupro');?> <input type="text" value="{{quiz-name}}" readonly onclick="this.select()" size="10"> <?php _e('for quiz name', 'watupro')?>, <input type="text" value="{{certificate}}" readonly onclick="this.select()" size="10"> <?php _e('for certificate name', 'watupro')?>, 
			<input type="text" value="{{date}}" readonly onclick="this.select();" size="6"> <?php _e('for the date when the exam was submitted, and', 'watupro')?> 
			<input type="text" value="{{url}}" readonly onclick="this.select()" size="6"> <?php _e('for the URL where the user can view/print their certificate.', 'watupro')?></p>
		</div>
	</div>

	<p class="submit">	
	<span id="autosave"></span>
	<input type="submit" name="ok" value="<?php _e('Save Certificate', 'watupro') ?>" style="font-weight: bold;" tabindex="4" />
	<?php if(!empty($certificate->ID)):?>
		<input type="button" value="Delete Certificate" onclick="confirmDelete(this.form);">
	<?php endif;?>
	</p>
	</div>
	<input type="hidden" name="del" value="0">
	</form>
</div>

<script type="text/javascript">
function validate(frm)
{
	if(frm.title.value=='')
	{
		alert("<?php _e('Please enter title of this certificate', 'watupro')?>");
		frm.title.focus();
		return false;
	}

	return true;
}

function confirmDelete(frm)
{
	if(confirm("<?php _e('Are you sure?', 'watupro')?>"))
	{
		frm.del.value=1;
		frm.submit();
	}
}
</script>