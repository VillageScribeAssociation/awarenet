<?

	require_once($kapenta->installPath . 'modules/code/models/file.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/package.mod.php');

//--------------------------------------------------------------------------------------------------
//*	delete a record
//--------------------------------------------------------------------------------------------------

    if ('admin' !== $kapenta->user->role) {
        $kapenta->page->do403();
    }

	//----------------------------------------------------------------------------------------------	
	//	check POST vars and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('Action not given.'); }
	if ('deleteRecord' != $_POST['action']) { $kapenta->page->do404('Action not supported.'); }

	if (false == array_key_exists('UID', $_POST)) { $kapenta->page->do404('UID not given'); }

	$model = new Code_File($_POST['UID']);
	if (false == $model->loaded) { $kapenta->page->do404('Code file not found.'); }
	if (false == $user->authHas('code', 'code_file', 'delete', $model->UID)) { $kapenta->page->do403(); }

	$package = new Code_Package($model->package);
	if (false == $package->loaded) { $kapenta->page->do404('Unknown package.'); }

	//----------------------------------------------------------------------------------------------	
	//	delete the object and increment revision
	//----------------------------------------------------------------------------------------------
	$check = $model->delete();
	if (true == $check) {
		$session->msg("Deleted item: " . $model->title, 'ok');

		$package->revision = ((int)$package->revision + 1);
		$package->save();

	} else {
		$session->msg("Could not delete item: " . $model->title, 'bad');
	}

	//----------------------------------------------------------------------------------------------	
	//	redirect back to package
	//----------------------------------------------------------------------------------------------
	$kapenta->page->do302('code/package/' . $package->UID);

?>
