<?

//--------------------------------------------------------------------------------------------------
//*	page for uploading a single image
//--------------------------------------------------------------------------------------------------
//	because images are bound to a specific UID on some other module, we need some arguments
//	specified in the url:
//		refModule - the module to which owner object belongs
//		refModel - type of object which may have an imge
//		refUID - UID of an object (not and alias)
//		category - a text label to differentiate between different categories of picture (reserved)
//
//	note that the refModule must have 'xxxxxx' permission for appropriate users/groups
//	TODO: choose a permission, add it

	//----------------------------------------------------------------------------------------------
	//	control variables
	//----------------------------------------------------------------------------------------------
	$refModule = '';
	$refModel = '';
	$refUID = '';
	$category = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------

	if (false == array_key_exists('refModule', $req->args))
		{ $page->do404('module not given', true); }

	if (false == array_key_exists('refModel', $req->args))
		{ $page->do404('model not specified', true); }

	if (false == array_key_exists('refUID', $req->args))
		{ $page->do404('UID of owner object not specified', true); }

	$refModule = $req->args['refModule'];
	$refModel = $req->args['refModel'];
	$refUID = $req->args['refUID'];

	if (false == $kapenta->moduleExists($refModule)) { $page->do404('no such module', true); }
	if (false == $db->tableExists($refModel)) { $page->do404('model not recognized', true); }
	if (false == $db->objectExists($refModel, $refUID))
		{ $page->do404('owner object does not exist', true); }

	if (false == $user->authHas($refModule, $refModel, 'images-add', $refUID)) { $page->do403(); }
	//TODO: check this image permission

	//----------------------------------------------------------------------------------------------
	//	load and render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/images/actions/uploadsingle.if.page.php');
	$page->blockArgs['refModule'] = $refModule;
	$page->blockArgs['refModel'] = $refModel;
	$page->blockArgs['refUID'] = $refUID;
	$page->blockArgs['category'] = $category;
	$page->render();

?>
