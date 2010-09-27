<?

//--------------------------------------------------------------------------------------------------
//*	confirm deletion of a forum thread
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and reference
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('uid', $req->args)) { $page->do404('UID not given.'); }
	if (false == $db->objectExists('Forums_Thread', $req->args['uid'])) 
		{ $page->do404('Forum thread not found'); }

	if (false == $user->authHas('forums', 'Forums_Board', 'delete', $req->args['UID']))
		{ $page->do403('You are not authorized to delete this forum thread.'); }	

	//----------------------------------------------------------------------------------------------
	//	make the cofirmation form
	//----------------------------------------------------------------------------------------------
	$thisRa = $aliases->getDefault('Forums_Thread', $req->args['uid']);	
	$labels = array('UID' => $req->args['uid'], 'raUID' => $thisRa);
	
	$block = $theme->loadBlock('modules/forums/views/confirmdeletethread.block.php');
	$html .= $theme->replaceLabels($labels, $block);	
	$session->msg($html, 'warn');

	//----------------------------------------------------------------------------------------------
	//	show confirmation form on item to be deleted
	//----------------------------------------------------------------------------------------------
	$page->do302('forums/showthread/' . $thisRa);

?>
