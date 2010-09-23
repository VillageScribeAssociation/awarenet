<?

//--------------------------------------------------------------------------------------------------
//*	iframe to upload multiple images
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $req->args)) 
		{ $page->do404('Module not specified.', true); }

	if (false == array_key_exists('refModel', $req->args))
		{ $page->do404('Model not specified.', true); }

	if (false == array_key_exists('refUID', $req->args))
		{ $page->do404('UID not specified.', true); }


	$refModule = $req->args['refModule'];
	$refModel = $req->args['refModel'];
	$refUID = $req->args['refUID'];

	if (false == $kapenta->moduleExists($refModule)) { $page->do404('No such module.', true); }
	if (false == $db->tableExists($refModel)) { $page->do404('Object type not recognized.', true); }
	if (false == $db->objectExists($refModel, $refUID))
		{ $page->do404('Owner object does not exist.', false); }

	if (false == $user->authHas($refModule, $refModel, 'images-add', $refUID))
		{ $page->do403('You are not authorized to add images to this object.', true); }
	
	//TODO: check this permission name
	/*
			//--------------------------------------------------------------------------------------
			//	not authorised to edit images, just display
			//--------------------------------------------------------------------------------------
			$page->load('modules/images/actions/imageset.if.page.php');
			$page->blockArgs['refModule'] = $refModule;
			$page->blockArgs['refUID'] = $refUID;
			$page->render();
	*/
		
	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/images/actions/uploadmultiple.if.page.php');
	$page->blockArgs['refModule'] = $refModule;
	$page->blockArgs['refModel'] = $refModel;
	$page->blockArgs['refUID'] = $refUID;
	$page->render();
			
?>
