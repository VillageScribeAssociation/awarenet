<?

	require_once($kapenta->installPath . 'modules/badges/models/badge.mod.php');

//--------------------------------------------------------------------------------------------------
//*	list all Badge objects
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and any POST variables
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('badges', 'badges_badge', 'show'))
		{ $page->do403('You are not authorized to see the list of badges.'); }

	$pageNo = 1;				//%	first page if not specified [int]
	$pageSize = 10;				//%	default number of items per page [int]
	$orderBy = 'createdOn';		//%	default list order [string]

	if (true == array_key_exists('page', $kapenta->request->args)) { $pageNo = (int)$kapenta->request->args['page']; }
	if (true == array_key_exists('num', $kapenta->request->args)) { $pageSize = (int)$kapenta->request->args['num']; }

	if (true == array_key_exists('by', $kapenta->request->args)) {	// users may list by these fields
		switch(strtolower($kapenta->request->args['by'])) {
			case 'createdon': 	$orderBy = 'createdOn';		break;
			case 'editedon': 	$orderBy = 'editedOn';		break;
		}
	}


	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/badges/actions/list.page.php');
	$kapenta->page->blockArgs['pageNo'] = $pageNo;
	$kapenta->page->blockArgs['pageSize'] = $pageSize;
	$kapenta->page->blockArgs['orderBy'] = $orderBy;
	$kapenta->page->render();

?>
