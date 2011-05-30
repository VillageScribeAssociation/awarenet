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
	if (false == $user->authHas('calendar', 'calendar_template', 'edit', $UID))
		{ $page->do403('You are not authorized to edit this Templates.'); }


	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/calendar/actions/edittemplate.page.php');
	$page->blockArgs['UID'] = $UID;
	$page->blockArgs['templateUID'] = $UID;
	$page->blockArgs['raUID'] = $req->ref;
	$page->render();

?>
