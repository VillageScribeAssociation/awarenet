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
	
	foreach($_POST as $key => $value) {
		switch($key) {
			case 'title':		$model->title = $utils->cleanTitle($value);			break;
			case 'content':		$model->content = $utils->cleanHtml($value);		break;
			case 'published':	$model->published = $utils->cleanYesNo($value);		break;
		}
	}

	//----------------------------------------------------------------------------------------------
	//	save it and redirect
	//----------------------------------------------------------------------------------------------

	$report = $model->save();
	if ('' != $report) { $session->msg('Could not have blog post: ' . $report, 'bad'); }

	$session->msg('Blog post updated: ' . $model->title, 'ok');
	$page->do302('moblog/' . $model->alias);
	

?>
