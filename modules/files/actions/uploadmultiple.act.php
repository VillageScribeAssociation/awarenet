<?

//--------------------------------------------------------------------------------------------------
//*	iframe to upload multiple files
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $kapenta->request->args)) { $kapenta->page->do404('(no module)'); }
	if (false == array_key_exists('refModel', $kapenta->request->args)) { $kapenta->page->do404('(no model)'); }
	if (false == array_key_exists('refUID', $kapenta->request->args)) { $kapenta->page->do404('(no UID)'); }
	
	$refModule = $kapenta->request->args['refModule'];
	$refModel = $kapenta->request->args['refModel'];
	$refUID = $kapenta->request->args['refUID'];

	//----------------------------------------------------------------------------------------------
	//	render the (iframe) page
	//----------------------------------------------------------------------------------------------
		
	//TODO: further permissions here
	if (false == $user->authHas($refModule, $refModel, 'files-edit', $refUID)) {
		//------------------------------------------------------------------------------------------
		//	not authorised to edit files, just display
		//------------------------------------------------------------------------------------------
		$kapenta->page->load('modules/files/actions/fileset.if.page.php');
		$kapenta->page->blockArgs['refModule'] = $refModule;
		$kapenta->page->blockArgs['refModel'] = $refModel;
		$kapenta->page->blockArgs['refUID'] = $refUID;
		$kapenta->page->render();
		
	} else {
		//------------------------------------------------------------------------------------------
		//	authorised to edit files, show upload form
		//------------------------------------------------------------------------------------------
		$kapenta->page->load('modules/files/actions/uploadmultiple.if.page.php');
		$kapenta->page->blockArgs['refModule'] = $refModule;
		$kapenta->page->blockArgs['refModel'] = $refModel;
		$kapenta->page->blockArgs['refUID'] = $refUID;
		$kapenta->page->render();
			
	}

?>
