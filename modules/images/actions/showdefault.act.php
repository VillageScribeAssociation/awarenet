<?php
	
	require_once($kapenta->installPath . 'modules/images/models/images.set.php');

//--------------------------------------------------------------------------------------------------
//|	display the default image associated with something
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $req->args)) { $page->do404('refModule not given'); }
	if (false == array_key_exists('refModel', $req->args)) { $page->do404('refModel not given'); }
	if (false == array_key_exists('refUID', $req->args)) { $page->do404('refUID not given'); }

	$refModule = $req->args['refModule'];
	$refModel = $req->args['refModel'];
	$refUID = $req->args['refUID'];
	$size = 'full';

	if (false == $kapenta->moduleExists($refModule)) { $page->do404('Unknown module'); }
	if (false == $db->objectExists($refModel, $refUID)) { $page->do404('Unknown owner'); }

	//TODO: permissions check here

	if (true == array_key_exists('s', $req->args)) { $req_args['size'] = $req->args['s']; }
	if (true == array_key_exists('size', $req->args)) { $size = $req->args['size']; }

	//----------------------------------------------------------------------------------------------
	//	load set of images related to this object
	//----------------------------------------------------------------------------------------------
	$set = new Images_Images($refModule, $refModel, $refUID);

	if (0 == count($set->members)) { $page->do302('images/unavailable/s_' . $size . '/'); }	

	$defaultAry = $set->members[0];

	$page->do302('images/s_' . $size . '/' . $defaultAry['alias']);

?>
