<div class="wrap">
	<h1><?php _e('Watu PRO Help', 'watupro')?></h1>
	
	<p><?php _e('Because most of the Watu PRO screens are self-explaining, this page is not meant to be a comprehensive user manual. Its intent is only to further clarify some of the functionality in the plugin. Also check the <a href="http://calendarscripts.info/watupro/howto.html" target="_blank">online Help &amp; How-To page</a>', 'watupro')?></p>
	
	<p>If you want to <b>upgrade to a new version</b>, please <a href="http://blog.calendarscripts.info/how-to-upgrade-watu-pro/" target="_blank">check this</a>.</p>
	
	<h2>Getting Started</h2>
	<p><strong>Here is how to start really quick:</strong> (<a href="http://blog.calendarscripts.info/watupro-quick-getting-started-guide/" target="_blank">See this guide with pictures</a>)</p>
	<ol>
		<li>Go to <a href="admin.php?page=watupro_exam&action=new">Create new quiz</a> page.</li>
		<li>You can skip filling almost everything - just enter name.</li>
		<li>If you want to create grades, click on Show/Hide link next to "Grading" on the same form</li>
		<li>Once the test is saved you'll be taken to a page to create questions. Please add some, a test makes no sense without any questions.</li>
		<li>Go back to the <a href="admin.php?page=watupro_exams">quizzes list</a> and you'll see all quizzes that you have created. Under the "Embed" column there is a code similar to <strong>[watupro 1]</strong> (or it might be showing other number). Copy it and paste it in a post or page in your blog. The URL of this post or page will be the URL of your test.</li>
		<li>You can view who submitted your new quiz by clicking on the hyperlinked number under the "Taken" column. You will see all the details, and you can filter through them, import, export them, etc.</li>
		<li>That's it! Feel free to create quiz categories and question categories, certificates, and user groups.</li>
	</ol>	
	
	<h2>How to see the results of my students?</h2>
	
	<p>There are multiple types of data and reports you can see. Check <a href="http://blog.calendarscripts.info/how-to-see-quiz-results-and-reports-of-students-in-watupro/" target="_blank">this pictorial</a>.</p>
	
	<h2><?php _e('Shortcodes in Watu PRO', 'watupro')?></h2>
	
	<ul>
		<li><strong>[watupro X]</strong> <?php _e('is the shortcode to publish an exam in a post or page. Instead of X you need to use the test ID. The full dynamically generated shortcode can be copied from "Manage quizzes" page.', 'watupro')?> </li>
		<li><strong>[watuprolist cat_id=X]</strong> <?php _e('is the shortcode to display links to all published quizzes from a selected category. X should be replaced with category ID so please copy the dynamic code from Categoires page.', 'watupro')?> </li>
		<li><strong>[watuprolist cat_id="ALL"]</strong> <?php _e('lists links to all published quizzes in the system.', 'watupro')?> <?php _e('Use [watuprolist cat_id="ALL" orderby="title"] to show them ordered by title or [watuprolist cat_id="ALL" orderby="latest"] to order them latest on top. The same format can be used in the above shortcode as well.', 'watupro')?></li>
		<li><strong>[watupro-myexams]</strong> <?php _e('lets you replicate the logged in user "My Quizzes" page outside of Wordpress admin area.', 'watupro')?> <?php _e('You can restrict this by categories as shown on the <a href="admin.php?page=watupro_cats">categories page</a>.', 'watupro');?> <?php _e('You can define sorting - by title or latest on top like this:', 'watupro')?> <b>[watupro-myexams ALL title]</b>, <b>[watupro-myexams ALL latest]</b></li>
		<li><strong>[watupro-mycertificates]</strong> lets you replicate the logged in user "My Certificates" page. </li>
		<li><strong>[watupro-leaderboard] or [watupro-leaderboard X]</strong> prints out a basic leaderboard of users who collected top number of points. In the second example X is the number of users, otherwise 10 is used. More configurable leaderboards are coming soon in additional module.</li>
		<li><strong>[watupro-userinfo]</strong> lets you display any information from the user profile. More info <a href="http://blog.calendarscripts.info/user-info-shortcodes-from-watupro-version-4-1-1/" target="_blank">here</a>.</li>
	</ul>
	
	<?php if(watupro_intel()):?>
	<h2>For Personality Quizzes</h2>
	<p>The following shortcode can be used <b>only in the "Final Screen" and email box</b> to improve the content shown to the user on personality quizzes. Many personality quizzes work better when displaying not just the assigned personality type but also information how many answers the user gave for the other types. Here is how to use it with an example:</p>
	<p><b>[watupro-expand-personality-result sort="best" limit=3 empty="false"]</b><br>
		For personality type {{{personality-type}}} you collected {{{num-answers}}} points.<br>
		&lt;p&gt;{{{personality-type-description}}}&lt;/p&gt;<br>
		<b>[/watupro-expand-personality-result]</b></p>
	<p>The text inside the shortcode will be repeated for every personality type. All the arguments in the shortcodes are optional:</p>
	<ul>
		<li><b>sort</b> defines how the types are sorted. You can sort by "best" (most answers collected type on top), "worst" (least on top), "alpha" (alphabetically by type title). If you don't specify the parameter the types will be sorted by the order you created them.</li>
		<li><b>limit</b> can be used to limit the number of personality types shown after sorting. Will be useful if you have tens of personality types and want to show only the ones that user gave most answers for. Or set to "1" to display information only for the type they received as result. If you don't include this parameter all the existing types will be shown.</li>
		<li><b>empty</b> defines whether the personality types that got 0 answers will be shown. When the parametter is skipped, they will be shown. When set to "false" they will be excluded.</li>
	</ul>	
	<?php endif;?>
	
	<?php if(watupro_module('reports')):?>
	<h2>The Reporting Module</h2>
	<a name="reporting"></a>
	<p>As a supervisor you can reach every user reports from your <a href="users.php">Wordpress Users</a> page. You'll see two extra columns added by WatuPRO there.</p>
	<p>You can also use the following shortcodes to display reports on the front-end:</p>
	
	<ul>
		<li><b>[WATUPROR OVERVIEW] or [WATUPROR OVERVIEW X]</b> shows the Overview page from the reports. You can pass user ID for X or leave it empty to display reports to the currently logged user.</li>
		<li><b>[WATUPROR TESTS] or [WATUPROR TESTS X]</b> displays the Tests page from the reports.</li>
		<li><b>[WATUPROR SKILLS] or [WATUPROR SKILLS X]</b> displays the Skills page from the reports.</li>
		<li><b>[WATUPROR HISTORY] or [WATUPROR HISTORY X]</b> displays the History page from the reports.</li>
		<li><b>[watupror-poll question_id="X"]</b> displays poll-like stats showing how everyone answered on a question. By default it loads "correct/incorrect"
