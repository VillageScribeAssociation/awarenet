<?php

//--------------------------------------------------------------------------------------------------
//*	show form to list poll questions for editing
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $kapenta->request->args)) { $kapenta->page->do404('missing refModule', true); }
	if (false == array_key_exists('refModel', $kapenta->request->args)) { $kapenta->page->do404('missing refModel', true); }
	if (false == array_key_exists('refUID', $kapenta->request->args)) { $kapenta->page->do404('missing refUID', true); }

	$refModule = $kapenta->request->args['refModule'];
	$refModel = $kapenta->request->args['refModel'];
	$refUID = $kapenta->request->args['refUID'];

	if (false == $kapenta->moduleExists($refModule)) { $kapenta->page->do404('No such module', true); }
	if (false == $kapenta->db->objectExists($refModel, $refUID)) { $kapenta->page->do404('No such owner', true); }

	//----------------------------------------------------------------------------------------------
	//	show the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/polls/actions/allquestions.page.php');
	$kapenta->page->blockArgs['refModule'] = $refModule;
	$kapenta->page->blockArgs['refModel'] = $refModel;
	$kapenta->page->blockArgs['refUID'] = $refUID;
	$kapenta->page->render();

?>
