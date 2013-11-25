<?

	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//*	edit an image gallery
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	load the model
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $page->do404(); }								// check for ref
	$UID = $aliases->findRedirect('gallery_gallery'); 						// check correct ref

	$model = new Gallery_Gallery($UID);
	if (false == $model->loaded)  { $page->do404('Gallery not found'); }
	if (false == $user->authHas('gallery', 'gallery_gallery', 'edit', $model->UID)) 
		{ $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	check permissions (must be admin or own gallery to edit)
	//----------------------------------------------------------------------------------------------

	$auth = false;
	if ('admin' == $user->role) { $auth = true; }
	if ($user->UID == $model->createdBy) { $auth = true; }
	// possibly more to come here...
	if (false == $auth) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------

	$kapenta->page->load('modules/gallery/actions/edit.page.php');
	$kapenta->page->blockArgs['UID'] = $model->UID;
	$kapenta->page->blockArgs['raUID'] = $model->alias;
	$kapenta->page->render();

?>
