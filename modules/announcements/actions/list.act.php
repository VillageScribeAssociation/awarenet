<?

//--------------------------------------------------------------------------------------------------
//*	list all announcements
//--------------------------------------------------------------------------------------------------
//reqopt: sc - UID of a Schools_School object to filter announcements to [string]

	//----------------------------------------------------------------------------------------------
	//	check user permissions and any reference
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('announcements', 'announcements_announcement', 'show')) {
		$page->do403();
	}
	
	$school = $user->school;
	if (true == array_key_exists('school', $kapenta->request->args)) { $school = $kapenta->request->args['sc']; }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------

	$kapenta->page->load('modules/announcements/actions/list.page.php');
	$kapenta->page->blockArgs['schoolUID'] = $school;
	$kapenta->page->render();

?>
