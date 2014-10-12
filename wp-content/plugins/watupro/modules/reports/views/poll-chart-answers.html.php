<table class="watupro-report-poll">
	<?php foreach($answers as $answer):
		$color = $answer->correct ? $correct_color : $wrong_color; ?>
		<tr><td width="50%"><?php echo apply_filters('watupro_content', stripslashes($answer->answer))?></td>
		<td width="50%"><div style="float:left;height:20px;background-color:<?php echo $color?>;width:<?php echo $answer->percent*2?>px;"></div>
		<div style="float:left;">&nbsp; <?php printf(__('%d / %d%% answers', 'watupro'), $answer->num_takers, $answer->percent);?></div></td></tr>
	<?php endforeach;?>
</table>