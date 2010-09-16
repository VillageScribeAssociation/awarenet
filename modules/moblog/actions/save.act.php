<?

	require_once($kapenta->installPath . 'modules/moblog/models/post.mod.php');

//--------------------------------------------------------------------------------------------------
//	save a blog posts
//--------------------------------------------------------------------------------------------------

	if (false == array_key_exists('action', $_POST)) { $page->do404('action not supported'); }

	//----------------------------------------------------------------------------------------------
	//	create a new post given its title
	//----------------------------------------------------------------------------------------------
	

	if ('newMoblogPost' == $_POST['action']) ) {
		if (false == $user->authHas('moblog', 'Moblog_Post', 'new')) 
			{ $page->do403('You are not permitted to create new blog posts.'); }

		$title = $utils->cleanString($_POST['title']);

		if ('' == trim($title)) { 
			$session->msg('Please enter a title for your new blog post.', 'bad');
			$page->do302('moblog/blog/' . $user->alias);
		}

		$model = new Moblog_Post();
		$model->title = $title;
		$model->save();

		$page->do302('moblog/edit/' . $model->alias);
	}

	//----------------------------------------------------------------------------------------------
	//	save a blog posts from edit form
	//----------------------------------------------------------------------------------------------

	if ('saveRecord' == $_POST['action']) {
		if (false == array_key_exists('UID', $_POST)) { $page->do404('UID not given.'); }
		//------------------------------------------------------------------------------------------
		//	load and check permissions
		//------------------------------------------------------------------------------------------

		$model = new Moblog_Post($_POST['UID']);
		if (false == $model->loaded) { $page->do404(); }
		if (false == $user->authHas('moblog', 'Moblog_Post', 'edit', $model->UID))
			{ $page->do403('You cannot edit this blog post.'); }

		//------------------------------------------------------------------------------------------
		//	update the record
		//------------------------------------------------------------------------------------------
		
		if (true == array_key_exists('title', $_POST))
			{ $model->title = trim($_POST['title']); }

		if (true == array_key_exists('content', $_POST))
			{ $model->content = trim($_POST['content']); }

		if (true == array_key_exists('published', $_POST))
			{ $model->published = $_POST['published']; }

		//------------------------------------------------------------------------------------------
		//	save it and redirect
		//------------------------------------------------------------------------------------------

		$report = $model->save();
		if ('' != $report) { $session->msg($report, 'bad'); }
		$page->do302('moblog/' . $model->alias);
	

	} else {
		//------------------------------------------------------------------------------------------
		// 	invalid submission (TODO: error logging here)
		//------------------------------------------------------------------------------------------
		$page->load('modules/home/actions/error.page.php');
		$page->render();
	}

?>
