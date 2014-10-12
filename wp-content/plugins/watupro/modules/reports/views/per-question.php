<div class="wrap">
	<?php if(empty($in_shortcode)):?>
		<h2><?php echo $exam->name?> : <?php _e('Stats Per Question', 'watupro')?></h2>
		
		<p><a href="admin.php?page=watupro_takings&exam_id=<?php echo $exam->ID?>"><?php _e("Back to the users list", 'watupro')?></a></p>
		
		<p><?php _e('Shortcode to display this page:', 'watupro')?> <input type="text" value="[watupror-stats-per-question <?php echo $exam->ID?>]" size="30" readonly="true" onclick="this.select();"></p>
		
		<p><?php _e("Sometimes the percentages don't add up to 100% or make more than 100% - this happens on a) open-end and 'fill the blanks' questions (usually under 100%) b) multiple-choices questions (usually more than 100%) and c) questions in which you have added or changed the possible answers after some users have taken the quiz. This is not error in calculations but natural effect of the different question types.", 'watupro')?></p>
	<?php endif;?>
	
	<?php foreach($questions as $cnt=>$question):
	$cnt++;?>
		<h3><?php echo apply_filters('watupro_content', $cnt.". ".stripslashes($question->question))?></h3>
		
		<table class="widefat">
			<tr class="alternate"><th><?php _e('Answer or metric', 'watupro')?></th><th><?php _e('Value', 'watupro')?></th></tr>
			<tr><td><?php _e('Number and % correct answers', 'watupro')?></td>
			<td><strong><?php echo $question->percent_correct?>%</strong> / <strong><?php echo $question->num_correct?></strong> <?php _e('correct answers from', 'watupro')?>
			<strong><?php echo $question->total_answers?></strong> <?php _e('total answers received', 'watupro')?> </td></tr>
			<?php $class = '';
			foreach($question->choices as $choice):
			$class = ('alternate' == @$class) ? '' : 'alternate';?><tr class="<?php echo $class?>">
				<td><?php echo apply_filters('watupro_content', stripslashes($choice->answer))?></td><td><strong><?php echo $choice->times_selected?></strong> <?php _e('times selected', 'watupro')?> / <strong><?php echo $choice->percentage?>%</strong> </td>			
			</tr><?php endforeach;?>
		</table>
		
		<?php if(empty($in_shortcode)):?><p><a href="admin.php?page=watupro_question_answers&exam_id=<?php echo $exam->ID?>&id=<?php echo $question->ID?>"><?php _e('Get full detauls', 'watupro')?></a></p><?php endif;?>
	<?php endforeach;?>
</div>	