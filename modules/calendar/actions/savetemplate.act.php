<?

	require_once($kapenta->installPath . 'modules/calendar/models/template.mod.php');

//--------------------------------------------------------------------------------------------------
//*	save changes to a Template object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and POST variables
	//----------------------------------------------------------------------------------------------
	//if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('Action not specified.'); }
	//if ('saveTemplate' != $_POST['action']) { $kapenta->page->do404('Action not supported.'); } 
	if (false == array_key_exists('UID', $_POST)) { $kapenta->page->do404('UID not POSTed.'); }

	$model = new Calendar_Template($_POST['UID']);
	if (false == $model->loaded) { $kapenta->page->do404("Could not load template $UID");}

	if (false == $kapenta->user->authHas('calendar', 'calendar_template', 'edit', $model->UID))
		{ $kapenta->page->do403('You are not authorized to edit this Template.'); }

	//----------------------------------------------------------------------------------------------
	//	load and update the object
	//----------------------------------------------------------------------------------------------
	//TODO: sanity checks here

	foreach($_POST as $field => $value) {
		switch(strtolower($field)) {
			case 'title':		$model->title = htmlentities($value); 				break;
			case 'category':	$model->category = htmlentities($value);		 	break;
			case 'venue':		$model->venue = htmlentities($value);		 		break;
			case 'content':		$model->content = $value;					 		break;
			case 'year':		$model->year = $utils->cleanString($value); 		break;
			case 'month':		$model->month = $utils->cleanString($value); 		break;
			case 'day':			$model->day = $utils->cleanString($value); 			break;
			case 'eventstart':	$model->eventStart = $utils->cleanString($value); 	break;
			case 'eventend':	$model->eventEnd = $utils->cleanString($value); 	break;
		}
	}

	$report = $model->save();

	//----------------------------------------------------------------------------------------------
	//	check that object was saved and redirect
	//----------------------------------------------------------------------------------------------
	if ('' == $report) { $kapenta->session->msg('Saved changes to Template', 'ok'); }
	else { $kapenta->session->msg('Could not save Template:<br/>' . $report, 'bad'); }

	if (true == array_key_exists('return', $_POST)) { $kapenta->page->do302($_POST['return']); }
	else { $kapenta->page->do301('calendar/edittemplate/' . $model->alias); }

?>
