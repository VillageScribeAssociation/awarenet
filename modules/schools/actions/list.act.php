<?

//--------------------------------------------------------------------------------------------------
//*	list all schools on the system
//--------------------------------------------------------------------------------------------------

	$pageNo = 1;

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('schools', 'schools_school', 'show')) { $page->do403(); }

	if (true == array_key_exists('page', $req->args)) { $pageNo = (int)$req->args['page']; }

	$page->load('modules/schools/actions/list.page.php');
	$page->blockArgs['pageNo'] = $pageNo;
	$page->render();

?>
