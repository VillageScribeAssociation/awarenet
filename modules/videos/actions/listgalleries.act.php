<?

//--------------------------------------------------------------------------------------------------
//*	list all video galleries created by a user (in root if nesting)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	// check basic permissions
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('videos', 'videos_gallery', 'show')) { $page->do403(); }	

	//----------------------------------------------------------------------------------------------
	//	decide which users galleries to show
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $kapenta->request->ref = $user->alias; }

	$model = new Users_User($kapenta->request->ref);
	if (false == $model->loaded) { $page->do404(); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------

	$kapenta->page->load('modules/videos/actions/listgalleries.page.php');		
	$kapenta->page->blockArgs['userUID'] = $model->UID;								
	$kapenta->page->blockArgs['userRa'] = $model->alias;
	$kapenta->page->blockArgs['userName'] = $model->getName();
	$page->title = 'awareNet - videos by ' . $kapenta->page->blockArgs['userName'];
	$kapenta->page->render();													

?>
