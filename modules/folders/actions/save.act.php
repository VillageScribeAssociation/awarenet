<?

//--------------------------------------------------------------------------------------------------
//	save a folder entry
//--------------------------------------------------------------------------------------------------

	if (authHas('folders', 'edit', '') == false) { do403(); }
	if (array_key_exists('UID', $_POST) == false) { do404(); }
	if (dbRecordExists('folders', $_POST['UID']) == false) { do404(); }

	require_once($installPath . 'modules/folders/models/folder.mod.php');

	//----------------------------------------------------------------------------------------------
	//	load model and check against current user
	//----------------------------------------------------------------------------------------------

	$model = new Folder($_POST['UID']);

	$authorised = false;
	if ($model->data['createdBy'] == $user->data['UID']) { $authorised = true; }
	if ($user->data['ofGroup'] == 'admin') { $authorised = false; }
	if ($authorised == false) { do403(); }

	//----------------------------------------------------------------------------------------------
	//	authorised, save any changes
	//----------------------------------------------------------------------------------------------

	//$model->data['parent'] = $_POST['UID'];				// moving folders is on hold for now
	$model->data['title'] = $_POST['title'];
	$model->data['description'] = $_POST['description'];	// not currently used
	$model->save();

	do302('folders/' . $model->data['recordAlias']);

?>
