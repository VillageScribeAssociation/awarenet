<?php

//--------------------------------------------------------------------------------------------------
//*	Show a iframe page for adding attachments to some object
//--------------------------------------------------------------------------------------------------
//reqarg: refModule - name of a kapenta module [string]
//reqarg: refModel - type of object which may have attachments [string]
//reqarg: refUID - UID of object which may have attachments [string]


	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------

	if ('public' == $user->role) { $kapenta->page->do403('', true); }

	if (
		(false == array_key_exists('refModule', $kapenta->request->args)) ||
		(false == array_key_exists('refModel', $kapenta->request->args)) ||
		(false == array_key_exists('refUID', $kapenta->request->args))
	) {
		$kapenta->page->do404('Must specify refModule, refModel and refUID', true);
	}

	$refModule = $kapenta->request->args['refModule'];
	$refModel = $kapenta->request->args['refModel'];
	$refUID = $kapenta->request->args['refUID'];

	if (false == $kapenta->moduleExists($refModule, $refUID)) { $kapenta->page->do404('No module', true); }
	if (false == $kapenta->db->objectExists($refModel, $refUID)) { $kapenta->page->do404('No owner', true); }

	//	TODO: permissions checks here

	//----------------------------------------------------------------------------------------------
	//	render page
	//----------------------------------------------------------------------------------------------

	$kapenta->page->load('modules/live/actions/attachments.if.page.php');
	$kapenta->page->blockArgs['refModule'] = $refModule;
	$kapenta->page->blockArgs['refModel'] = $refModel;
	$kapenta->page->blockArgs['refUID'] = $refUID;
	$kapenta->page->render();

?>
