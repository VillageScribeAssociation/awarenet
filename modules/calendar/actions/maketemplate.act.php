<?

	require_once($kapenta->installPath . 'modules/calendar/models/entry.mod.php');
	require_once($kapenta->installPath . 'modules/calendar/models/template.mod.php');

//--------------------------------------------------------------------------------------------------
//*	make a template from a calendar entry
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and reference
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('calendar', 'calendar_template', 'new')) { $page->do403(); }
	if ('' == $req->ref) { $page->do404('No calendar entry specified.'); }


	$entry = new Calendar_Entry($req->ref);
	if (false == $entry->loaded) { $page->do404('Calendar entry not found.'); }

	//----------------------------------------------------------------------------------------------
	//	make the template
	//----------------------------------------------------------------------------------------------
	$template = new Calendar_Template();
	$template->title = $entry->title;
	$template->category = $entry->category;
	$template->venue = $entry->venue;
	$template->content = $entry->content;
	$template->year = $entry->year;
	$template->month = $entry->month;
	$template->day = $entry->day;
	$template->eventStart = $entry->eventStart;
	$template->eventEnd = $entry->eventEnd;
	$report = $template->save();

	//----------------------------------------------------------------------------------------------
	//	redirect bcak to template
	//----------------------------------------------------------------------------------------------

	if ('' == $report) {
		$session->msg("Created calendar entry from template.<br/>\n", 'ok');
		$page->do302('calendar/edittemplate/' . $template->UID);
	} else {
		$session->msg("Could not create calendar entry from template:<br/>\n" . $report, 'bad');
		$page->do302('calendar/');
	}

?>
