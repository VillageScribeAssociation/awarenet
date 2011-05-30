<?

	require_once($kapenta->installPath . 'modules/calendar/models/entry.mod.php');

//--------------------------------------------------------------------------------------------------
//*	add a new calendar entry
//--------------------------------------------------------------------------------------------------

	if (false == $user->authHas('calendar', 'calendar_entry', 'new')) { $page->do403(); }

	$model = new Calendar_Entry();
	$model->save();
	
	$page->do302('calendar/edit/' . $model->UID);

?>
