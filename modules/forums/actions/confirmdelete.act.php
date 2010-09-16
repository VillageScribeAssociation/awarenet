<?

//--------------------------------------------------------------------------------------------------
//*	confirm deletion of a Forums_Board object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and reference
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('uid', $req->args)) { $page->do404('UID not given.'); }
	if (false == $db->objectExists('Forums_Board', $req->args['uid'])) 
		{ $page->do404('Board not found.'); }

	if (false == $user->authHas('forums', 'Forums_Board', 'edit', $req->args['uid']))
		{ $page->do403('You cannot delete this forum (insufficient privilege).'); }
	
	//----------------------------------------------------------------------------------------------
	//	make the confirmation form
	//----------------------------------------------------------------------------------------------
	$thisRa = $aliases->getDefault('Forums_Board', $req->args['uid']);
	$labels = array('UID' => $req->args['uid'], 'raUID' => $thisRa);
	$block = $theme->loadBlock('modules/forums/views/confirmdelete.block.php');
	$html .= $theme->replaceLabels($labels, $block);	
	$session->msg($html, 'warn');

	//----------------------------------------------------------------------------------------------
	//	show confirmation for above item to be deleted
	//----------------------------------------------------------------------------------------------
	$page->do302('forums/' . $thisRa);

?>
