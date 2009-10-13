<?

//--------------------------------------------------------------------------------------------------
//	save a blog posts
//--------------------------------------------------------------------------------------------------

	if (authHas('moblog', 'edit', '') == false) { do403(''); }
	require_once($installPath . 'modules/moblog/models/moblog.mod.php');

	//----------------------------------------------------------------------------------------------
	//	create a new post given its title
	//----------------------------------------------------------------------------------------------

	if ( (array_key_exists('action', $_POST) == true) AND ($_POST['action'] == 'newMoblogPost') ) {

		$title = clean_string($_POST['title']);
		if (trim($title) == '') { 
			$_SESSION['sMessage'] .= "Please enter a title for your new blog post.<br/>\n";
			do302('moblog/blog/' . $user->data['recordAlias']);
		}

		$model = new Moblog();
		$model->data['title'] = $title;
		$model->save();

		do302('moblog/edit/' . $model->data['recordAlias']);

	}

	//----------------------------------------------------------------------------------------------
	//	save a blog posts from edit form
	//----------------------------------------------------------------------------------------------

	if ( (array_key_exists('action', $_POST) == true) 
	     AND (array_key_exists('UID', $_POST) == true)
	     AND ($_POST['action'] == 'saveRecord') 
	     AND (dbRecordExists('moblog', $_POST['UID']) == true) ) {

		//------------------------------------------------------------------------------------------
		//	load and check permissions
		//------------------------------------------------------------------------------------------

		$authorised = false;
		$model = new Moblog($_POST['UID']);
		if ($user->data['ofGroup'] == 'admin') { $authorised = true; }
		if ($user->data['UID'] == $model->data['createdBy']) { $authorised = true; }

		//------------------------------------------------------------------------------------------
		//	update the record
		//------------------------------------------------------------------------------------------
		
		if (array_key_exists('title', $_POST))
			{ $model->data['title'] = trim($_POST['title']); }

		if (array_key_exists('content', $_POST))
			{ $model->data['content'] = trim($_POST['content']); }

		if (array_key_exists('published', $_POST))
			{ $model->data['published'] = $_POST['published']; }

		//------------------------------------------------------------------------------------------
		//	save it and redirect
		//------------------------------------------------------------------------------------------

		$model->save();
		do302('moblog/' . $model->data['recordAlias']);
	

	} else {
		//------------------------------------------------------------------------------------------
		// 	invalid submission (TODO: error logging here)
		//------------------------------------------------------------------------------------------
		$page->load($installPath . 'modules/home/actions/error.page.php');
		$page->render();
	}

?>
