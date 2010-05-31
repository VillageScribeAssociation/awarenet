<?

//--------------------------------------------------------------------------------------------------
//	display a moblog post
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check auth and recordAlias
	//----------------------------------------------------------------------------------------------
	//if (authHas('moblog', 'view', '') == false) { do403(''); } // changed to allow public access
	raFindRedirect('moblog', '', 'moblog', $request['ref']);	
	require_once($installPath . 'modules/moblog/models/moblog.mod.php');

	//----------------------------------------------------------------------------------------------
	//	find user who's post this is
	//----------------------------------------------------------------------------------------------

	$model = new Moblog($request['ref']);
	$thisUser = new User($model->data['createdBy']);

	//----------------------------------------------------------------------------------------------
	//	user can create new posts on their own blog
	//----------------------------------------------------------------------------------------------

	$newPostForm = "[[:moblog::newpostformnav:]]\n";
	if ($model->data['createdBy'] != $user->data['UID']) { $newPostForm = ''; }

	//----------------------------------------------------------------------------------------------
	//	increment hit count if not viewed by this user this session
	//----------------------------------------------------------------------------------------------
	$viewKey = $model->data['UID'] . '_' . $user->data['UID'];
	if (array_key_exists('sMoblogView', $_SESSION) == false) { $_SESSION['sMoblogView'] = array(); }
	if (array_key_exists($viewKey , $_SESSION['sMoblogView']) == false) {
		$_SESSION['sMoblogView'][$viewKey] = 'viewed';
		$model->incHitCount();
	} 

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------

	$page->load($installPath . 'modules/moblog/actions/show.page.php');
	$page->blockArgs['raUID'] = $request['ref'];
	$page->blockArgs['UID'] = $model->data['UID'];
	$page->blockArgs['userUID'] = $model->data['createdBy'];
	$page->blockArgs['userRa'] = $thisUser->data['recordAlias'];
	$page->blockArgs['userName'] = $thisUser->getName();
	$page->blockArgs['postTitle'] = $model->data['title'];
	$page->blockArgs['newPostForm'] = $newPostForm;
	$page->render();

?>
