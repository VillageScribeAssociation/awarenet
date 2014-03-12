<?

//--------------------------------------------------------------------------------------------------
//	edit an image folder
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	load the model
	//----------------------------------------------------------------------------------------------

	if ($kapenta->user->authHas('files', 'files_folder', 'edit', 'TODO:UIDHERE') == false) { $kapenta->page->do403(); }			// check basic permissions
	if ('' == $kapenta->request->ref) { $kapenta->page->do404(); }								// check for ref
	
	require_once($kapenta->installPath . 'modules/folder/folder.mod.php');

	$model = new folder();
	if ($model->load($kapenta->request->ref) == false)  { $kapenta->page->do404(); }

	//----------------------------------------------------------------------------------------------
	//	check permissions (must be admin or own folder to edit)
	//----------------------------------------------------------------------------------------------

	$auth = false;
	if ('admin' == $kapenta->user->role) { $auth = true; }
	if ($kapenta->user->UID == $model->createdBy) { $auth = true; }
	// possibly more to come here...
	if ($auth == false) { $kapenta->page->do404(); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------

	$kapenta->page->load('modules/folder/edit.page.php');
	$kapenta->page->blockArgs['UID'] = $model->UID;
	$kapenta->page->blockArgs['raUID'] = $model->alias;
	$kapenta->page->render();

?>
