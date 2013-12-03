<?

	require_once($kapenta->installPath . 'modules/calendar/models/entry.mod.php');	

//--------------------------------------------------------------------------------------------------
//*	view a calendar entry
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check reference and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) {  
		$session->msg('No entry specified.');
		$page->do302('calendar/');
	}

	$UID = $aliases->findRedirect('calendar_entry');
	$model = new Calendar_Entry($kapenta->request->ref);
	if (false == $model->loaded) { $page->do404('No such calendar entry.'); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/calendar/actions/show.page.php');
	$kapenta->page->blockArgs['raUID'] = $model->alias;
	$kapenta->page->blockArgs['UID'] = $model->UID;
	$kapenta->page->blockArgs['calendarTitle'] = $model->title;
	$kapenta->page->blockArgs['calendarCategory'] = $model->category;
	$kapenta->page->render();

?>
