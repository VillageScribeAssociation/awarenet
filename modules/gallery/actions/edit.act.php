<?

	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//*	edit an image gallery
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	load the model
	//----------------------------------------------------------------------------------------------

	if ($user->authHas('gallery', 'Gallery_Gallery', 'edit', 'TODO:UIDHERE') == false) { $page->do403(); }		// basic permissions
	if ('' == $req->ref) { $page->do404(); }								// check for ref
	$UID = $aliases->findRedirect('Gallery_Gallery'); 						// check correct ref

	$model = new Gallery_Gallery($UID);
	if (false == $model->loaded)  { $page->do404('Gallery not found'); }

	//----------------------------------------------------------------------------------------------
	//	check permissions (must be admin or own gallery to edit)
	//----------------------------------------------------------------------------------------------

	$auth = false;
	if ('admin' == $user->role) { $auth = true; }
	if ($user->UID == $model->createdBy) { $auth = true; }
	// possibly more to come here...
	if ($auth == false) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------

	$page->load('modules/gallery/actions/edit.page.php');
	$page->blockArgs['UID'] = $model->UID;
	$page->blockArgs['raUID'] = $model->alias;
	$page->render();

?>
