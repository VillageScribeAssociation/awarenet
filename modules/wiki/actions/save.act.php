<?

//--------------------------------------------------------------------------------------------------
//	save an edit to a wiki page
//--------------------------------------------------------------------------------------------------

	if (authHas('wiki', 'edit', '') == false) { do403(); }
	require_once($installPath . 'modules/wiki/models/wiki.mod.php');
	require_once($installPath . 'modules/wiki/models/wikirevision.mod.php');

	if (array_key_exists('action', $_POST)) {

		//------------------------------------------------------------------------------------------
		//	create a new page
		//------------------------------------------------------------------------------------------

		if ($_POST['action'] == 'newPage') {
			
			// ensure a title was supplied
			if ((array_key_exists('title', $_POST) == false) OR (trim($_POST['title']) == '')) { 
				$_SESSION['sMessage'] .= 'Please choose a title for your new article.';
				do302('/wiki/new/');
			}

			//--------------------------------------------------------------------------------------
			//	save the current wiki record
			//--------------------------------------------------------------------------------------

			$model = new Wiki();
			$model->data['title'] = $_POST['title'];
			$model->data['content'] = '';
			$model->data['locked'] = 'user';
			$model->save();

			//--------------------------------------------------------------------------------------
			//	done, 302 to the article edit form
			//--------------------------------------------------------------------------------------

			$_SESSION['sMessage'] .= "Created new wiki article.<br/>\n";
			do302('wiki/edit/' . $model->data['recordAlias']);			

		}

		//------------------------------------------------------------------------------------------
		//	save from edit form
		//------------------------------------------------------------------------------------------

		if ($_POST['action'] == 'savePage') {

			//--------------------------------------------------------------------------------------
			//	save the current wiki record
			//--------------------------------------------------------------------------------------

			if (dbRecordExists('wiki', $_POST['UID']) == false) { do404(); }

			$model = new Wiki($_POST['UID']);
			$model->data['title'] = $_POST['title'];
			$model->data['content'] = $_POST['content'];
			$model->data['editedBy'] = $user->data['UID'];
			$model->data['editedOn'] = mk_mysql_datetime(time());
			$model->save();
			$thisRa = $model->data['recordAlias'];

			//--------------------------------------------------------------------------------------
			//	save new content in revisions table
			//--------------------------------------------------------------------------------------

			$model = new WikiRevision();
			$model->data['refUID'] = $_POST['UID'];
			$model->data['content'] = $_POST['content'];
			$model->data['type'] = 'content';
			$model->data['reason'] = strip_tags($_POST['reason']);
			$model->data['editedBy'] = $user->data['UID'];
			$model->data['editedOn'] = mk_mysql_datetime(time());
			$model->save();

			//--------------------------------------------------------------------------------------
			//	done, 302 back to the article
			//--------------------------------------------------------------------------------------

			$_SESSION['sMessage'] .= "Saved edit to wiki article.<br/>\n";
			do302('wiki/' . $thisRa);			
		}

		//------------------------------------------------------------------------------------------
		//	save from edit talk form
		//------------------------------------------------------------------------------------------

		if ($_POST['action'] == 'saveTalkPage') {

			//--------------------------------------------------------------------------------------
			//	save the current wiki record
			//--------------------------------------------------------------------------------------

			if (dbRecordExists('wiki', $_POST['UID']) == false) { do404(); }

			$model = new Wiki($_POST['UID']);
			$model->data['talk'] = $_POST['talk'];
			$model->data['editedBy'] = $user->data['UID'];
			$model->data['editedOn'] = mk_mysql_datetime(time());
			$model->save();
			$thisRa = $model->data['recordAlias'];

			//--------------------------------------------------------------------------------------
			//	save new content in revisions table
			//--------------------------------------------------------------------------------------

			$model = new WikiRevision();
			$model->data['refUID'] = $_POST['UID'];
			$model->data['content'] = $_POST['talk'];
			$model->data['type'] = 'talk';
			$model->data['reason'] = strip_tags($_POST['reason']);
			$model->data['editedBy'] = $user->data['UID'];
			$model->data['editedOn'] = mk_mysql_datetime(time());
			$model->save();

			//--------------------------------------------------------------------------------------
			//	done, 302 back to the article
			//--------------------------------------------------------------------------------------

			$_SESSION['sMessage'] .= "Saved edit to discussion.<br/>\n";
			do302('wiki/talk/' . $thisRa);			
		}

	}

?>
