<?

//--------------------------------------------------------------------------------------------------
//*	edit a project abstract (since moved to editabstract.act.php) 	//TODO: remove this if possible
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check reference and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $page->do404(); }
	$UID = $aliases->findRedirect('projects_project');
	if (false == $user->authHas('projects', 'projects_project', 'edit', $UID)) 
		{ $page->do403('You are not authorized to edit this project.'); }

	//----------------------------------------------------------------------------------------------
	//	check reference and permissions
	//----------------------------------------------------------------------------------------------
	$page->do302('projects/editabstract/' . $kapenta->request->ref);

?>
