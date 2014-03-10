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

	if (false == array_key_exists('refModule', $kapenta->request->args))
		{ $kapenta->page->do404('module not given', true); }

	if (false == array_key_exists('refModel', $kapenta->request->args))
		{ $kapenta->page->do404('model not specified', true); }

	if (false == array_key_exists('refUID', $kapenta->request->args))
		{ $kapenta->page->do404('UID of owner object not specified', true); }

	$refModule = $kapenta->request->args['refModule'];
	$refModel = $kapenta->request->args['refModel'];
	$refUID = $kapenta->request->args['refUID'];

	if (false == $kapenta->moduleExists($refModule)) { $kapenta->page->do404('no such module', true); }
	if (false == $kapenta->db->tableExists($refModel)) { $kapenta->page->do404('model not recognized', true); }
	if (false == $kapenta->db->objectExists($refModel, $refUID))
		{ $kapenta->page->do404('owner object does not exist', true); }

	if (false == $user->authHas($refModule, $refModel, 'images-add', $refUID)) { $kapenta->page->do403(); }
	//TODO: check this image permission

	//----------------------------------------------------------------------------------------------
	//	load and render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/images/actions/uploadsingle.if.page.php');
	$kapenta->page->blockArgs['refModule'] = $refModule;
	$kapenta->page->blockArgs['refModel'] = $refModel;
	$kapenta->page->blockArgs['refUID'] = $refUID;
	$kapenta->page->blockArgs['category'] = $category;
	$kapenta->page->render();

?>
