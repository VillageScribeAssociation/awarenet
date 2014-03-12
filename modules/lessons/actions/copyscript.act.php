<?php

	require_once($kapenta->installPath . 'modules/lessons/models/courses.set.php');

//--------------------------------------------------------------------------------------------------
//	admin action to make a bash script for copying lessons from the lessons directory
//--------------------------------------------------------------------------------------------------

	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }

	$tarString = "tar -cvvf %%UID%%.tar ./%%UID%%\n";
	$scpString = "scp -B %%UID%%.tar strix@10.0.0.254:/var/www/awarenet/data/lessons/\n";
	$rmString = "rm %%UID%%.tar\n";

	$tarString = "tar -xvvf %%UID%%.tar \n";
	$scpString = "\n";
	$rmString = "rm %%UID%%.tar\n";

	$set = new Lessons_Courses('videolessons');

	header('Content-type: text/plain');

	foreach($set->members as $member) {
		echo str_replace('%%UID%%', $member['UID'], $tarString);
		echo str_replace('%%UID%%', $member['UID'], $scpString);
		echo str_replace('%%UID%%', $member['UID'], $rmString);
		echo "\n";
	}

?>
