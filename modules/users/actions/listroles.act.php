<?
	require_once($kapenta->installPath . 'modules/users/models/role.mod.php');

//--------------------------------------------------------------------------------------------------
//*	list all Role objects
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and any POST variables
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('users', 'Users_Role', 'list'))
		{ $page->do403('You are not authorized to list Roles.'); }

	$pageNo = 1;			//%	first page if not specified [int]
	$pageSize = 10;			//%	default number of items per page [int]
	$orderBy = 'name';		//%	default list order [string]

	if (true == array_key_exists('page', $req->args)) { $pageNo = (int)$req->args['page']; }
	if (true == array_key_exists('num', $req->args)) { $pageSize = (int)$req->args['num']; }

	if (true == array_key_exists('by', $req->args)) {	// users may list by these fields
		switch(strtolower($req->args['by'])) {
			case 'name': $orderBy = 'name';	break;
			case 'createdon': $orderBy = 'createdOn';	break;
		}
	}


	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/users/actions/listroles.page.php');
	$page->blockArgs['pageNo'] = $pageNo;
	$page->blockArgs['pageSize'] = $pageSize;
	$page->blockArgs['orderBy'] = $orderBy;
	$page->render();

?>