chart on all question types except on "single answer" and "multiple answer" questions where it loads chart per each answer. You can force it to always show correct / incorrect by adding parameter to the shortcode: [watupro-poll question_id="X" mode="correct"]. You can also control the colors used in the chart like this: [watupror-poll question_id="X" correct_color="green" wrong_color="#FF0000"]. </li>
	</ul>
	<?php endif;?>
	
	<h2>Translating WatuPRO</h2>
	
	<p>WatuPRO supports translating in all the languages that Wordpress supports. We have created a short guide about translating the plugin <a href="http://blog.calendarscripts.info/how-to-translate-a-wordpress-plugin/" target="_blank">here</a>. See also the available <a href="http://blog.calendarscripts.info/watupro-community-translations/" target="_blank">community translations</a>.</p>
	
	<h2>Troubleshooting</h2>
	
	<p>Before contacting support please do check our <a href="http://calendarscripts.info/watupro/howto.html" target="_blank">online Help &amp; How-To page</a> on the site as many frequently asked questions are already answered there.</p>
	
	<h2>Redesigning and Customizing the Views / Templates</h2>
	
	<p style="color:red;"><b>Only for advanced users!</b></p>
	
	<p>You can safely customize all files from the "views" folders by placing their copies in your theme folder. Simply create folder "watupro" <b>in your theme root folder</b> and copy the files you want to custom from "views" folder directly there. For files from the Intelligence module, add folder "i" under "watupro" folder, and for files from the Reporting module add folder "reports".</p>

	<p>For example:</p>
	
	<ol>
		<li>If you are using the Twenty Fourteen theme, you should create folder "watupro" under it so the structure will now be something like <b>wp-content/themes/twentyfourteen/watupro</b>. (The files that are above the new "watupro" folder should remain where they are)</li>
		<li>Then if you want to modify the "My Quizzes" page copy the file my_exams.php from the plugin "views" folder and place it in the new "watupro" folder so you'll have  <b>wp-content/themes/twentyfourteen/watupro/my_exams.php</b></li>
		<li>If you want to create your version of the Reports Overview page from the Reporting module, copy it to <b>wp-content/themes/twentyfourteen/watupro/reports/overview.php</b></li>
	</ol>	
	
	<p>Don't worry if you use modified WordPress directory structure and don't have "wp-content" folder. The trick will work with any structure as long as you follow the same logic.</p>
	
	<p>Then feel free to modify the code, but of course be careful not to mess with the PHP or Javascript inside. This will let you change the design and even part of the functionality and not lose these changes when the plugin is upgraded. Be careful: we can not provide support for your custom versions of our views.</p>
	
	<h2>Support</h2>
	
	<p>If you have any questions or issues please send us email at <strong>info@calendarscripts.info</strong> or <b>support@kibokolabs.com</b>. <strong>VERY IMPORTANT:</strong> if you have a problem while using the plugin please <b>do provide the URL where we can observe it</b>. Seeing the URL helps far more than sending long descriptions of the problem. We don't knowingly release anything buggy so you can assume we don't know about the problem you have until you send us your report.</p>
</div>	