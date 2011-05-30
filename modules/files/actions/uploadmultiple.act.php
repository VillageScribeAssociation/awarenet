<?

//--------------------------------------------------------------------------------------------------
//*	iframe to upload multiple files
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $req->args)) { $page->do404('(no module)'); }
	if (false == array_key_exists('refModel', $req->args)) { $page->do404('(no model)'); }
	if (false == array_key_exists('refUID', $req->args)) { $page->do404('(no UID)'); }
	
	$refModule = $req->args['refModule'];
	$refModel = $req->args['refModel'];
	$refUID = $req->args['refUID'];

	//----------------------------------------------------------------------------------------------
	//	render the (iframe) page
	//----------------------------------------------------------------------------------------------
		
	//TODO: further permissions here
	if (false == $user->authHas($refModule, $refModel, 'files-edit', $refUID)) {
		//------------------------------------------------------------------------------------------
		//	not authorised to edit files, just display
		//------------------------------------------------------------------------------------------
		$page->load('modules/files/actions/fileset.if.page.php');
		$page->blockArgs['refModule'] = $refModule;
		$page->blockArgs['refModel'] = $refModel;
		$page->blockArgs['refUID'] = $refUID;
		$page->render();
		
	} else {
		//------------------------------------------------------------------------------------------
		//	authorised to edit files, show upload form
		//------------------------------------------------------------------------------------------
		$page->load('modules/files/actions/uploadmultiple.if.page.php');
		$page->blockArgs['refModule'] = $refModule;
		$page->blockArgs['refModel'] = $refModel;
		$page->blockArgs['refUID'] = $refUID;
		$page->render();
			
	}

?>
