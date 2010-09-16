<?

//--------------------------------------------------------------------------------------------------
//	add a new school
//--------------------------------------------------------------------------------------------------

	if ($user->authHas('schools', 'Schools_School', 'edit', 'TODO:UIDHERE') == false) { $page->do403(); }

	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');
	$model = new Schools_School();
	$model->save();
	
	$page->do302('schools/edit/' . $model->UID);

?>
