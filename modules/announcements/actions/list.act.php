<?

//--------------------------------------------------------------------------------------------------
//	list all announcements
//--------------------------------------------------------------------------------------------------

	if ($user->authHas('announcements', 'Announcements_Announcement', 'show', 'TODO:UIDHERE') == false) { $page->do403(); }
	
	$school = $user->school;
	if (array_key_exists('sc', $req->args) == true) { $school = $req->args['sc']; }

	$page->load('modules/announcements/actions/list.page.php');
	$page->blockArgs['schoolUID'] = $school;
	$page->render();

?>
