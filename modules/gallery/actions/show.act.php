<?

	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//*	display a gallery page
//--------------------------------------------------------------------------------------------------
	
	//if ($user->authHas('gallery', 'gallery_gallery', 'show', 'TODO:UIDHERE') == false) { $page->do403(); }		// check basic permissions
	if ('' == $req->ref) { $page->do404(); }							// check ref
	$UID = $aliases->findRedirect('gallery_gallery'); 					// check correct ref
	
	$model = new Gallery_Gallery($req->ref);	

	$userRa = $aliases->getDefault('users_user', $model->createdBy);

	$page->load('modules/gallery/actions/show.page.php');
	$page->blockArgs['UID'] = $UID;
	$page->blockArgs['raUID'] = $req->ref;
	$page->blockArgs['userUID'] = $model->createdBy;
	$page->blockArgs['userRa'] = $userRa;
	$page->blockArgs['galleryRa'] = $model->alias;
	$page->blockArgs['galleryTitle'] = $model->title;
	$page->render();

?>
