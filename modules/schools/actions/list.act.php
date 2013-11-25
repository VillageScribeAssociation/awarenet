<?

//--------------------------------------------------------------------------------------------------
//*	list all schools on the system
//--------------------------------------------------------------------------------------------------

	$pageNo = 1;

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('schools', 'schools_school', 'show')) { $page->do403(); }

	if (true == array_key_exists('page', $kapenta->request->args)) { $pageNo = (int)$kapenta->request->args['page']; }

	$kapenta->page->load('modules/schools/actions/list.page.php');
	$kapenta->page->blockArgs['pageNo'] = $pageNo;
	$kapenta->page->render();

?>
