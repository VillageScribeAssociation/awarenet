<?

	require_once($kapenta->installPath . 'modules/videos/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//*	edit a video gallery
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	load the model
	//----------------------------------------------------------------------------------------------

	if ('' == $kapenta->request->ref) { $kapenta->page->do404(); }								// check for ref
	$UID = $aliases->findRedirect('videos_gallery'); 						// check correct ref

	$model = new Videos_Gallery($UID);
	if (false == $model->loaded)  { $kapenta->page->do404('Gallery not found'); }
	
	// basic permissions, TODO: extend
	if (false == $user->authHas('videos', 'videos_gallery', 'edit', $model->UID)) {$kapenta->page->do403();}		

	//----------------------------------------------------------------------------------------------
	//	check permissions (must be admin or own gallery to edit)
	//----------------------------------------------------------------------------------------------

	$auth = false;
	if ('admin' == $user->role) { $auth = true; }
	if ($user->UID == $model->createdBy) { $auth = true; }
	// possibly more to come here...
	if (false == $auth) { $kapenta->page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------

	$kapenta->page->load('modules/videos/actions/editgallery.page.php');
	$kapenta->page->blockArgs['UID'] = $model->UID;
	$kapenta->page->blockArgs['raUID'] = $model->alias;
	$kapenta->page->render();

?>
