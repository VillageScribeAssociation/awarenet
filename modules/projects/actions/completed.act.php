<?

//--------------------------------------------------------------------------------------------------
//*	list all projects on the system
//--------------------------------------------------------------------------------------------------
//reqopt: page - results page to show, 1...n (int) [string]

	$pageNo = 1;

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('projects', 'projects_project', 'show')) { $kapenta->page->do403(); }
	if (true == array_key_exists('page', $kapenta->request->args)) { $pageNo = $kapenta->request->args['page']; }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/projects/actions/completed.page.php');
	$kapenta->page->blockArgs['pageNo'] = $pageNo;
	$kapenta->page->blockArgs['page'] = $pageNo;
	$kapenta->page->render();

?>
