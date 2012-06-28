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

	if ('public' == $user->role) { $page->do403('', true); }

	if (
		(false == array_key_exists('refModule', $req->args)) ||
		(false == array_key_exists('refModel', $req->args)) ||
		(false == array_key_exists('refUID', $req->args))
	) {
		$page->do404('Must specify refModule, refModel and refUID', true);
	}

	$refModule = $req->args['refModule'];
	$refModel = $req->args['refModel'];
	$refUID = $req->args['refUID'];

	if (false == $kapenta->moduleExists($refModule, $refUID)) { $page->do404('No module', true); }
	if (false == $db->objectExists($refModel, $refUID)) { $page->do404('No owner', true); }

	//	TODO: permissions checks here

	//----------------------------------------------------------------------------------------------
	//	render page
	//----------------------------------------------------------------------------------------------

	$page->load('modules/live/actions/attachments.if.page.php');
	$page->blockArgs['refModule'] = $refModule;
	$page->blockArgs['refModel'] = $refModel;
	$page->blockArgs['refUID'] = $refUID;
	$page->render();

?>
