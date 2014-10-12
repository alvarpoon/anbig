<div class="wrap">
	<?php if(empty($in_shortcode)):?>
		<h2><?php printf(__('%s: Stats Per Category', 'watupro'), $exam->name)?></h2>
		
		<p><a href="admin.php?page=watupro_takings&exam_id=<?php echo $exam->ID?>"><?php _e("Back to the users list", 'watupro')?></a></p>
		
		<p><?php _e('Shortcode to display this page:', 'watupro')?> <input type="text" value="[watupror-stats-per-category <?php echo $exam->ID?>]" size="30" readonly="true" onclick="this.select();"></p>		
	<?php endif;?>
	
	<table class="widefat">
		<tr><th><?php _e('Category Name', 'watupro')?></th><th><?php _e('Total questions asked', 'watupro')?></th>
		<th><?php _e('Answered - num and %', 'watupro')?></th><th><?php _e('Unanswered - num and %', 'watupro')?></th>
		<th><?php _e('Num. Correct anwers', 'watupro')?></th>
		<th><?php _e('% Correct anwers (from answered)', 'watupro')?></th>
		<th><?php _e('% Correct answers (from total)', 'watupro')?></th></tr>
		<?php foreach($cats as $cat):
		$class = ('alternate' == @$class) ? '' : 'alternate';?>	
			<tr class="<?php echo $class;?>"><td><?php echo stripslashes($cat->name)?></td>
			<td><?php echo $cat->total?></td>
			<td><?php printf(__('%d (%d%%)', 'watupro'), $cat->num_answered, $cat->perc_answered);?></td>
			<td><?php printf(__('%d (%d%%)', 'watupro'), $cat->num_unanswered, $cat->perc_unanswered);?></td>			
			<td><?php echo $cat->num_correct;?></td>
			<td><?php printf(__('%d%%', 'watupro'), $cat->perc_correct_a);?></td>
			<td><?php printf(__('%d%%', 'watupro'), $cat->perc_correct_t);?></td></tr>				
		<?php endforeach;?>
	</table>
</div>	