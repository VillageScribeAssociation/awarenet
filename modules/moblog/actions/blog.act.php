<?

//--------------------------------------------------------------------------------------------------
//	show all posts from a particular users blog, ordered by date and paginated
//--------------------------------------------------------------------------------------------------

	if (authHas('moblog', 'view', '') == false) { do403(); }

	//----------------------------------------------------------------------------------------------
	//	decide which users blog to show
	//----------------------------------------------------------------------------------------------

	$userUID = $user->data['UID'];	// default to own blog
	$model = new User();

	if ($request['ref'] != '') {
		if ($model->load($request['ref']) == false) { do404(); }
		$userUID = $model->data['UID'];
	} else {
		$model->load($userUID);
	}

	$userName = $model->data['firstname'] . ' ' . $model->data['surname'];

	//----------------------------------------------------------------------------------------------
	//	user can create new posts on their own blog
	//----------------------------------------------------------------------------------------------

	$newPostForm = "[[:moblog::newpostformnav:]]\n";
	if ($model->data['UID'] != $user->data['UID']) { $newPostForm = ''; }

	//----------------------------------------------------------------------------------------------
	//	show the page
	//----------------------------------------------------------------------------------------------

	$page->load($installPath . 'modules/moblog/actions/blog.page.php');
	$page->blockArgs['userUID'] = $userUID;
	$page->blockArgs['userRa'] = $model->data['recordAlias'];
	$page->blockArgs['userName'] = $userName;
	$page->blockArgs['newPostForm'] = $newPostForm;
	$page->allowBlockArgs('page,tag');
	$page->data['title'] = ':: awareNet :: people :: ' . $userName . ' :: blog ::';
	$page->render();

?>
