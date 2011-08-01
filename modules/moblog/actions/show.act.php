<?

	require_once($kapenta->installPath . 'modules/moblog/models/post.mod.php');

//--------------------------------------------------------------------------------------------------
//*	display a moblog post
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check auth and recordAlias
	//----------------------------------------------------------------------------------------------
	$UID = $aliases->findRedirect('moblog_post');	

	//----------------------------------------------------------------------------------------------
	//	find user whose post this is
	//----------------------------------------------------------------------------------------------
	$model = new Moblog_Post($req->ref);
	if (false == $model->loaded) { $page->do404(); }
	if (false == $user->authHas('moblog', 'moblog_post', 'show', $model->UID)) { $page->do403(''); }

	$thisUser = new Users_User($model->createdBy);

	//----------------------------------------------------------------------------------------------
	//	user can create new posts on their own blog
	//----------------------------------------------------------------------------------------------

	$newPostForm = "[[:moblog::newpostformnav:]]\n";
	if ($model->createdBy != $user->UID) { $newPostForm = ''; }

	//----------------------------------------------------------------------------------------------
	//	bump popularity of this item if viewed by someone other than the creator
	//----------------------------------------------------------------------------------------------
	if ($model->createdBy != $user->UID) {
		$args = array(
			'ladder' => 'moblog.all',
			'item' => $model->UID
		);

		$kapenta->raiseEvent('popular', 'popularity_bump', $args);
		//TODO: consider adding ladders for tags and for individual blogs
	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------

	$page->load('modules/moblog/actions/show.page.php');
	$page->blockArgs['raUID'] = $req->ref;
	$page->blockArgs['UID'] = $model->UID;
	$page->blockArgs['userUID'] = $model->createdBy;
	$page->blockArgs['userRa'] = $thisUser->alias;
	$page->blockArgs['userName'] = $thisUser->getName();
	$page->blockArgs['postTitle'] = $model->title;
	$page->blockArgs['newPostForm'] = $newPostForm;
	$page->render();

?>
