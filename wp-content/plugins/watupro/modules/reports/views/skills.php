<?php if($has_tabs):?>
<h2 class="nav-tab-wrapper">
	<a class='nav-tab' href="admin.php?page=watupro_reports&user_id=<?php echo $report_user_id?>"><?php _e('Overview', 'watupro')?></a>
	<?php if(!get_option('watupro_nodisplay_reports_tests')):?><a class='nav-tab' href='admin.php?page=watupro_reports&tab=tests&user_id=<?php echo $report_user_id?>'><?php _e('Tests', 'watupro')?></a><?php endif;?>
	<?php if(!get_option('watupro_nodisplay_reports_skills')):?><a class='nav-tab-active'><?php _e('Skills/Categories', 'watupro')?></a><?php endif;?>
	<?php if(!get_option('watupro_nodisplay_reports_history')):?><a class='nav-tab' href='admin.php?page=watupro_reports&tab=history&user_id=<?php echo $report_user_id?>'><?php _e('History', 'watupro')?></a><?php endif;?>
</h2>
<?php endif;?>

<div class="wrap">
	 <form class="watupro" method="post">
		 <div class="postbox watupro-padded">
				<p><?php printf(__('%s category', 'watupro'), __('Quiz', 'watupro'))?> <select name="cat">
					<option value="-1"><?php _e('All categories', 'watupro')?></option>		
					<?php foreach($exam_cats as $cat):?>
						<option value="<?php echo $cat->ID?>" <?php if($cat->ID==@$_POST['cat']) echo "selected"?>><?php echo $cat->name;?></option>
					<?php endforeach;?>	
					<option value="0" <?php if(isset($_POST['cat']) and $_POST['cat']==="0") echo 'selected'?>><?php _e('Uncategorized', 'watupro')?></option>	
				</select> &nbsp;
				<?php _e('Skill (Question category):', 'watupro')?> <select name="q_cat">
					<option value="-1"><?php _e('All categories', 'watupro')?></option>
					<?php foreach($q_cats as $cat):?>
						<option value="<?php echo $cat->ID?>" <?php if($cat->ID==@$_POST['q_cat']) echo "selected"?>><?php echo $cat->name;?></option>
					<?php endforeach;?>					
				</select></p>				
				<p><?php _e('View skills:', 'watupro')?> <select name="skill_filter" onchange="WatuPRO.changeSkillFilter(this.value);">
						<option value=""><?php _e('All', 'watupro')?></option>
						<option value="practiced" <?php if(!empty($_POST['skill_filter']) and $_POST['skill_filter']=='practiced') echo "selected"?>><?php _e('Practiced only', 'watupro')?></option>
						<option value="proficient" <?php if(!empty($_POST['skill_filter']) and $_POST['skill_filter']=='proficient') echo "selected"?>><?php _e('Proficient only', 'watupro')?></option>
					</select> 
					<span id="proficiencyGoal" <?php if(empty($_POST['skill_filter']) or $_POST['skill_filter']!='proficient'):?>style="display:none;"<?php endif;?>><?php _e('Proficiency goal: at least', 'watupro')?> <input type="text" size="4" name="proficiency_goal" value="<?php echo @$_POST['proficiency_goal']?>"> <?php _e('% correct answers on the whole quiz', 'watupro')?></span>			
				</p>
				<p><input type="submit" value="<?php _e('Show Report', 'watupro')?>"></p>
		 </div>
	 </form>
	 
	 <p class="watupro-note"><strong><?php _e('These reports are based on the latest attempt made for every exam.', 'watupro')?></strong></p>
	 
	 <?php if(!empty($_POST['skill_filter']) and $_POST['skill_filter']=='proficient'):?>
		 <h2><?php _e('Proficiency summary', 'watupro')?></h2>
		 
		 <p>You are proficient in <?=$num_proficient?> skills.</p>
	 <?php endif;?>
	 
	 <h2><?php _e('Proficiency by skill', 'watupro')?></h2>
	 <table class="widefat">
	 	<tr><th><?php _e('Skill (question category) and exams', 'watupro')?></th><th><?php _e('Proficiency (% correct answers)', 'watupro')?></th></tr>
			 <?php foreach($skills as $skill):?>
			 	 <tr><th colspan="2"><?php echo $skill['category']->name?></th></tr>
			 	 <?php foreach($skill['exams'] as $exam):
			 	 		$class = ('alternate' == @$class) ? '' : 'alternate';?>
			 	 		<tr class="<?php echo $class?>"><td style="padding-left:25px;">
			 	 		<?php if(!empty($exam->post)) echo "<a href='".get_permalink($exam->post->ID)."' target='_blank'>"; 
			 	 		if(empty($exam->post) and !empty($exam->published_odd)) echo "<a href='".$exam->published_odd_url."' target='_blank'>";
						echo stripslashes($exam->name);
						if(!empty($exam->post) or !empty($exam->published_odd)) echo "</a>";?></td>
						<td><?php if(empty($exam->taking->ID)) echo "-";
						else echo @$exam->cats[$skill['category']->ID]['percentage']."%"?></td></tr>
			 	 <?php endforeach;
			 endforeach;?>
	 </table>
</div>

<script type="text/javascript" >
jQuery(function(){
	WatuPRO.changeSkillFilter = function(val) {
		if(val=='proficient') {
			jQuery('#proficiencyGoal').show();
		}
		else jQuery('#proficiencyGoal').hide();
	}
});	
</script>