<?

//--------------------------------------------------------------------------------------------------
//	display a folder
//--------------------------------------------------------------------------------------------------
	
	if (authHas('folders', 'view', '') == false) { do403(); }		// check basic permissions
	if ($request['ref'] == '') { do404(); }							// check ref

	$UID = raFindRedirect('folders', 'show', 'folder', $request['ref']); 	// check correct ref
	
	require_once($installPath . 'modules/folders/models/folder.mod.php');
	$model = new folder($request['ref']);	

	$page->load($installPath . 'modules/folders/actions/show.page.php');

	$page->blockArgs['UID'] = $UID;
	$page->blockArgs['raUID'] = $request['ref'];
	$page->blockArgs['userUID'] = $model->data['createdBy'];
	$page->render();

?>
