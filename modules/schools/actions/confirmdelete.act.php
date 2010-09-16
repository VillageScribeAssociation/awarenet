<?

	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//*	confirm deletion of a school
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('uid', $req->args)) { $page->do404('School not specified (UID)'); }

	$model = new Schools_School($req->args['uid']);
	if (false == $model->loaded) { $page->do404('School not found.'); }

	if (false == $user->authHas('schools', 'Schools_School', 'delete', $model->UID))
		{ $page->do403('You are not authorized to delete this school.'); }

	//----------------------------------------------------------------------------------------------
	//	make the confirmation box and redirect to the school's page
	//----------------------------------------------------------------------------------------------
	$labels = array('UID' => $model->UID, 'raUID' => $model->alias);
	
	$block = $theme->loadBlock('modules/schools/views/confirmdelete.block.php');
	$html = $theme->replaceLabels($labels, $block);
	
	$session->msg($html, 'warn');
	$page->do302('schools/' . $model->alias);

?>
