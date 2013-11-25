<?
	
	require_once($kapenta->installPath . 'modules/calendar/models/entry.mod.php');

//--------------------------------------------------------------------------------------------------
//*	edit a calendar entry
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check reference and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $page->do404(); }
	$model = new Calendar_Entry($kapenta->request->ref);
	if (false == $model->loaded) { $page->do404('Calendar entry not found.'); }	
	if (false == $user->authHas('calendar', 'calendar_entry', 'edit', $model->UID))
		{ $page->do403('You cannot edit this calendar entry.'); }

	//----------------------------------------------------------------------------------------------
	//	make the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/calendar/actions/edit.page.php');
	$kapenta->page->blockArgs['raUID'] = $kapenta->request->ref;
	$kapenta->page->blockArgs['UID'] = $model->UID;
	$kapenta->page->render();

?>
