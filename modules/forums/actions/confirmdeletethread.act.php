<?

	require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');

//--------------------------------------------------------------------------------------------------
//*	confirm deletion of a forum thread
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and reference
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('UID', $kapenta->request->args)) { $kapenta->page->do404('UID not given.'); }

	$model = new Forums_Thread($kapenta->request->args['UID']);
	if (false == $model->loaded) { $kapenta->page->do404('Forum thread not found'); }

	if (false == $kapenta->user->authHas('forums', 'forums_thread', 'delete', $model->UID))
		{ $kapenta->page->do403('You are not authorized to delete this forum thread.'); }	

	//----------------------------------------------------------------------------------------------
	//	make the cofirmation form
	//----------------------------------------------------------------------------------------------
	$labels = array('UID' => $model->UID, 'raUID' => $model->alias);
	
	$block = $theme->loadBlock('modules/forums/views/confirmdeletethread.block.php');
	$html = $theme->replaceLabels($labels, $block);	
	$kapenta->session->msg($html, 'warn');

	//----------------------------------------------------------------------------------------------
	//	show confirmation form on item to be deleted
	//----------------------------------------------------------------------------------------------
	$kapenta->page->do302('forums/showthread/' . $model->alias);

?>
