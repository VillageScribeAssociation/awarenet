<?php

	require_once($kapenta->installPath . 'modules/lessons/inc/khan.inc.php');

//--------------------------------------------------------------------------------------------------
//*	devlopment / administrative action to import data from khan academy
//--------------------------------------------------------------------------------------------------
//	Note: fragile and inefficient, just meant to get the job done

	if ('admin' != $user->role) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	make listing from scraper output
	//----------------------------------------------------------------------------------------------
	$kapenta->fileMakeSubDirs('data/lessons/scraper/x.x');
	$listing = "<div class='spacer'></div><div class='block'><h1>Package Builder</h1></div><br/>\n";
	$courses = lessons_listKhan();

	foreach($courses as $course) {
		$listing .= "<div class='block'>\n<h2>" . $course->title . "</h2>\n";

		foreach($course->documents as $doc) {
			$listing .= "<a href='" . $doc['downloadfrom'] . "'>" .  $doc['title'] . "</a><br/>\n";
		}

		if (true == $kapenta->fs->exists('data/lessons/' . $course->UID . '/manifest.xml')) {
			$listing .= "<div class='inlinequote'>Package exists on this server.</div>\n";
		} else {
			$listing .= ''
			 . "<div class='inlinequote'>\n"
			 . "<span style='float: right;'>\n"
			 . "<form name='frm{$course->UID}' method='POST' action='%%serverPath%%lessons/buildka/'>\n"
			 . "<input type='hidden' name='UID' value='{$course->UID}' />\n"
			 . "<input type='submit' value='Build Package' />\n"
			 . "</form>\n"
			 . "</span>\n"
			 . "See sidebar for deployment instructions.<br/><br/>\n"
			 . "</div>\n";
		}
		
		$listing .= "</div>\n<div class='spacer'></div>\n";
	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------

	$page->load('modules/lessons/actions/importka.page.php');
	$kapenta->page->blockArgs['kalisting'] = $listing;
	$page->render();

?>
