<?php

//--------------------------------------------------------------------------------------------------
//*	show the latest published edition of the newsletter
//--------------------------------------------------------------------------------------------------

	$conditions = array("status='published'");
	$range = $kapenta->db->loadRange('newsletter_edition', '*', $conditions, 'createdOn', 1);
	
	if (0 == count($range)) {
		//------------------------------------------------------------------------------------------
		//	There is no latest edition, redirect to category list
		//------------------------------------------------------------------------------------------
		$page->do302('newsletter/listeditions/');
	} else {
		//------------------------------------------------------------------------------------------
		//	Show the edition
		//------------------------------------------------------------------------------------------
		$item = array_pop($range);
		$page->do302('newsletter/showedition/' . $item['alias']);
	}

?>
