<?

//--------------------------------------------------------------------------------------------------
//	add a new forum (by default, bound to the admin's school)
//--------------------------------------------------------------------------------------------------

	if (authHas('forums', 'new', '') == false) { do403(); }

	require_once($installPath . 'modules/forums/models/forum.mod.php');
	$model = new Forum();
	$model->data['UID'] = createUID();
	$model->data['school'] = $user->data['school'];
	$model->save();
	
	do302('forums/edit/' . $model->data['recordAlias']);

?>
