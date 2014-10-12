<div class="wrap">
	<h1><?php printf(__("Users Who Earned Certificate %s", 'watupro'), $certificate->title)?></h1>
	
	<p><a href="admin.php?page=watupro_certificates"><?php _e('Back to all certificates', 'watupro')?></a></p>
	
	<?php if(!sizeof($users)):?>
		<p><?php _e('No user has earned this certificate yet.', 'watupro')?></p>
		</div>
	<?php return true;
	endif;?>
	
	<table class="widefat">
		<tr><th><?php _e('User name and email', 'watupro')?></th><th><?php _e('Date earned', 'watupro')?></th>
		<th><?php _e('Quiz taken', 'watupro')?></th><th><?php _e('With result', 'watupro')?></th><th><?php _e('View details')?></th>
		<?php if($certificate->require_approval):?>
			<th><?php _e('Status', 'watupro')?></th>
		<?php endif;?>
		<th><?php _e('View Certificate', 'watupro')?></th>		
		<th><?php _e('Remove', 'watupro')?></th></tr>	
		
		<?php foreach($users as $user):?>
			<tr><td><?php echo $user->user_nicename . " (".$user->user_email . ")"?></td>
			<td><?php echo date( $dateformat, strtotime($user->taking_date) ); ?></td>
			<td><?php echo $user->exam_name?></td><td><?php echo $user->taking_result?></td>
			<td><a href="#" onclick="WatuPRO.takingDetails('<?php echo $user->taking_id?>');return false;" target="_blank"><?php _e('view', 'watupro')?></a></td>
			<?php if($certificate->require_approval):?>
				<td><?php if($user->pending_approval):?>
				<a href="#" onclick="watuPROApproveUserCertificate(<?php echo $user->user_certificate_id?>);return false;"><?php _e('Approve', 'watupro')?></a>
				<?php else: _e('Approved', 'watupro'); endif;?></td>
			<?php endif;?>
			<td><a href="<?php echo site_url('?watupro_view_certificate=1&taking_id='.$user->taking_id.'&id='.$certificate->ID)?>" target="_blank"><?php _e('View / print', 'watupro')?></a></td>
			<td><a href="#" onclick="watuPRORemoveUserCertificate(<?php echo $user->user_certificate_id?>);return false;"><?php _e('Remove', 'watupro')?></a></td></tr>
		<?php endforeach;?>
	</table>
</div>

<script type="text/javascript" >
function watuPROApproveUserCertificate(ucID) {
	if(confirm("<?php _e('Are you sure?', 'watupro')?>")) {
		window.location = 'admin.php?page=watupro_user_certificates&id=<?php echo $certificate->ID?>&approve=1&user_certificate_id=' + ucID;
	}
}

function watuPRORemoveUserCertificate(ucID) {
	if(confirm("<?php _e('Are you sure? The user will not be able to print this certificate.', 'watupro')?>")) {
		window.location = 'admin.php?page=watupro_user_certificates&id=<?php echo $certificate->ID?>&delete=1&user_certificate_id=' + ucID;
	}
}
</script>