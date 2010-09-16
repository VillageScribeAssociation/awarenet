<?

	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//*	display the current user's notifications
//--------------------------------------------------------------------------------------------------
//TODO: allow users to see other people's activity feeds

	$pageNo = 1;

	//----------------------------------------------------------------------------------------------
	//	check arguments and authorization
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { $page->do403(); }	// user must be logged in
	if (true == array_key_exists('page', $req->args)) { $pageNo = (int)$req->args['page']; }


	$model = $user;

	if (('' != $req->ref) && ('admin' == $user->role)) {
		// only admins can see other peoples notification feed
		$model = new Users_User($req->ref);
		if (false == $model->loaded) { $page->do404(); }
	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/notifications/actions/show.page.php');
	$page->blockArgs['userUID'] = $model->UID;
	$page->blockArgs['userRa'] = $model->alias;
	$page->blockArgs['userName'] = $model->getName();
	$page->blockArgs['pageNo'] = $pageNo;
	$page->render()

?>
