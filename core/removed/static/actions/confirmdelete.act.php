<?

//--------------------------------------------------------------------------------------------------
//*	confirm deletion of a static page
//--------------------------------------------------------------------------------------------------
//$req->ref should contain the UID of the page we're considering deleting

	//----------------------------------------------------------------------------------------------
	//	check reference and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) {$page->do302('static/list/'); }
	if (false == $db->objectExists('Home_Static', $req->ref))
		{ $page->do404('static page not found'); }

	if ($user->authHas('home', 'Home_Static', 'edit', $req->ref) == false) { $page->do403(); }
	
	//----------------------------------------------------------------------------------------------
	//	check reference and permissions
	//----------------------------------------------------------------------------------------------
	$thisRa = $aliases->getDefault('Home_Static', $req->ref);

	$labels = array('UID' => $req->ref, 'alias' => $thisRa);
	$block = $theme->loadBlock('modules/static/views/confirmdelete.block.php');
	$session->msg($theme->replaceLabels($labels, $block);
	$page->do302('static/' . $req->ref);
	
?>
