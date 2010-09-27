<?

//--------------------------------------------------------------------------------------------------
//	edit an image folder
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	load the model
	//----------------------------------------------------------------------------------------------

	if ($user->authHas('files', 'Files_Folder', 'edit', 'TODO:UIDHERE') == false) { $page->do403(); }			// check basic permissions
	if ('' == $req->ref) { $page->do404(); }								// check for ref
	
	require_once($kapenta->installPath . 'modules/folder/folder.mod.php');

	$model = new folder();
	if ($model->load($req->ref) == false)  { $page->do404(); }

	//----------------------------------------------------------------------------------------------
	//	check permissions (must be admin or own folder to edit)
	//----------------------------------------------------------------------------------------------

	$auth = false;
	if ('admin' == $user->role) { $auth = true; }
	if ($user->UID == $model->createdBy) { $auth = true; }
	// possibly more to come here...
	if ($auth == false) { $page->do404(); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------

	$page->load('modules/folder/edit.page.php');
	$page->blockArgs['UID'] = $model->UID;
	$page->blockArgs['raUID'] = $model->alias;
	$page->render();

?>
