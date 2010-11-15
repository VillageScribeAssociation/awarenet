<?

//--------------------------------------------------------------------------------------------------
//*	list all video galleries created by a user (in root if nesting)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	// check basic permissions
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('videos', 'Videos_Gallery', 'show')) { $page->do403(); }	

	//----------------------------------------------------------------------------------------------
	//	decide which users galleries to show
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $req->ref = $user->alias; }

	$model = new Users_User($req->ref);
	if (false == $model->loaded) { $page->do404(); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------

	$page->load('modules/videos/actions/listgalleries.page.php');		
	$page->blockArgs['userUID'] = $model->UID;								
	$page->blockArgs['userRa'] = $model->alias;
	$page->blockArgs['userName'] = $model->getName();
	$page->title = 'awareNet - videos by ' . $page->blockArgs['userName'];
	$page->render();													

?>
