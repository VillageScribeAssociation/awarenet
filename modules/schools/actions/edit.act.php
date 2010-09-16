<?

	//require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');
	// ^ sometimes needed for breadcrumbs, etc

//--------------------------------------------------------------------------------------------------
//*	show form to edit a School object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions
	//----------------------------------------------------------------------------------------------
	$UID = $aliases->findRedirect('Schools_School');
	if (false == $user->authHas('schools', 'Schools_School', 'edit', $UID))
		{ $page->do403('You are not authorized to edit this Schools.'); }

	if ('' == $req->ref) { $page->do404(); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------	
	$page->load('modules/schools/actions/edit.page.php');
	$page->blockArgs['raUID'] = $req->ref;
	$page->blockArgs['UID'] = $UID;
	$page->render();

?>
