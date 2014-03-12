<?

	require_once($kapenta->installPath . 'modules/calendar/models/template.mod.php');

//--------------------------------------------------------------------------------------------------
//*	confirm deletion of a calendar entry
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('UID', $kapenta->request->args))
		{ $kapenta->page->do404('Calendar template not specified.'); }

	$model = new Calendar_Template($kapenta->request->args['UID']);
	if (false == $model->loaded) { $kapenta->page->do404('Calendar template not found.'); }
	if (false == $kapenta->user->authHas('calendar', 'calendar_template', 'delete', $model->UID))
		{ $kapenta->page->do403('You are not authorized to delete this calendar template.'); }

	//----------------------------------------------------------------------------------------------
	//	make the confirmation block and redirect to the entry page
	//----------------------------------------------------------------------------------------------
	$labels = array('UID' => $model->UID, 'raUID' => $model->alias);
	
	$block = $theme->loadBlock('modules/calendar/views/confirmdeletetemplate.block.php');
	$html = $theme->replaceLabels($labels, $block);
	
	$kapenta->session->msg($html, 'warn');
	$kapenta->page->do302('calendar/edittemplate/' . $model->alias);

?>
