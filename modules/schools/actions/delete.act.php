<?

	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//*	delete a Schools_School object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST vars and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('Action not specified.'); }
	if ('deleteRecord' != $_POST['action']) { $kapenta->page->do404('Action not supported.'); }
	if (false == array_key_exists('UID', $_POST)) { $kapenta->page->do404('School not specified (UID)'); }
 
	$model = new Schools_School($_POST['UID']);
	if (false == $kapenta->user->authHas('schools', 'schools_school', 'edit', $model->UID))
		{ $kapenta->page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	delete and redirect back to list of schools
	//----------------------------------------------------------------------------------------------
	$model->delete();		
	$kapenta->session->msg("Deleted school: " . $model->name);
	$kapenta->page->do302('schools/');
	  
?>
