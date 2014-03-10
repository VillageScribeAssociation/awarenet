<?

	require_once($kapenta->installPath . 'modules/revisions/models/deleted.mod.php');

//--------------------------------------------------------------------------------------------------
//*	undelete an item
//--------------------------------------------------------------------------------------------------
//postarg: UID - UID of a Revisions_Deleted object [string]	

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	if (false == array_key_exists('UID', $_POST)) { $page->do404('UID not given'); }

	$model = new Revisions_Deleted($_POST['UID']);
	if (false == $model->loaded) { $page->do404("Deleted item not found."); }

	if (false == $kapenta->moduleExists($model->refModule)) { $page->do404('Unknown module.'); }
	if (false == $kapenta->db->tableExists($model->refModel)) { $page->do404('Unknown model / table.'); }

	//----------------------------------------------------------------------------------------------
	//	restore this object
	//----------------------------------------------------------------------------------------------
	$check = $model->restore();
	if (true == $check) {
		$msg = 'Restored object: ' . $model->refModel . '::' . $model->refUID;
		$session->msg($msg, 'ok');

		//------------------------------------------------------------------------------------------
		//	restore dependant objects
		//------------------------------------------------------------------------------------------
		$session->msgAdmin("Restoring dependants of " . $model->refModel . '::' . $model->refUID);
		$revisions->restoreDependant($model->refModel, $model->refUID);

	} else {
		$msg = 'Couble not restore object: ' . $model->refModel . '::' . $model->refUID;
		$session->msg($msg, 'bad');
	}

	//----------------------------------------------------------------------------------------------
	//	redirect back to deleted items
	//----------------------------------------------------------------------------------------------
	$page->do302('revisions/listdeleted/');

?>
