<?

//--------------------------------------------------------------------------------------------------
//	add a new calendar event
//--------------------------------------------------------------------------------------------------

	if ($user->authHas('calendar', 'Calendar_Entry', 'edit', 'TODO:UIDHERE') == false) { $page->do403(); }

	require_once($kapenta->installPath . 'modules/calendar/models/entry.mod.php');
	$c = new Calendar_Entry();
	$c->save();
	
	$page->do302('calendar/edit/' . $c->UID);

?>
