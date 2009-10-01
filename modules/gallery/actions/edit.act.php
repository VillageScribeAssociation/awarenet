<?

//--------------------------------------------------------------------------------------------------
//	edit an image gallery
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	load the model
	//----------------------------------------------------------------------------------------------

	if (authHas('gallery', 'edit', '') == false) { do403(); }				// basic permissions
	if ($request['ref'] == '') { do404(); }									// check for ref
	$UID = raFindRedirect('gallery', 'edit', 'gallery', $request['ref']); 	// check correct ref

	require_once($installPath . 'modules/gallery/models/gallery.mod.php');

	$model = new Gallery();
	if ($model->load($request['ref']) == false)  { do404(); }

	//----------------------------------------------------------------------------------------------
	//	check permissions (must be admin or own gallery to edit)
	//----------------------------------------------------------------------------------------------

	$auth = false;
	if ($user->data['ofGroup'] == 'admin') { $auth = true; }
	if ($user->data['UID'] == $model->data['createdBy']) { $auth = true; }
	// possibly more to come here...
	if ($auth == false) { do404(); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------

	$page->load($installPath . 'modules/gallery/actions/edit.page.php');
	$page->blockArgs['UID'] = $model->data['UID'];
	$page->blockArgs['raUID'] = $model->data['recordAlias'];
	$page->render();

?>
