<?

	require_once($kapenta->installPath . 'modules/calendar/models/entry.mod.php');

//--------------------------------------------------------------------------------------------------
//*	add a new calendar entry
//--------------------------------------------------------------------------------------------------

	if (false == $kapenta->user->authHas('calendar', 'calendar_entry', 'new')) { $kapenta->page->do403(); }

	$model = new Calendar_Entry();
	$model->save();
	
	$kapenta->page->do302('calendar/edit/' . $model->UID);

?>
