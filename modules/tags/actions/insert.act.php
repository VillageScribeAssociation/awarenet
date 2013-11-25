<?php

//--------------------------------------------------------------------------------------------------
//*	window for seraching inserting tagged items into HyperTextAreas
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { $page->do403(); }

	if (false == array_key_exists('hta', $kapenta->request->args)) {
		$page->do404('no hypertext area specified', true);
	}

	//----------------------------------------------------------------------------------------------
	//	show the window
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/tags/actions/insert.if.page.php');
	$page->requireJs($kapenta->serverPath . 'modules/live/js/live.js');
	$kapenta->page->blockArgs['hta'] = $kapenta->request->args['hta'];
	$kapenta->page->render();
?>
