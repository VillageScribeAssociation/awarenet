<?

//--------------------------------------------------------------------------------------------------
//*	list all galleries created by a user (in root if nesting)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	// check basic permissions
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('gallery', 'gallery_gallery', 'show')) { $kapenta->page->do403(); }	

	//----------------------------------------------------------------------------------------------
	//	decide which users galleries to show
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $kapenta->request->ref = $user->alias; }

	$model = new Users_User($kapenta->request->ref);
	if (false == $model->loaded) { $kapenta->page->do404(); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------

	$kapenta->page->load('modules/gallery/actions/list.page.php');		
	$kapenta->page->blockArgs['userUID'] = $model->UID;								
	$kapenta->page->blockArgs['userRa'] = $model->alias;
	$kapenta->page->blockArgs['userName'] = $model->getName();
	$kapenta->page->title = 'awareNet - galleries by ' . $kapenta->page->blockArgs['userName'];
	$kapenta->page->render();													

?>
