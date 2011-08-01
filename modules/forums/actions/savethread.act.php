<?

	require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');

//--------------------------------------------------------------------------------------------------
//*	save changes to a Thread object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and POST variables
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $page->do404('Action not specified.'); }
	if ('saveThread' != $_POST['action']) { $page->do404('Action not supported.'); } 
	if (false == array_key_exists('UID', $_POST)) { $page->do404('UID not POSTed.'); }

	$model = new Forums_Thread($_POST['UID']);
	if (false == $model->loaded) { $page->do404("could not load Thread $UID");}

	if (false == $user->authHas('forums', 'Forums_Thread', 'edit', $model->UID))
		{ $page->do403('You are not authorized to edit this Thread.'); }

	//----------------------------------------------------------------------------------------------
	//	load and update the object
	//----------------------------------------------------------------------------------------------
	foreach($_POST as $field => $value) {
		switch(strtolower($field)) {
			case 'title':		$model->title = $utils->cleanTitle($value); 		break;
			case 'content':		$model->content = $utils->cleanHtml($value);	 	break;
			case 'sticky':		$model->sticky = $utils->cleanYesNo($value);		break;

			case 'board':		
				if (true == $db->objectExists('forums_board', $value)) { $model->board = $value; }
				break;
		}
	}

	$report = $model->save();

	//----------------------------------------------------------------------------------------------
	//	check that object was saved and redirect
	//----------------------------------------------------------------------------------------------
	if ('' == $report) { $session->msg('Saved changes to Thread', 'ok'); }
	else { $session->msg('Could not save Thread:<br/>' . $report, 'bad'); }

	if (true == array_key_exists('return', $_POST)) { $page->do302($_POST['return']); }
	else { $page->do302('forums/showthread/' . $model->alias); }

?>
