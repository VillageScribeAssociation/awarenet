<?

//--------------------------------------------------------------------------------------------------
//	add a new calendar event
//--------------------------------------------------------------------------------------------------

	if (authHas('calendar', 'edit', '') == false) { do403(); }

	require_once($installPath . 'modules/calendar/models/calendar.mod.php');
	$c = new Calendar();
	$c->save();
	
	do302('calendar/edit/' . $c->data['UID']);

?>
