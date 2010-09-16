<?

	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//*	display a gallery page
//--------------------------------------------------------------------------------------------------
	
	//if ($user->authHas('gallery', 'Gallery_Gallery', 'show', 'TODO:UIDHERE') == false) { $page->do403(); }		// check basic permissions
	if ('' == $req->ref) { $page->do404(); }							// check ref
	$UID = $aliases->findRedirect('Gallery_Gallery'); 					// check correct ref
	
	$model = new Gallery_Gallery($req->ref);	

	$userRa = $aliases->getDefault('Users_User', $model->createdBy);

	$page->load('modules/gallery/actions/show.page.php');
	$page->blockArgs['UID'] = $UID;
	$page->blockArgs['raUID'] = $req->ref;
	$page->blockArgs['userUID'] = $model->createdBy;
	$page->blockArgs['userRa'] = $userRa;
	$page->blockArgs['galleryRa'] = $model->alias;
	$page->blockArgs['galleryTitle'] = $model->title;
	$page->render();

?>
