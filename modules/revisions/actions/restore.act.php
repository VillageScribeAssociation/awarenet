<?

	require_once($kapenta->installPath . 'modules/revisions/models/deleted.mod.php');

//--------------------------------------------------------------------------------------------------
//*	undelete an item
//--------------------------------------------------------------------------------------------------
//postarg: UID - UID of a Revisions_Deleted object [string]	

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }

	if (false == array_key_exists('UID', $_POST)) { $kapenta->page->do404('UID not given'); }

	$model = new Revisions_Deleted($_POST['UID']);
	if (false == $model->loaded) { $kapenta->page->do404("Deleted item not found."); }

	if (false == $kapenta->moduleExists($model->refModule)) { $kapenta->page->do404('Unknown module.'); }
	if (false == $kapenta->db->tableExists($model->refModel)) { $kapenta->page->do404('Unknown model / table.'); }

	//----------------------------------------------------------------------------------------------
	//	restore this object
	//----------------------------------------------------------------------------------------------
	$check = $model->restore();
	if (true == $check) {
		$msg = 'Restored object: ' . $model->refModel . '::' . $model->refUID;
		$kapenta->session->msg($msg, 'ok');

		//------------------------------------------------------------------------------------------
		//	restore dependant objects
		//------------------------------------------------------------------------------------------
		$kapenta->session->msgAdmin("Restoring dependants of " . $model->refModel . '::' . $model->refUID);
		$revisions->restoreDependant($model->refModel, $model->refUID);

	} else {
		$msg = 'Couble not restore object: ' . $model->refModel . '::' . $model->refUID;
		$kapenta->session->msg($msg, 'bad');
	}

	//----------------------------------------------------------------------------------------------
	//	redirect back to deleted items
	//----------------------------------------------------------------------------------------------
	$kapenta->page->do302('revisions/listdeleted/');

?>
