<h3><?php _e('Usable Variables:', 'watupro') ?></strong> [<a href="#" onclick="jQuery('#usableVariables').toggle();return false;"><?php _e('show/hide', 'watupro')?></a>]</h3> 
<p><?php _e('(All the variables can be used in grade descriptions as well.)', 'watupro')?></p>
	<table id='usableVariables'>
	<tr><th style="text-align:left;"><?php _e('Variable', 'watupro') ?></th><th style="text-align:left;"><?php _e('Explanation', 'watupro') ?></th></tr>
	<tr><td>%%CORRECT%%</td><td><?php _e('The number of correct answers (the old %%SCORE%% also works)', 'watupro') ?></td></tr>
	<tr><td>%%TOTAL%%</td><td><?php _e('Total number of questions', 'watupro') ?></td></tr>
	<tr><td>%%POINTS%%</td><td><?php _e('Total points collected', 'watupro') ?></td></tr>
	<tr><td>%%MAX-POINTS%%</td><td><?php _e('Maximum number of points that user could collect.', 'watupro') ?></td></tr>
	<tr><td>%%PERCENTAGE%%</td><td><?php _e('Correct answer percentage', 'watupro') ?></td></tr>
	<tr><td>%%GRADE%%</td><td><?php printf(__('The assigned grade after taking the %s - title and description together', 'watupro'), __('quiz', 'watupro')) ?>.</td></tr>
	<tr><td>%%GTITLE%%</td><td><?php _e('The assigned grade - title only', 'watupro') ?>.</td></tr>
	<tr><td>%%GDESC%%</td><td><?php _e('The assigned grade - description only', 'watupro') ?>.</td></tr>
	<?php if(empty($edit_mode)):?>
		<tr><td>%%RATING%%</td><td><?php _e("A generic rating of your performance - it could be 'Failed'(0-39%), 'Just Passed'(40%-50%), 'Satisfactory', 'Competent', 'Good', 'Excellent' and 'Unbeatable'(100%)", 'watupro') ?></td></tr>
	<?php endif;?>
	<tr><td>%%QUIZ_NAME%%</td><td><?php printf(__('The name of the %s', 'watupro'), __('quiz', 'watupro')) ?></td></tr>
	<tr><td>%%CERTIFICATE%%</td><td><?php _e('Outputs a link to printable certificate. Will be displayed only if certificate is assigned to the achieved grade and the user is logged in.', 'watupro') ?></td></tr>
	<?php if(empty($edit_mode)):?>
		<tr><td>%%UNRESOLVED%%</td><td><?php _e('Shows unresolved questions without showing which is the correct answer. Useful if you want to point user attention where they need to work more without exposing the correct results. Questions that are considered unresolved are unanswered ones or the questions where points collected are less or equal to 0.', 'watupro') ?></td></tr>
	<?php endif;?>	
	<tr><td>%%ANSWERS%%</td><td><?php if(empty($edit_mode)) _e('Displays the user answers along with correct/incorrect mark. Shows the same as the setting under "Correct answer display" but without any predefined text before it.', 'watupro');
	else _e('Displays table with user answers, points, and teacher comments', 'watupro')?></td></tr>
	<tr><td>%%CATGRADES%%</td><td><?php _e('Grades and stats per category in case you have defined such grades.', 'watupro') ?></td></tr>
	<tr><td>%%EMAIL%%</td><td><?php _e('User email address.', 'watupro') ?></td></tr>
	<tr><td>%%DATE%%</td><td><?php printf(__('The date when the %s is completed (Date format comes from your Wordpress Settings page).', 'watupro'), __('quiz', 'watupro')); ?>.</td></tr>
	<tr><td>%%TIME-SPENT%%</td><td><?php printf(__('The time spent to take the %s.', 'watupro'), __('quiz', 'watupro')); ?></td></tr>
	<tr><td>%%USER-NAME%%</td><td><?php _e('The logged in (or requested by a {{{name-field}}} tag) user name. If empty, it will display "Guest"', 'watupro'); ?>.</td></tr>
	<tr><td>%%AVG-POINTS%%</td><td><?php printf(__('Shows the average points achieved by others who took the same %s.', 'watupro'), __('quiz', 'watupro')); ?></td></tr>
	<tr><td>%%AVG-PERCENT%%</td><td><?php printf(__('Shows the average percent correct answer given by others who took the same %s.', 'watupro'), __('quiz', 'watupro')); ?></td></tr>
	</table>