<?php

//--------------------------------------------------------------------------------------------------
//*	script to compile annual stats for this instance
//--------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $page->do403(); }

	echo $theme->expandBlocks("[[:theme::ifscrollheader:]]");

	//----------------------------------------------------------------------------------------------
	//	initialize registry values
	//----------------------------------------------------------------------------------------------
	$kapenta->registry->set('stats.images', 'Images := images_image');
	$kapenta->registry->set('stats.videos', 'Videos := videos_image, Video Galleries := videos_gallery');
	$kapenta->registry->set('stats.forums', 'Forum Threads := forums_thread, Forum Replies := forums_reply');
	$kapenta->registry->set('stats.users', 'Users := users_user, Friendships := users_friendship, User Sessions := users_session');
	$kapenta->registry->set('stats.projects', 'Projects := projects_project, Project Members := projects_membership, Project Revisions := projects_change');
	$kapenta->registry->set('stats.moblog', 'Moblog Posts := moblog_post');
	$kapenta->registry->set('stats.comments', 'Comments := comments_comment');
	$kapenta->registry->set('stats.files', 'Files := files_file');
	$kapenta->registry->set('stats.gallery', 'Image Galleries := gallery_gallery');

	//----------------------------------------------------------------------------------------------
	//	control valiables
	//----------------------------------------------------------------------------------------------
	$table = array();
	$title = array('Item');
	$startYear = 2011;
	$startMonth = 10;
	$endYear = 2012;
	$endMonth = 10;

	//----------------------------------------------------------------------------------------------
	//	get the set of labels
	//----------------------------------------------------------------------------------------------
	$mods = $kapenta->listModules();
	$labels = array();

	foreach($mods as $mod) {
		$statObjects = $kapenta->registry->get('stats.' . $mod);
		$assignments = explode(',', $statObjects);
		foreach($assignments as $assignment) {
			if ('' != $assignment) {
				$parts = explode(':=', $assignment);
				$label = trim($parts[0]);
				$objType = trim($parts[1]);
				$labels[$label] = $objType;
				//echo "$label => $objType <br/>\n";
			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//	columns of title row
	//----------------------------------------------------------------------------------------------
	$currMonth = $startMonth;
	$currYear = $startYear;
	$continue = true;
	while (true == $continue) {
		$title[] = substr('00' . $currYear, - 2) . '-' . substr('00' . $currMonth, - 2);

		//------------------------------------------------------------------------------------------
		//	move on to next month
		//------------------------------------------------------------------------------------------
		$currMonth++;
		if ($currMonth > 12) { $currMonth = 1; $currYear++; }
		if (($currYear >= $endYear) && ($currMonth >= $endMonth)) { $continue = false; }
	}

	$title[] = "Total";

	$table[] = $title;

	//----------------------------------------------------------------------------------------------
	//	rest of table
	//----------------------------------------------------------------------------------------------

	foreach($labels as $label => $objType) {
		$row = array(str_replace(' ', '&nbsp;', $label));
		$currMonth = $startMonth;
		$currYear = $startYear;
		$continue = true;

		$total = 0;

		while (true == $continue) {
			$currMonthS = substr('00' . $currMonth, - 2);
			$startDate = $currYear . '-' . $currMonthS . '-00';	
			$endDate = $currYear . '-' . $currMonthS . '-31';

			$condition = "createdOn BETWEEN '$startDate' AND '$endDate'";
			$new = $kapenta->db->countRange($objType, array($condition));

			$condition = "editedOn BETWEEN '$startDate' AND '$endDate'";
			$edit = $kapenta->db->countRange($objType, array($condition));

			$row[] = $new . "<small>/$edit</small>";
			$total += $new;
			//--------------------------------------------------------------------------------------
			//	move on to next month
			//--------------------------------------------------------------------------------------
			$currMonth++;
			if ($currMonth > 12) { $currMonth = 1; $currYear++; }
			if (($currYear >= $endYear) && ($currMonth >= $endMonth)) { $continue = false; }
		}

		$row[] = $total;
		$table[] = $row;
	}


	echo "<div class='chatmessageblack'>";
	echo $theme->arrayToHtmlTable($table, true, true);
	echo "</div>\n";

?>
