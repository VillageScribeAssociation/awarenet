<?php

	require_once($kapenta->installPath . 'modules/lessons/models/courses.set.php');

//--------------------------------------------------------------------------------------------------
//*	list all courses in a group, formatted for nav
//--------------------------------------------------------------------------------------------------
//arg: group - course group to display [string]

function lessons_allcourses($args) {

	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------

	if (false == array_key_exists('courseGroup', $args)) { return 'course group not specified.'; }
	$set = new Lessons_Courses($args['courseGroup']);
	if (false == $set->loaded) { return '(unknown course group: ' . $args['courseGroup'] . ')'; }

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------

	$html .= "<div class='block'>\n[[:theme::navtitlebox::label=Browse:]]<h2>All Courses</h2>\n";

	foreach($set->members as $item) {
		$html .= "<a href='#lessons" . $item['UID'] . "'>" . $item['title'] . "</a><br/>\n";
	}

	$html .= "</div>\n<br/>\n";

	return $html;
}

?>
