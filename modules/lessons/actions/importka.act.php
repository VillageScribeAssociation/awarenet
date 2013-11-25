<?php

	require_once($kapenta->installPath . 'modules/lessons/inc/khan.inc.php');

	if ('admin' !== $user->role) { $page->do404(); }
	
	$kapenta->fileMakeSubDirs('data/lessons/scraper/x.x');

	$listing = "";
	$courses = lessons_listKhan();
	
	$listing .= $courses;	
	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------

	$page->load('modules/lessons/actions/importka.page.php');
	$kapenta->page->blockArgs['kalisting'] = $listing;
	$page->render();	

?>
