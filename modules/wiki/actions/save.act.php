<?
	
	require_once($kapenta->installPath . 'modules/wiki/models/article.mod.php');
	require_once($kapenta->installPath . 'modules/wiki/models/revision.mod.php');

//--------------------------------------------------------------------------------------------------
//*	save an edit to a wiki page
//--------------------------------------------------------------------------------------------------

	//------------------------------------------------------------------------------------------
	//	check action
	//------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $page->do404('action not supplied'); }
	if ('savePage' != $_POST['action']) { $page->do404('action not supported'); }

	//------------------------------------------------------------------------------------------
	//	save from edit form
	//------------------------------------------------------------------------------------------
	$model = new Wiki_Article($_POST['UID']);
	if (false == $model->loaded) { $page->do404('no such article'); }
	if (false == $user->authHas('wiki', 'Wiki_Article', 'edit', $model->UID)) { $page->do403(); }

	$model->title = $_POST['title'];
	$model->content = $_POST['content'];
	$model->nav = $_POST['nav'];
	$model->editedBy = $user->UID;
	$model->editedOn = $db->datetime();
	$model->save();
	$thisRa = $model->alias;

	//--------------------------------------------------------------------------------------
	//	save new content in revisions table
	//--------------------------------------------------------------------------------------
	
	$model = new Wiki_Revision();
	$model->refUID = $_POST['UID'];
	$model->articleUID = $_POST['UID'];
	$model->content = $_POST['content'];
	$model->nav = $_POST['nav'];
	$model->title = $_POST['title'];
	$model->type = 'content';
	$model->reason = strip_tags($_POST['reason']);
	$model->editedBy = $user->UID;
	$model->editedOn = $db->datetime();
	$model->save();

	$session->msgAdmin("Saved revision .", 'ok');
	
	//--------------------------------------------------------------------------------------
	//	done, 302 back to the article
	//--------------------------------------------------------------------------------------

	$session->msg("Saved edit to wiki article.", 'ok');
	$page->do302('wiki/' . $thisRa);			

	/*

		//------------------------------------------------------------------------------------------
		//	save from edit talk form
		//------------------------------------------------------------------------------------------

		if ($_POST['action'] == 'saveTalkPage') {

			//--------------------------------------------------------------------------------------
			//	save the current wiki record
			//--------------------------------------------------------------------------------------

			if ($db->objectExists('wiki', $_POST['UID']) == false) { $page->do404(); }

			$model = new Wiki($_POST['UID']);
			$model->talk = $_POST['talk'];
			$model->editedBy = $user->UID;
			$model->editedOn = $db->datetime();
			$model->save();
			$thisRa = $model->alias;

			//--------------------------------------------------------------------------------------
			//	save new content in revisions table
			//--------------------------------------------------------------------------------------

			$model = new WikiRevision();
			$model->refUID = $_POST['UID'];
			$model->content = $_POST['talk'];
			$model->type = 'talk';
			$model->reason = strip_tags($_POST['reason']);
			$model->editedBy = $user->UID;
			$model->editedOn = $db->datetime();
			$model->save();

			//--------------------------------------------------------------------------------------
			//	done, 302 back to the article
			//--------------------------------------------------------------------------------------

			$_SESSION['sMessage'] .= "Saved edit to discussion.<br/>\n";
			$page->do302('wiki/talk/' . $thisRa);			
		}

	}
	*/

?>
