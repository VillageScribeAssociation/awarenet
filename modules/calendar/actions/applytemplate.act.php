<?

	require_once($kapenta->installPath . 'modules/calendar/models/template.mod.php');
	require_once($kapenta->installPath . 'modules/calendar/models/entry.mod.php');

//--------------------------------------------------------------------------------------------------
//*	make a new calendar entry from a template
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('calendar', 'calendar_entry', 'new')) { $kapenta->page->do403(); }
	if (false == array_key_exists('UID', $_POST)) { $kapenta->page->do404('Template not given.'); }
	$template = new Calendar_Template($_POST['UID']);
	if (false == $template->loaded) { $kapenta->page->do404('Template not found.'); }

	//----------------------------------------------------------------------------------------------
	//	make the entry
	//----------------------------------------------------------------------------------------------
	//TODO: sanitize and sanity check these values

	$model = new Calendar_Entry();
	foreach($_POST as $field => $value) {
		switch(strtolower($field)) {
			case 'title':		$model->title = htmlentities($value);	 			break;
			case 'category':	$model->category = htmlentities($value); 			break;
			case 'venue':		$model->venue = htmlentities($value);		 		break;
			case 'content':		$model->content = $value;					 		break;
			case 'year':		$model->year = $value;						 		break;
			case 'month':		$model->month = $utils->cleanString($value); 		break;
			case 'day':			$model->day = $utils->cleanString($value); 			break;
			case 'eventstart':	$model->eventStart = $utils->cleanString($value); 	break;
			case 'eventend':	$model->eventEnd = $utils->cleanString($value); 	break;
		}
	}
	$report = $model->save();

	//----------------------------------------------------------------------------------------------
	//	redirect back to template form
	//----------------------------------------------------------------------------------------------
	if ('' == $report) {
		$ext = $model->extArray();
		$msg = 'Created calendar entry from template: ' . $ext['nameLink'];
		$session->msg($msg, 'ok');
		$kapenta->page->do302('calendar/edittemplate/' . $template->UID);
	} else {
		$session->msg('Could not create calendar entry from template:<br/>' . $report, 'bad');
		$kapenta->page->do302('calendar/edittemplate/' . $template->UID);
	}


?>
