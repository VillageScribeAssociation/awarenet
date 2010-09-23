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
	if (false == $user->authHas('moblog', 'Moblog_Post', 'edit', $model->UID))
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

	$session->msg('Blog post updated: ' . $model->title, 'ok');
	$page->do302('moblog/' . $model->alias);
	

?>
