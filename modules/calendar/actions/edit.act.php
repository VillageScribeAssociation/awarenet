<?

//--------------------------------------------------------------------------------------------------
//	edit a calendar event
//--------------------------------------------------------------------------------------------------

	if (authHas('calendar', 'edit', '') == false) { do403(); }
	
	if ($request['ref'] == '') { do404(); }
	
	$page->load($installPath . 'modules/calendar/actions/edit.page.php');
	$page->blockArgs['raUID'] = $request['ref'];
	$page->render();

?>
