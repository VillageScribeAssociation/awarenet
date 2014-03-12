<?

//--------------------------------------------------------------------------------------------------
//*	list all groups in a school (default to user's school)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------

	//TODO: add school-based permission?
	if (false == $kapenta->user->authHas('groups', 'groups_group', 'show')) { $kapenta->page->do403(); }
	
	$schoolUID = $kapenta->user->school;
	if (true == array_key_exists('sc', $kapenta->request->args)) { $schoolUID = $kapenta->request->args['sc']; }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/groups/actions/list.page.php');
	$kapenta->page->blockArgs['schoolUID'] = $schoolUID;
	$kapenta->page->render();

?>
