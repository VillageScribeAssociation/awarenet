<?

//--------------------------------------------------------------------------------------------------
//*	show all posts from a particular users blog, ordered by date and paginated
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	decide which users blog to show
	//----------------------------------------------------------------------------------------------

	$model = new Users_User();

	if (false == $user->authHas('moblog', 'moblog_post', 'show')) { $page->do403(); }
	//TODO: more advanced permisions for blog - tie to profile

	if ('' != $kapenta->request->ref) {
		$model->load($kapenta->request->ref);
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

	$kapenta->page->load('modules/moblog/actions/blog.page.php');
	$kapenta->page->blockArgs['userUID'] = $model->UID;
	$kapenta->page->blockArgs['userRa'] = $model->alias;
	$kapenta->page->blockArgs['userName'] = $model->getName();
	$kapenta->page->blockArgs['newPostForm'] = $newPostForm;
	$page->allowBlockArgs('page,tag');
	$page->title = 'awareNet - blogs - ' . $model->getName();
	$kapenta->page->render();

?>
