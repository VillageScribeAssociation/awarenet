<?

	require_once($kapenta->installPath . 'modules/moblog/models/post.mod.php');

//--------------------------------------------------------------------------------------------------
//*	save a blog post
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST vars and permissions
	//----------------------------------------------------------------------------------------------

	if (false == array_key_exists('action', $_POST)) { $page->do404('Action not specified.'); }
	//TODO: pick an action and implement it
	if ('saveRecord' != $_POST['action']) { $page->do404('Action not supported.'); }
	if (false == array_key_exists('UID', $_POST)) { $page->do404('UID not given.'); }

	$model = new Moblog_Post($_POST['UID']);
	if (false == $model->loaded) { $page->do404(); }
	if (false == $user->authHas('moblog', 'moblog_post', 'edit', $model->UID))
		{ $page->do403('You cannot edit this blog post.'); }

	//----------------------------------------------------------------------------------------------
	//	update the record	//TODO: use a switch here
	//----------------------------------------------------------------------------------------------
		
	if (true == array_key_exists('title', $_POST))
		{ $model->title = trim($_POST['title']); }

	if (true == array_key_exists('content', $_POST))
		{ $model->content = trim($_POST['content']); }

	if (true == array_key_exists('published', $_POST))
		{ $model->published = $_POST['published']; }

	//----------------------------------------------------------------------------------------------
	//	save it and redirect
	//----------------------------------------------------------------------------------------------

	$report = $model->save();
	if ('' != $report) { $session->msg('Could not have blog post: ' . $report, 'bad'); }
	else {
		if ('yes' == $model->published) {

			//--------------------------------------------------------------------------------------
			//	notify user's friends
			//--------------------------------------------------------------------------------------
			$ext = $model->extArray();
			$title = "Blog update: " . $ext['nameLink'];
			$content = "" 
				. "[[:users::namelink::userUID=" . $model->createdBy . ":]] "
				. "has updated their blog post.";

			$nUID = $notifications->create(
				'moblog', 'moblog_post', $model->UID, 'moblog_editpost', 
				$title, $content, $ext['viewUrl']
			);

			$notifications->addFriends($nUID, $user->UID);
			$notifications->addAdmins($nUID, $user->UID);

			//--------------------------------------------------------------------------------------
			//	raise a microbog event for this
			//--------------------------------------------------------------------------------------
			$args = array(
				'refModule' => 'moblog',
				'refModel' => 'moblog_post',
				'refUID' => $model->UID,
				'message' => '#'. $kapenta->websiteName .' blog - '. $model->title
			);
		}
	}

	$session->msg('Blog post updated: ' . $model->title, 'ok');
	$page->do302('moblog/' . $model->alias);
	

?>
