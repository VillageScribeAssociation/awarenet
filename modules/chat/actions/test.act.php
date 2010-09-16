<?

//--------------------------------------------------------------------------------------------------
//	testing page for sending messages to/from arbitrary users
//--------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $page->do403(); }
	$page->load('modules/chat/actions/test.page.php');
	$page->render();

?>
