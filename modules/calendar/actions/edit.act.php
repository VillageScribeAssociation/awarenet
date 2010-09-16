<?

//--------------------------------------------------------------------------------------------------
//	edit a calendar event
//--------------------------------------------------------------------------------------------------

	if ($user->authHas('calendar', 'Calendar_Entry', 'edit', 'TODO:UIDHERE') == false) { $page->do403(); }
	
	if ('' == $req->ref) { $page->do404(); }
	
	$page->load('modules/calendar/actions/edit.page.php');
	$page->blockArgs['raUID'] = $req->ref;
	$page->render();

?>
