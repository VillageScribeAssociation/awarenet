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
	if (true == array_key_exists('page', $kapenta->request->args)) { $pageNo = (int)$kapenta->request->args['page']; }


	$model = $user;

	if (('' != $kapenta->request->ref) && ('admin' == $user->role)) {
		// only admins can see other peoples notification feed
		$model = new Users_User($kapenta->request->ref);
		if (false == $model->loaded) { $page->do404(); }
	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/notifications/actions/show.page.php');
	$kapenta->page->blockArgs['userUID'] = $model->UID;
	$kapenta->page->blockArgs['userRa'] = $model->alias;
	$kapenta->page->blockArgs['userName'] = $model->getName();
	$kapenta->page->blockArgs['pageNo'] = $pageNo;

	if (true == $session->get('mobile')) {
		//------------------------------------------------------------------------------------------
		//	require javascript and css which may be needed by mobile clients (AJAX)
		//------------------------------------------------------------------------------------------
		$page->requireCss('%%serverPath%%modules/images/css/pikachoose.mobile.css');
		$page->requireJs('%%serverPath%%modules/images/js/jquery.pikachoose.full.js');
	} else {
		//------------------------------------------------------------------------------------------
		//	require javascript and css which may be needed by dynamically loaded content
		//------------------------------------------------------------------------------------------
		$page->requireCss('%%serverPath%%modules/images/css/pikachoose.css');
		$page->requireJs('%%serverPath%%modules/images/js/jquery.pikachoose.full.js');
	}

	$kapenta->page->render()

?>
