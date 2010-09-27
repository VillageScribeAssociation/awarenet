<?

//--------------------------------------------------------------------------------------------------
//	save a folder entry
//--------------------------------------------------------------------------------------------------

	if ($user->authHas('files', 'Files_Folder', 'edit', 'TODO:UIDHERE') == false) { $page->do403(); }
	if (array_key_exists('UID', $_POST) == false) { $page->do404(); }
	if ($db->objectExists('folders', $_POST['UID']) == false) { $page->do404(); }

	require_once($kapenta->installPath . 'modules/folders/models/folder.mod.php');

	//----------------------------------------------------------------------------------------------
	//	load model and check against current user
	//----------------------------------------------------------------------------------------------

	$model = new Folder($_POST['UID']);

	$authorised = false;
	if ($model->createdBy == $user->UID) { $authorised = true; }
	if ('admin' == $user->role) { $authorised = false; }
	if ($authorised == false) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	authorised, save any changes
	//----------------------------------------------------------------------------------------------

	//$model->parent = $_POST['UID'];				// moving folders is on hold for now
	$model->title = $_POST['title'];
	$model->description = $_POST['description'];	// not currently used
	$model->save();

	$page->do302('folders/' . $model->alias);

?>
