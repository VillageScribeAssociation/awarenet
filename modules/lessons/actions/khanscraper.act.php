<?php

	require_once($kapenta->installPath . 'modules/lessons/inc/khan.inc.php');

//--------------------------------------------------------------------------------------------------
//*	Scraper for khan academy videos / deveopment script
//--------------------------------------------------------------------------------------------------
	echo "here";
	if ('admin' != $user->role) { $page->do403(); }

	$courses = lessons_listKhan();

	echo "<pre>";
	print_r($courses);
	echo "</pre>";

?>
