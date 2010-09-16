<?

//--------------------------------------------------------------------------------------------------
//*	show all posts from a particular users blog, ordered by date and paginated
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	decide which users blog to show
	//----------------------------------------------------------------------------------------------

	$model = new Users_User();

	if (false == $user->authHas('moblog', 'Moblog_Post', 'show')) { $page->do403(); }
	//TODO: more advanced permisions for blog - tie to profile

	if ('' != $req->ref) {
		$model->load($req->ref);
		if (false == $model->loaded) { $page->do404(); }
	} else { $model->load($user->UID); }

	//----------------------------------------------------------------------------------------------
	//	user can create new posts on their own blog
	//----------------------------------------------------------------------------------------------
	$newPostForm = "[[:moblog::newpostformnav:]]\n";
	if ($model->UID != $user->UID) { $newPostForm = ''; }

	//----------------------------------------------------------------------------------------------
	//	show the page
	//----------------------------------------------------------------------------------------------

	$page->load('modules/moblog/actions/blog.page.php');
	$page->blockArgs['userUID'] = $model->UID;
	$page->blockArgs['userRa'] = $model->alias;
	$page->blockArgs['userName'] = $model->getName();
	$page->blockArgs['newPostForm'] = $newPostForm;
	$page->allowBlockArgs('page,tag');
	$page->title = 'awareNet - blogs - ' . $model->getName();
	$page->render();

?>
