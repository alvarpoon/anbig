<div class="wrap">
	<h1><?php _e("My Certificates", 'watupro');?></h1>
	
	<?php if($user_id != $user_ID):?>
		<p><?php _e('Showing certificates of ', 'watupro')?> <strong><?php echo $user->user_login?></strong></p>
	<?php endif;?>
	
	<?php if(sizeof($certificates)):?>
	
	<table class="widefat">
		<tr><th><?php printf(__('%s title', 'watupro'), __('quiz'))?></th><th><?php _e('Completed on', 'watupro')?></th><th><?php _e('With result', 'watupro')?></th>
		<th><?php _e('View/Print', 'watupro')?></th><th><?php _e('Allow public access', 'watupro')?></th></tr>
		<?php foreach($certificates as $certificate):
			$class = ('alternate' == @$class) ? '' : 'alternate';?>
			<tr class="<?php echo $class?>"><td><strong><?php echo $certificate->exam_name;?></strong></td>
			<td><?php echo date(get_option('date_format'), strtotime($certificate->end_time)) ?></td>
			<td><?php echo $certificate->grade?></td>
			<td><?php echo "<a href='".site_url("?watupro_view_certificate=1&taking_id=".$certificate->taking_id."&id=".$certificate->ID)."' target='_blank'>".__('print your certificate', 'watupro')."</a>"?></td>
			<td align="center"><input type="checkbox" onclick="window.location = 'admin.php?page=watupro_my_certificates&set_public_access=1&id=<?php echo $certificate->us_id?>&public_access=' + (this.checked ? 1 : 0);" <?php if(!empty($certificate->public_access)) echo 'checked'?>></td></tr>
		<?php endforeach;?>
	</table>
	
	<p><?php _e('When "Allow public access" is checked, everyone who has the link will be able to see your certificate.', 'watupro')?></p>
	
	<?php else:?>
		<p><?php _e('There are no accessible certificates at the moment.', 'watupro')?></p>
	<?php endif;?>
	
	<?php do_action('watupro_my_certificates');?>
</div>