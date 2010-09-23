<?

//--------------------------------------------------------------------------------------------------
//*	list all galleries created by a user (in root if nesting)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	// check basic permissions
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('gallery', 'Gallery_Gallery', 'show')) { $page->do403(); }	

	//----------------------------------------------------------------------------------------------
	//	decide which users galleries to show
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $req->ref = $user->alias; }

	$model = new Users_User($req->ref);
	if (false == $model->loaded) { $page->do404(); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------

	$page->load('modules/gallery/actions/list.page.php');		
	$page->blockArgs['userUID'] = $model->UID;								
	$page->blockArgs['userRa'] = $model->alias;
	$page->blockArgs['userName'] = $model->getName();
	$page->title = 'awareNet - galleries by ' . $page->blockArgs['userName'];
	$page->render();													

?>
