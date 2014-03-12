<?
	//require_once($kapenta->installPath . 'modules/calendar/models/template.mod.php');
	// ^ sometimes needed for breadcrumbs, etc

//--------------------------------------------------------------------------------------------------
//*	show form to edit a Template object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions
	//----------------------------------------------------------------------------------------------
	$UID = $aliases->findRedirect('calendar_template');
	if (false == $kapenta->user->authHas('calendar', 'calendar_template', 'edit', $UID))
		{ $kapenta->page->do403('You are not authorized to edit this Templates.'); }


	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/calendar/actions/edittemplate.page.php');
	$kapenta->page->blockArgs['UID'] = $UID;
	$kapenta->page->blockArgs['templateUID'] = $UID;
	$kapenta->page->blockArgs['raUID'] = $kapenta->request->ref;
	$kapenta->page->render();

?>
