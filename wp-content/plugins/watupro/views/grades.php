<div class="wrap">
	<?php if(empty($in_default_grades)):?>
		<h1><?php if(empty($exam->is_personality_quiz)) printf(__('Manage Grades in "%s"', 'watupro'), stripslashes($exam->name));
		else printf(__('Manage Personality Types in "%s"', 'watupro'), stripslashes($exam->name));?></h1>
	
		<p><a href="admin.php?page=watupro_exams"><?php printf(__("Back to %s", 'watupro'), __('quizzes','watupro'))?></a>
		| <a href="admin.php?page=watupro_exam&quiz=<?php echo $exam->ID?>&action=edit"><?php printf(__('Edit %s', 'watupro'), __('quiz','watupro'))?></a>
		| <a href="admin.php?page=watupro_questions&quiz=<?php echo $exam->ID?>"><?php _e('Manage Questions', 'watupro')?></a>	
		</p>
		<form method="post">
			<p><input type="checkbox" name="reuse_default_grades" value="1" <?php if($exam->reuse_default_grades) echo 'checked'?> onclick="this.form.submit();"> <?php printf(__('This %s will reuse the <a href="%s" target="_blank">default grades</a>.', 'watupro'), __('quiz', 'watupro'), 'admin.php?page=watupro_default_grades')?> <?php printf(__('(Alternatively you can <a href="%s">copy the default grades</a> into the %s.)', 'watupro'), "admin.php?page=watupro_grades&quiz=".$exam->ID."&amp;copy_default_grades=1", __('quiz', 'watupro'))?></p>
			<input type="hidden" name="set_reuse_default_grades" value="1">
		</form>
		
		<?php if($exam->reuse_default_grades):?>
			<h2><?php printf(__('This %s reuses the default grades', 'watupro'), __('quiz', 'watupro'))?></h2>
			
			<p><?php printf(__('Any change you apply to the default grades will be applied to this %s as well. If you want to use the default grades but have your copy in this %s, you can <a href="%s">copy the default grades</a> into it instead.', 'watupro'), __('quiz', 'watupro'), __('quiz', 'watupro'), "admin.php?page=watupro_grades&quiz=".$exam->ID."&amp;copy_default_grades=1");?></p>			
		</div><!-- end wrap-->
		<?php return true; 
		endif;?>
	<?php else:?>
		<h1><?php _e('Manage Default Grades', 'watupro')?></h1>
		
		<p><?php printf(__('These grades / results can be reused or copied in any of your %s.', 'watupro'), 'quizzes');?> </p>
	<?php endif;?>

	<?php if(sizeof($cats)):?>
		<form method="post">
			<p><?php _e('Manage grades / results for', 'watupro')?> <select name="cat_id" onchange="this.form.submit();">
				<option value="0" <?php if($cat_id == 0) echo 'selected'?>><?php printf(__('- The Whole %s -', 'watupro'), __('Quiz', 'watupro'))?></option>
				<?php foreach($cats as $cat):?>
					<option value="<?php echo $cat->ID?>" <?php if($cat_id == $cat->ID) echo "selected"?>><?php echo __('Category:','watupro').' '.$cat->name?></option>
				<?php endforeach;?>			
			</select></p>
			
			<?php if(!empty($cat_id)):?>
				<p><a href="#" onclick="jQuery('#gradecatDesign').toggle();return false;"><?php _e('Design the common category grade output for this quiz', 'watupro')?></a></p>
				<div id="gradecatDesign" style="display:<?php echo empty($_POST['save_design'])?'none':'block';?>;">
				   <h2><?php _e('Design the common category grade output for this quiz', 'watupro')?></h2>
					<p><strong><?php _e('Note: you are currently managing category-specific grades. These can be displayed at the final screen using the %%CATGRADES%% variable. All of them will be shown in loop at the place of the variable. In the box below you can design how each of the category grades will look.', 'watupro')?></strong></p>
					<p><strong><?php _e('This design is the same for all question categories in this exam.', 'watupro')?></strong></p>
										
					<?php echo wp_editor(stripslashes($exam->gradecat_design), 'gradecat_design');?>
					
					<p><?php _e('You can use several of the already known variables: <strong>%%CORRECT%%, %%TOTAL%%, %%POINTS%%, %%PERCENTAGE%%, %%GTITLE%%, %%GDESC%%</strong>. The variable <strong>%%CATEGORY%%</strong> will be replaced by the category name and the variable <strong>%%CATDESC%%</strong> - with the category description.', 'watupro')?></p>
					<p><?php _e('When displaying the category results / grades on the final screen, order them:', 'watupro')?> 
						<select name="gradecat_order">
							<option value="same"><?php printf(__('The same way categories with questions were ordered in the %s','watupro'), __('quiz', 'watupro'))?></option>
							<option value="best" <?php if(!empty($advanced_settings['gradecat_order']) and $advanced_settings['gradecat_order'] == 'best') echo 'selected'?>><?php printf(__('From the best to worst result','watupro'), __('quiz', 'watupro'))?></option>
							<option value="worst" <?php if(!empty($advanced_settings['gradecat_order']) and $advanced_settings['gradecat_order'] == 'worst') echo 'selected'?>><?php printf(__('From the worst to best result','watupro'), __('quiz', 'watupro'))?></option>
						</select></p>
					<p align="center"><input type="submit" value="<?php _e('Save the Design', 'watupro')?>" name="save_design"></p>
				</div>	
			<?php endif;?>	
		</form>
	<?php else:?>
		<p><?php _e('If you create <a href="admin.php?page=watupro_question_cats">question categories</a> you will be able to create category-based grades as well.', 'watupro')?></p>
	<?php endif;?>
	
	<?php if(!empty($in_default_grades)):?>
	<p><?php _e('Currently managing grades for', 'watupro')?> <select name="grades_by_percent" onchange="window.location='admin.php?page=watupro_default_grades&percentage_based=' + this.value;">
				<option value="0" <?php if(empty($_GET['percentage_based']) or $_GET['percentage_based'] != '1') echo 'selected'?>><?php _e('points based calculation')?></option>
				<option value="1" <?php if(!empty($_GET['percentage_based']) and $_GET['percentage_based'] == '1') echo 'selected'?>><?php _e('% correct anser based calculation')?></option>
			</select> <?php printf(__('This depends on the settings of each individual %s', 'watupro'), __('quiz', 'watupro'))?></p>
   <?php endif;?>
	<hr>	
	
	
	<h2><?php if(empty($exam->is_personality_quiz)) _e('Add New Grade', 'watupro');
	else _e('Add New Personality Type', 'watupro');?></h2>
	
	<p class="help"><strong><?php _e("Feel free to enter valid URL instead of title - I'll redirect to it instead of showing the final screen!", 'watupro')?></strong></p>
	<form method="post" onsubmit="return validateGrade(this);">
		<div class="watupro-padded">
			<div><?php _e('Title:', 'watupro')?> <input type="text" name="gtitle" size="60"></div>
			<div><?php _e('Description:', 'watupro')?> <?php echo wp_editor('', 'gdescription')?></div>
			<div style="display:<?php echo (!empty($exam->is_personality_quiz) and empty($cat_id)) ? 'none' : 'block'; ?>"><?php if($exam->grades_by_percent): _e('Assign this grade when % correct answers is from', 'watupro');				 
			else: _e('Assign this grade when  the points that user has collected are from', 'watupro');
			endif;?>
			<input type="text" name="gfrom" size="5"> <?php _e('to', 'watupro')?> <input type="text" name="gto" size="5"></div>
			
			<?php if(!empty($exam->is_personality_quiz) and empty($cat_id)):?>
					<p><?php _e("When creating global personality types for a personality quiz you don't need to set from/to points or % correct answers. Instead of this the grade is assigned to the choices your user make on questions.", 'watupro')?></p>
				<?php endif;?>			
			
			<?php if(!empty($cnt_certificates)):?>
				<div><label><?php _e('Upon achieving this grade / result assign the following certificate:', 'watupro')?></label> <select name="certificate_id">
				<option value="0"><?php _e("- Don't assign certificate", 'watupro')?></option>
				<?php foreach($certificates as $certificate):?>
					<option value="<?php echo $certificate->ID;?>"><?php echo $certificate->title;?></option>
				<?php endforeach;?>
				</select></div>
			<?php endif;?>
			<div align="center"><input type="submit" value="<?php _e('Add This Grade / Result', 'watupro')?>"></div>
		</div>
		<input type="hidden" name="add" value="1">
		<input type="hidden" name="cat_id" value="<?php echo $cat_id?>">	
	</form>
	
	<hr>
	<?php if(sizeof($grades)):?>
	<h2><?php if(empty($exam->is_personality_quiz)) _e('Edit Existing Grades', 'watupro');
	else _e('Edit Existing Personality Types', 'watupro');?></h2>
	<?php endif;?>
	
	<?php foreach($grades as $grade):?>
		<form method="post" onsubmit="return validateGrade(this);">
			<div class="watupro-padded">
				<div><?php _e('Title:', 'watupro')?> <input type="text" name="gtitle" size="80" value="<?php echo $grade->gtitle?>"></div>
				<div><?php _e('Description:', 'watupro')?> <?php echo wp_editor(stripslashes($grade->gdescription), 'gdescription'.$grade->ID)?></div>
				<div style="display:<?php echo (!empty($exam->is_personality_quiz) and empty($cat_id)) ? 'none' : 'block'; ?>">
					<?php if($exam->grades_by_percent): _e('Assign this grade when % correct answers is from', 'watupro');				 
					else: _e('Assign this grade when  the points that user has collected are from', 'watupro');
					endif;?>
					<input type="text" name="gfrom" size="5" value="<?php echo $grade->gfrom?>"> <?php _e('to', 'watupro')?> <input type="text" name="gto" size="5" value="<?php echo $grade->gto?>">									
				</div>
				
				<?php if(!empty($exam->is_personality_quiz) and empty($cat_id)):?>
					<p><?php _e("When creating global personality types for a personality quiz you don't need to set from/to points or % correct answers. Instead of this the grade is assigned to the choices your user make on questions.", 'watupro')?></p>
				<?php endif;?>
				
				<?php if(!empty($cnt_certificates)):?>
					<div><label><?php _e('Upon achieving this grade / result assign the following certificate:', 'watupro')?></label> <select name="certificate_id">
					<option value="0" <?php if(empty($row->ID) or $row->certificate_id==0) echo "selected"?>><?php _e("- Don't assign certificate", 'watupro')?></option>
					<?php foreach($certificates as $certificate):?>
						<option value="<?php echo $certificate->ID;?>" <?php if(!empty($grade->ID) and $grade->certificate_id==$certificate->ID) echo "selected"?>><?php echo $certificate->title;?></option>
					<?php endforeach;?>
					</select></div>
				<?php endif;?>
				<?php if($in_default_grades and $multiuser_access == 'own' and $grade->editor_id != $user_ID):?>
					<p><?php _e('This default grade is not created by you and you have no permissions to edit or delete it.', 'watupro')?></p>
				<?php else:?>
					<div align="center"><input type="submit" value="<?php _e('Save', 'watupro')?>">
					<input type="button" value="<?php _e('Delete', 'watupro')?>" onclick="confirmDelGrade(this.form);"></div>
				<?php endif;?>	
			</div>
			<input type="hidden" name="id" value="<?php echo $grade->ID?>">
			<input type="hidden" name="save" value="1">
			<input type="hidden" name="del" value="0">
			<input type="hidden" name="cat_id" value="<?php echo $cat_id?>">	
		</form>
		
		<hr>
	<?php endforeach;?>
</div>
<script type="text/javascript" >
function validateGrade(frm) {
	if(frm.gtitle.value=="") {
		alert("<?php _e('Please enter grade title','watupro')?>");
		frm.gtitle.focus();
		return false;
	}
	
	<?php if(empty($exam->is_personality_quiz)):?>
	if(frm.gfrom.value=="" || isNaN(frm.gfrom.value)) {
		alert("<?php _e('Please enter number','watupro')?>");
		frm.gfrom.focus();
		return false;
	}
	
	if(frm.gto.value=="" || isNaN(frm.gto.value)) {
		alert("<?php _e('Please enter number','watupro')?>");
		frm.gto.focus();
		return false;
	}
	<?php endif;?>
	return true;
}

function confirmDelGrade(frm) {
	if(confirm("<?php _e('Are you sure?', 'watupro')?>")) {
		frm.del.value=1;
		frm.submit();
	}
}
</script>