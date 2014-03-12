<?

	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//*	confirm deletion of a school
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('uid', $kapenta->request->args)) { $kapenta->page->do404('School not specified (UID)'); }

	$model = new Schools_School($kapenta->request->args['uid']);
	if (false == $model->loaded) { $kapenta->page->do404('School not found.'); }

	if (false == $kapenta->user->authHas('schools', 'schools_school', 'delete', $model->UID))
		{ $kapenta->page->do403('You are not authorized to delete this school.'); }

	//----------------------------------------------------------------------------------------------
	//	make the confirmation box and redirect to the school's page
	//----------------------------------------------------------------------------------------------
	$labels = array('UID' => $model->UID, 'raUID' => $model->alias);
	
	$block = $theme->loadBlock('modules/schools/views/confirmdelete.block.php');
	$html = $theme->replaceLabels($labels, $block);
	
	$kapenta->session->msg($html, 'warn');
	$kapenta->page->do302('schools/' . $model->alias);

?>
