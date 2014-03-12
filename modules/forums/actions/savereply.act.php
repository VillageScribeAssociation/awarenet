<?

	require_once($kapenta->installPath . 'modules/forums/models/reply.mod.php');

//--------------------------------------------------------------------------------------------------
//*	save a forum reply
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST vars and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('Action not given.', true); }
	if ('saveReply' != $_POST['action']) { $kapenta->page->do404('Action not supported.', true); }

	$model = new Forums_Reply($_POST['UID']);
	if (false == $model->loaded) { $kapenta->page->do404('Reply not found'); }

	//if ($kapenta->user->UID != $model->createdBy) { $kapenta->page->do403('Not your post to edit.'); }

	//----------------------------------------------------------------------------------------------
	//	update the reply
	//----------------------------------------------------------------------------------------------
	$model->content = $utils->cleanHtml($_POST['content']);
	$report = $model->save();

	if ('' == $report) { 
		$kapenta->session->msg('Saved changes to reply.', 'ok');
	} else {
		$kapenta->session->msg('Changes could not be saved:<br/>' . $report, 'bad');
	}

	$kapenta->page->do302('forums/showthread/' . $model->thread);

?>
