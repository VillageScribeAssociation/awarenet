<?

	require_once($kapenta->installPath . 'modules/calendar/models/entry.mod.php');	

//--------------------------------------------------------------------------------------------------
//*	view a calendar entry
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check reference and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) {  
		$session->msg('No entry specified.');
		$page->do302('calendar/');
	}

	$UID = $aliases->findRedirect('calendar_entry');
	$model = new Calendar_Entry($req->ref);
	if (false == $model->loaded) { $page->do404('No such calendar entry.'); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/calendar/actions/show.page.php');
	$page->blockArgs['raUID'] = $model->alias;
	$page->blockArgs['UID'] = $model->UID;
	$page->blockArgs['calendarTitle'] = $model->title;
	$page->blockArgs['calendarCategory'] = $model->category;
	$page->render();

?>
