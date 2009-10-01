<?

//--------------------------------------------------------------------------------------------------
//	display a gallery page
//--------------------------------------------------------------------------------------------------
	
	if (authHas('gallery', 'show', '') == false) { do403(); }		// check basic permissions
	if ($request['ref'] == '') { do404(); }							// check ref

	if (raGetOwner($request['ref'], 'gallery') == false) { do404(); }  		// check gallery exists
	$UID = raFindRedirect('gallery', 'show', 'gallery', $request['ref']); 	// check correct ref
	
	require_once($installPath . 'modules/gallery/models/gallery.mod.php');
	$model = new Gallery($request['ref']);	

	$userRa = raGetDefault('users', $model->data['createdBy']);

	$page->load($installPath . 'modules/gallery/actions/show.page.php');
	$page->blockArgs['UID'] = $UID;
	$page->blockArgs['raUID'] = $request['ref'];
	$page->blockArgs['userUID'] = $model->data['createdBy'];
	$page->blockArgs['userRa'] = $userRa;
	$page->blockArgs['galleryRa'] = $model->data['recordAlias'];
	$page->blockArgs['galleryTitle'] = $model->data['title'];
	$page->render();

?>
