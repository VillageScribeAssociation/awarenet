<?

//--------------------------------------------------------------------------------------------------
//*	list all groups in a school (default to user's school)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------

	//TODO: add school-based permission?
	if (false == $user->authHas('groups', 'Groups_Group', 'show')) { $page->do403(); }
	
	$schoolUID = $user->school;
	if (true == array_key_exists('sc', $req->args)) { $schoolUID = $req->args['sc']; }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/groups/actions/list.page.php');
	$page->blockArgs['schoolUID'] = $school;
	$page->render();

?>
