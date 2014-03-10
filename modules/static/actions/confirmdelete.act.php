<?

//--------------------------------------------------------------------------------------------------
//*	confirm deletion of a static page
//--------------------------------------------------------------------------------------------------
//$kapenta->request->ref should contain the UID of the page we're considering deleting

	//----------------------------------------------------------------------------------------------
	//	check reference and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) {$kapenta->page->do302('static/list/'); }
	if (false == $kapenta->db->objectExists('Home_Static', $kapenta->request->ref))
		{ $kapenta->page->do404('static page not found'); }

	if ($user->authHas('home', 'Home_Static', 'edit', $kapenta->request->ref) == false) { $kapenta->page->do403(); }
	
	//----------------------------------------------------------------------------------------------
	//	check reference and permissions
	//----------------------------------------------------------------------------------------------
	$thisRa = $aliases->getDefault('Home_Static', $kapenta->request->ref);

	$labels = array('UID' => $kapenta->request->ref, 'alias' => $thisRa);
	$block = $theme->loadBlock('modules/static/views/confirmdelete.block.php');
	$session->msg($theme->replaceLabels($labels, $block);
	$kapenta->page->do302('static/' . $kapenta->request->ref);
	
?>
