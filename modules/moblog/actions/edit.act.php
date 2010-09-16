<?

	require_once($kapenta->installPath . 'modules/moblog/models/post.mod.php');

//--------------------------------------------------------------------------------------------------
//*	edit a moblog post and associated files/images
//--------------------------------------------------------------------------------------------------
//	users can edit their own blog, admins can do as they please

	//----------------------------------------------------------------------------------------------
	//	check authorisation
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $page->do404('No blog post specified.'); }

	$model = new Moblog_Post($req->ref);
	if (false == $model->loaded) { $page->do404(); }	// no such post

	if (false == $user->authHas('moblog', 'Moblog_Post', 'edit', $model->UID))
		{ $page->do403('You are not authorized to edit this blog post.'); }

	//----------------------------------------------------------------------------------------------
	//	show the edit page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/moblog/actions/edit.page.php');
	$page->blockArgs['raUID'] = $req->ref;
	$page->blockArgs['postUID'] = $model->UID;
	$page->blockArgs['postTitle'] = $model->title;
	$page->blockArgs['userRa'] = $user->alias;
	$page->blockArgs['userName'] = $user->getName();
	$page->render();

?>
