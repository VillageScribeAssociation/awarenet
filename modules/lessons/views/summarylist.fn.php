<?php

	require_once($kapenta->installPath . 'modules/lessons/models/courses.set.php');

//--------------------------------------------------------------------------------------------------
//|	display some group of lessons
//--------------------------------------------------------------------------------------------------
//arg: courseGroup - a 'group' of lessons [string]

function lessons_summarylist($args) {
	global $kapenta;

	$html = '';								//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------

	if ('public' == $kapenta->user->role) { return ''; }
	if (false == array_key_exists('courseGroup', $args)) { return ''; }

	$set = new Lessons_Courses($args['courseGroup']);
	if (false == $set->loaded) { return "(could not load group: $courseGroup)"; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	foreach($set->members as $member) {
		$html .= "[[:lessons::summary::courseUID=" . $member['UID'] . ":]]";
	}

	return $html;
}

?>
