<?

//--------------------------------------------------------------------------------------------------
//*	list all projects on the system
//--------------------------------------------------------------------------------------------------
//reqopt: page - results page to show, 1...n (int) [string]

	$pageNo = 1;

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('projects', 'projects_project', 'show')) { $page->do403(); }
	if (true == array_key_exists('page', $req->args)) { $pageNo = $req->args['page']; }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/projects/actions/list.page.php');
	$page->blockArgs['pageNo'] = $pageNo;
	$page->blockArgs['page'] = $pageNo;
	$page->render();

?>
