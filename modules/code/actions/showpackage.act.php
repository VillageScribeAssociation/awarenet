<?

	require_once($kapenta->installPath . 'modules/code/models/package.mod.php');

//--------------------------------------------------------------------------------------------------
//*	 action to display a single Package object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check reference and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $kapenta->page->do404('No Package specified.'); } 
	$UID = $aliases->findRedirect('code_package');
	$model = new Code_Package($kapenta->request->ref);
	if (false == $model->loaded) { $kapenta->page->do404("Unkown Package");}

	if (false == $user->authHas('code', 'Code_Package', 'view', $model->UID)) {
		$kapenta->page->do403('You are not authorized to view this Package.'); 
	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/code/actions/showpackage.page.php');
	$kapenta->page->blockArgs['UID'] = $model->UID;
	$kapenta->page->blockArgs['raUID'] = $model->UID;
	$kapenta->page->blockArgs['packageUID'] = $model->UID;
	$kapenta->page->blockArgs['name'] = $model->name;
	//	^ add any further block arguments here
	$kapenta->page->render();

?>
