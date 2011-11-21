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

	if (true == array_key_exists('page', $req->args)) { $pageNo = (int)$req->args['page']; }
	if (true == array_key_exists('num', $req->args)) { $pageSize = (int)$req->args['num']; }

	if (true == array_key_exists('by', $req->args)) {	// users may list by these fields
		switch(strtolower($req->args['by'])) {
			case 'createdon': 	$orderBy = 'createdOn';		break;
			case 'editedon': 	$orderBy = 'editedOn';		break;
		}
	}


	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/badges/actions/list.page.php');
	$page->blockArgs['pageNo'] = $pageNo;
	$page->blockArgs['pageSize'] = $pageSize;
	$page->blockArgs['orderBy'] = $orderBy;
	$page->render();

?>
