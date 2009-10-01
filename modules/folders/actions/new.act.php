<?

//--------------------------------------------------------------------------------------------------
//	add a new (root) folder
//--------------------------------------------------------------------------------------------------

	if (authHas('folders', 'edit', '') == false) { do403(); }

	require_once($installPath . 'modules/folders/models/folder.mod.php');
	$model = new Folder();
	$model->data['UID'] = createUID();
	$model->save();
	
	do302('folders/edit/' . $model->data['recordAlias']);

?>
