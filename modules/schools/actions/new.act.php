<?

//--------------------------------------------------------------------------------------------------
//	add a new school
//--------------------------------------------------------------------------------------------------

	if (authHas('schools', 'edit', '') == false) { do403(); }

	require_once($installPath . 'modules/schools/models/school.mod.php');
	$model = new School();
	$model->save();
	
	do302('schools/edit/' . $model->data['UID']);

?>
