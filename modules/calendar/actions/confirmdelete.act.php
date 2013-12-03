<?

	require_once($kapenta->installPath . 'modules/calendar/models/entry.mod.php');

//--------------------------------------------------------------------------------------------------
//*	confirm deletion of a calendar entry
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('UID', $kapenta->request->args))
		{ $page->do404('Calendar entry not specified.'); }

	$model = new Calendar_Entry($kapenta->request->args['UID']);
	if (false == $model->loaded) { $page->do404('Calendar entry not found.'); }
	if (false == $user->authHas('calendar', 'calendar_entry', 'delete', $model->UID))
		{ $page->do403('You are not authorized to delete this calendar entry.'); }

	//----------------------------------------------------------------------------------------------
	//	make the confirmation block and redirect to the entry page
	//----------------------------------------------------------------------------------------------
	$labels = array('UID' => $model->UID, 'raUID' => $model->alias);
	
	$block = $theme->loadBlock('modules/calendar/views/confirmdelete.block.php');
	$html = $theme->replaceLabels($labels, $block);
	
	$session->msg($html, 'warn');
	$page->do302('calendar/' . $model->alias);

?>
