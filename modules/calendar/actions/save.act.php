<?

	require_once($kapenta->installPath . 'modules/calendar/models/entry.mod.php');

//--------------------------------------------------------------------------------------------------
//*	save changes to a Calendar_Entry object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and POST variables
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('action not specified'); }
	if ('saveCalendar' != $_POST['action']) { $kapenta->page->do404('action not supported'); } 
	if (false == array_key_exists('UID', $_POST)) { $kapenta->page->do404('UID not POSTed'); }

	$UID = $_POST['UID'];

	if (false == $kapenta->user->authHas('calendar', 'calendar_entry', 'edit', $UID))
		{ $kapenta->page->do403('You are not authorized to edit this Entry.'); }

	//----------------------------------------------------------------------------------------------
	//	load and update the object
	//----------------------------------------------------------------------------------------------
	$model = new Calendar_Entry($UID);
	if (false == $model->loaded) { $kapenta->page->do404("could not load Entry $UID");}

	//TODO: more error checking here
	foreach($_POST as $field => $value) {
		switch(strtolower($field)) {
			case 'title':		$model->title = $utils->cleanTitle($value); break;
			case 'category':	$model->category = $utils->cleanString($value); break;
			case 'venue':		$model->venue = $utils->cleanString($value); break;
			case 'content':		$model->content = $utils->cleanHtml($value); break;
			case 'year':		$model->year = $utils->cleanString($value); break;
			case 'month':		$model->month = $utils->cleanString($value); break;
			case 'day':			$model->day = $utils->cleanString($value); break;
			case 'eventstart':	$model->eventStart = $utils->cleanString($value); break;
			case 'eventend':	$model->eventEnd = $utils->cleanString($value); break;
			case 'published':	$model->published = $utils->cleanString($value); break;
		}
	}

	$report = $model->save();

	//----------------------------------------------------------------------------------------------
	//	check that object was saved and redirect
	//----------------------------------------------------------------------------------------------
	if ('' == $report) { $kapenta->session->msg('Calendar entry updated.'); }
	else { $kapenta->session->msg('Could not save Entry:<br/>' . $report); }

	if (true == array_key_exists('return', $_POST)) { $kapenta->page->do302($_POST['return']); }
	else { $kapenta->page->do302('/calendar/show/' . $model->alias); }

?>
