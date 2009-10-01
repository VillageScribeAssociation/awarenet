<?

//--------------------------------------------------------------------------------------------------
//	create a new forum thread
//--------------------------------------------------------------------------------------------------

	if ( (array_key_exists('action', $_POST) == true)
	   && ($_POST['action'] == 'newThread')
	   && (array_key_exists('forum', $_POST) == true)
	   && (dbRecordExists('forums', $_POST['forum']) == true) ) {

		require_once($installPath . 'modules/forums/models/forum.mod.php');
		require_once($installPath . 'modules/forums/models/forumthread.mod.php');

		//------------------------------------------------------------------------------------------
		//	check permissions
		//------------------------------------------------------------------------------------------
		// TODO

		//------------------------------------------------------------------------------------------
		//	create thread
		//------------------------------------------------------------------------------------------

		$threadTitle = clean_string($_POST['title']);
		if (trim($threadTitle) == '') { $threadTitle = '(No Subject)'; }

		$model = new ForumThread();
		$model->data['forum'] = clean_string($_POST['forum']);
		$model->data['title'] = $threadTitle;
		$model->data['content'] = strip_tags($_POST['content']);
		$model->save();

		//------------------------------------------------------------------------------------------
		//	increment thread count on the forum
		//------------------------------------------------------------------------------------------
		$forum = new Forum($model->data['forum']);
		$forum->data['threads'] += 1;
		$forum->save();

		//------------------------------------------------------------------------------------------
		//	redirect back to forum	// TODO: redirect to new thread?
		//------------------------------------------------------------------------------------------
		$forumRa = raGetDefault('forums', $_POST['forum']);
		do302('forums/' . $forumRa);		

	} else { do403(); }

?>
