<?

	require_once($kapenta->installPath . 'modules/comments/models/comment.mod.php');

//--------------------------------------------------------------------------------------------------
//*	retract a comment (users can retract their own comments, admins can just blast away)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check reference, permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $page->do404(); }

	$model = new Comments_Comment($kapenta->request->ref);
	if (false == $model->loaded) { $page->do404(); }

	$authorised = false;

	if (true == $user->authHas('comments', 'comments_comment', 'retractall'))
		{ $authorised = true; }

	if (true == $user->authHas('comments', 'comments_comment', 'retract', $model->UID))
		{ $authorised = true; }

	if (true == $user->authHas($model->refModule, $model->refModel, 'comments-retract', $model->refUID)) 
		{ $authorised = true; }

	if (false == $authorised) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	blank the comment body
	//----------------------------------------------------------------------------------------------

	$model->comment = '<small>This comment has been retracted by the poster. '
							. $db->datetime() . '</small>';
	$model->save();

	$session->msg("Your comment has been retracted.");

	//----------------------------------------------------------------------------------------------
	//	return to page comment was retected from, or user profile if none supplied
	//----------------------------------------------------------------------------------------------

	if (true == array_key_exists('HTTP_REFERER', $_SERVER)) {
		// TODO, conside the security implications of this
		$referer = $_SERVER['HTTP_REFERER'];
		$referer = str_replace($kapenta->serverPath, '', $referer);
		$referer = str_replace('//', '/', $referer);
		if (substr($referer, 0, 1) == '/') { $referer = substr($referer, 1); }
		$page->do302($referer);

	} else { $page->do302('users/profile/'); }

?>
