<?

//--------------------------------------------------------------------------------------------------
//	page for uploading a single picture
//--------------------------------------------------------------------------------------------------
//	because images are bound to a specific UID on some other module, we need some arguments
//	specified in the url:
//		refModule - the module which needs an image
//		refUID - UID or a record on that module (not recordAlias, UID)
//		category - a text label to differentiate between different categories of picture
//
//	note that the refModule must have 'imageupload' permission for appropriate users/groups

	$refModule = '';
	$refUID = '';
	$category = '';

	if (array_key_exists('refmodule', $request['args'])) 
		{ $refModule = $request['args']['refmodule']; }

	if (array_key_exists('refuid', $request['args'])) 
		{ $refUID = $request['args']['refuid']; }

	if (array_key_exists('category', $request['args'])) 
		{ $category = $request['args']['category']; }

	if (($refModule == '') OR ($refUID == '')) { echo "refUID or refModule unavailable."; die(); }

	$page->load($installPath . 'modules/images/actions/uploadsingle.if.page.php');
	$page->blockArgs['refModule'] = $refModule;
	$page->blockArgs['refUID'] = $refUID;
	$page->blockArgs['category'] = $category;
	$page->render();

?>
