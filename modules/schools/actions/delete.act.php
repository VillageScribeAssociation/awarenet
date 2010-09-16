<?

	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//*	delete a Schools_School object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST vars and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $page->do404('Action not specified.'); }
	if ('deleteRecord' != $_POST['action']) { $page->do404('Action not supported.'); }
	if (false == array_key_exists('UID', $_POST)) { $page->do404('School not specified (UID)'); }
 
	$model = new Schools_School($_POST['UID']);
	if (false == $user->authHas('schools', 'Schools_School', 'edit', $model->UID))
		{ $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	delete and redirect back to list of schools
	//----------------------------------------------------------------------------------------------
	$model->delete();		
	$session->msg("Deleted school: " . $model->name);
	$page->do302('schools/');
	  
?>
