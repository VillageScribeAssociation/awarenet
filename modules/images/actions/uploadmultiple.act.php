<?

//--------------------------------------------------------------------------------------------------
//*	iframe to upload multiple images
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $kapenta->request->args)) 
		{ $kapenta->page->do404('Module not specified.', true); }

	if (false == array_key_exists('refModel', $kapenta->request->args))
		{ $kapenta->page->do404('Model not specified.', true); }

	if (false == array_key_exists('refUID', $kapenta->request->args))
		{ $kapenta->page->do404('UID not specified.', true); }


	$refModule = $kapenta->request->args['refModule'];
	$refModel = $kapenta->request->args['refModel'];
	$refUID = $kapenta->request->args['refUID'];

	if (false == $kapenta->moduleExists($refModule)) { $kapenta->page->do404('No such module.', true); }
	if (false == $kapenta->db->tableExists($refModel)) { $kapenta->page->do404('Object type not recognized.', true); }
	if (false == $kapenta->db->objectExists($refModel, $refUID))
		{ $kapenta->page->do404('Owner object does not exist.', false); }

	if (false == $user->authHas($refModule, $refModel, 'images-add', $refUID))
		{ $kapenta->page->do403('You are not authorized to add images to this object.', true); }

	$tags = 'no';
	if ((true == array_key_exists('tags', $kapenta->request->args)) && ('yes' == $kapenta->request->args['tags']))
		{ $tags = 'yes'; }
	
	//TODO: check this permission name
	/*
			//--------------------------------------------------------------------------------------
			//	not authorised to edit images, just display
			//--------------------------------------------------------------------------------------
			$kapenta->page->load('modules/images/actions/imageset.if.page.php');
			$kapenta->page->blockArgs['refModule'] = $refModule;
			$kapenta->page->blockArgs['refUID'] = $refUID;
			$kapenta->page->render();
	*/
		
	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/images/actions/uploadmultiple.if.page.php');
	$kapenta->page->blockArgs['refModule'] = $refModule;
	$kapenta->page->blockArgs['refModel'] = $refModel;
	$kapenta->page->blockArgs['refUID'] = $refUID;
	$kapenta->page->blockArgs['tags'] = $tags;
	$kapenta->page->render();
			
?>
