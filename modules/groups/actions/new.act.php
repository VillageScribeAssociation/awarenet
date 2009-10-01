<?

//--------------------------------------------------------------------------------------------------
//	add a new group
//--------------------------------------------------------------------------------------------------

	if (authHas('groups', 'edit', '') == false) { do403(); }

	require_once($installPath . 'modules/groups/models/groups.mod.php');
	$model = new group();
	$model->save();
	
	do302('groups/edit/' . $model->data['UID']);

?>
