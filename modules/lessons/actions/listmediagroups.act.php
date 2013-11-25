<?php

	require_once($kapenta->installPath . 'modules/lessons/models/courses.set.php');

//--------------------------------------------------------------------------------------------------
//*	admin / development action to print media groups
//--------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $page->do403(); }

	$set = new Lessons_Courses();

	$groups = $set->listGroups(true);

	foreach($groups as $group) {
		echo "Recorded group: $group <br/>\n";
	}

?>
