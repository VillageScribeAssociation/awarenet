<?

	//require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');
	// ^ sometimes needed for breadcrumbs, etc

//--------------------------------------------------------------------------------------------------
//*	show form to edit a School object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions
	//----------------------------------------------------------------------------------------------
	$UID = $aliases->findRedirect('schools_school');
	if (false == $user->authHas('schools', 'schools_school', 'edit', $UID))
		{ $page->do403('You are not authorized to edit this Schools.'); }

	if ('' == $kapenta->request->ref) { $page->do404(); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------	
	$kapenta->page->load('modules/schools/actions/edit.page.php');
	$kapenta->page->blockArgs['raUID'] = $kapenta->request->ref;
	$kapenta->page->blockArgs['UID'] = $UID;
	$kapenta->page->render();

?>
