<?

	require_once($kapenta->installPath . 'modules/chat/models/room.mod.php');

//--------------------------------------------------------------------------------------------------
//*	list all Room objects
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and any POST variables
	//----------------------------------------------------------------------------------------------
	//if (false == $user->authHas('chat', 'chat_room', 'list'))
	//	{ $page->do403('You are not authorized to list Rooms.'); }

	$pageNo = 1;				//%	first page if not specified [int]
	$pageSize = 10;				//%	default number of items per page [int]
	$orderBy = 'createdOn';		//%	default list order [string]

	if (true == array_key_exists('page', $req->args)) { $pageNo = (int)$req->args['page']; }
	if (true == array_key_exists('num', $req->args)) { $pageSize = (int)$req->args['num']; }

	if (true == array_key_exists('by', $req->args)) {	// users may list by these fields
		switch(strtolower($req->args['by'])) {
		}
	}


	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/chat/actions/listrooms.page.php');
	$page->blockArgs['pageNo'] = $pageNo;
	$page->blockArgs['pageSize'] = $pageSize;
	$page->blockArgs['orderBy'] = $orderBy;
	$page->render();

?>
