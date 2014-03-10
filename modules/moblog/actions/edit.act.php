<?

	require_once($kapenta->installPath . 'modules/moblog/models/post.mod.php');

//--------------------------------------------------------------------------------------------------
//*	edit a moblog post and associated files/images
//--------------------------------------------------------------------------------------------------
//	users can edit their own blog, admins can do as they please

	//----------------------------------------------------------------------------------------------
	//	check authorisation
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $kapenta->page->do404('No blog post specified.'); }

	$model = new Moblog_Post($kapenta->request->ref);
	if (false == $model->loaded) { $kapenta->page->do404(); }	// no such post

	if (false == $user->authHas('moblog', 'moblog_post', 'edit', $model->UID))
		{ $kapenta->page->do403('You are not authorized to edit this blog post.'); }

	//----------------------------------------------------------------------------------------------
	//	show the edit page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/moblog/actions/edit.page.php');
	$kapenta->page->blockArgs['raUID'] = $model->alias;
	$kapenta->page->blockArgs['UID'] = $model->UID;
	$kapenta->page->blockArgs['postUID'] = $model->UID;
	$kapenta->page->blockArgs['postTitle'] = $model->title;
	$kapenta->page->blockArgs['userRa'] = $user->alias;
	$kapenta->page->blockArgs['userName'] = $user->getName();
	$kapenta->page->render();

?>
