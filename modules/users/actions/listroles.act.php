<?
	require_once($kapenta->installPath . 'modules/users/models/role.mod.php');

//--------------------------------------------------------------------------------------------------
//*	list all Role objects
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and any POST variables
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('users', 'users_role', 'list'))
		{ $page->do403('You are not authorized to list Roles.'); }

	$pageNo = 1;			//%	first page if not specified [int]
	$pageSize = 10;			//%	default number of items per page [int]
	$orderBy = 'name';		//%	default list order [string]

	if (true == array_key_exists('page', $kapenta->request->args)) { $pageNo = (int)$kapenta->request->args['page']; }
	if (true == array_key_exists('num', $kapenta->request->args)) { $pageSize = (int)$kapenta->request->args['num']; }

	if (true == array_key_exists('by', $kapenta->request->args)) {	// users may list by these fields
		switch(strtolower($kapenta->request->args['by'])) {
			case 'name': $orderBy = 'name';	break;
			case 'createdon': $orderBy = 'createdOn';	break;
		}
	}


	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/users/actions/listroles.page.php');
	$kapenta->page->blockArgs['pageNo'] = $pageNo;
	$kapenta->page->blockArgs['pageSize'] = $pageSize;
	$kapenta->page->blockArgs['orderBy'] = $orderBy;
	$kapenta->page->render();

?>
