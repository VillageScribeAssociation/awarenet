<?

//--------------------------------------------------------------------------------------------------
//	edit an image folder
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	load the model
	//----------------------------------------------------------------------------------------------

	if (authHas('folder', 'edit', '') == false) { do403(); }			// check basic permissions
	if ($request['ref'] == '') { do404(); }								// check for ref
	
	require_once($installPath . 'modules/folder/folder.mod.php');

	$model = new folder();
	if ($model->load($request['ref']) == false)  { do404(); }

	//----------------------------------------------------------------------------------------------
	//	check permissions (must be admin or own folder to edit)
	//----------------------------------------------------------------------------------------------

	$auth = false;
	if ($user->data['ofGroup'] == 'admin') { $auth = true; }
	if ($user->data['UID'] == $model->data['createdBy']) { $auth = true; }
	// possibly more to come here...
	if ($auth == false) { do404(); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------

	$page->load($installPath . 'modules/folder/edit.page.php');
	$page->blockArgs['UID'] = $model->data['UID'];
	$page->blockArgs['raUID'] = $model->data['recordAlias'];
	$page->render();

?>
