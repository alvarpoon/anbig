<?php if($has_tabs):?>
<h2 class="nav-tab-wrapper">
	<a class='nav-tab-active'><?php _e('Overview', 'watupro')?></a>
	<?php if(!get_option('watupro_nodisplay_reports_tests')):?><a class='nav-tab' href='admin.php?page=watupro_reports&tab=tests&user_id=<?php echo $report_user_id?>'><?php _e('Tests', 'watupro')?></a><?php endif;?>
	<?php if(!get_option('watupro_nodisplay_reports_skills')):?><a class='nav-tab' href='admin.php?page=watupro_reports&tab=skills&user_id=<?php echo $report_user_id?>'><?php _e('Skills/Categories', 'watupro')?></a><?php endif;?>
	<?php if(!get_option('watupro_nodisplay_reports_history')):?><a class='nav-tab' href='admin.php?page=watupro_reports&tab=history&user_id=<?php echo $report_user_id?>'><?php _e('History', 'watupro')?></a><?php endif;?>
</h2>
<?php endif;?>

<div class="wrap">
	<h2><?php _e('All time overview', 'watupro')?></h2>
	
	<div>
			<div class="postarea">
				<table>
					<tr><td><strong><?php _e('Different tests attempted:', 'watupro')?></strong></td><td><?php echo $num_attempts?></td></tr>
					<tr><td><strong><?php _e('Skills practiced (question categories):', 'watupro')?></strong></td><td><?php echo $num_skills?></td></tr>
					<tr><td><strong><?php _e('Certificates earned:', 'watupro')?></strong></td><td><?php echo $cnt_certificates?></td></tr>
				</table>
			</div>
	</div>		
	
	<p>&nbsp;</p>
	<div style="float:left;width:500px;">
			<h2><?php _e('Tests attempts per category', 'watupro')?></h2>
			<p><?php _e('Multiple attempts per test are also included in the chart.', 'watupro')?></p>
			<p>&nbsp;</p>
			<div>
					<div id="chart" class="postarea">
					</div>
			</div>
	</div>
	<div style="float:left;width:500px;">
			<h2><?php _e('Questions answered per question category', 'watupro')?></h2>			
			<p>&nbsp;</p>
			<p>&nbsp;</p>
			<div>
					<div id="chart2" class="postarea">
					</div>
			</div>
	</div>		
</div>

<script>
window.onload = function () {
    var r = Raphael("chart");
    r.piechart(200, 100, 100, [<?php foreach($report_cats as $cat) echo $cat['num_attempts'].",";?>],	{
			legend: [<?php foreach($report_cats as $cat) echo '"'.$cat['name'].' - '.$cat['num_attempts'].'",'?>],
			maxSlices: 10    
    });
    
    var r2 = Raphael("chart2");
    r2.piechart(200, 100, 100, [<?php foreach($question_cats as $cat) echo $cat->cnt.",";?>],	{
			legend: [<?php foreach($question_cats as $cat) echo '"'.$cat->name.' - '.$cat->cnt.'",'?>],
			maxSlices: 10    
    });
};
</script>