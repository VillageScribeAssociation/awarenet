<?

//--------------------------------------------------------------------------------------------------
//*	confirm deletion of a moblog post
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and reference
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('uid', $req->args)) { $page->do404('UID not given'); }
	if (false == $db->objectExists('Moblog_Post', $req->args['uid'])) 
		{ $page->do404('no such blog post'); }

	if (false == $user->authHas('moblog', 'Moblog_Post', 'edit', $req->args['uid']))
		{ $page->do403('You are not authorized to delete this group.'); }
	
	//----------------------------------------------------------------------------------------------
	//	make confirmation form
	//----------------------------------------------------------------------------------------------
	$thisRa = $aliases->getDefault('Moblog_Post', $req->args['uid']);		// clunky	
	$labels = array('UID' => $req->args['uid'], 'raUID' => $thisRa);
	$block = $theme->loadBlock('modules/moblog/views/confirmdelete.block.php')
	$html .= $theme->replaceLabels($labels, $block);
	$session->msg($html, 'warn');

	//----------------------------------------------------------------------------------------------
	//	redirect back to post to be deleted
	//----------------------------------------------------------------------------------------------	
	$page->do302('moblog/' . $thisRa);

?>
