<?

//--------------------------------------------------------------------------------------------------
//	testing page for sending messages to/from arbitrary users
//--------------------------------------------------------------------------------------------------

	if ($user->data['ofGroup'] != 'admin') { do403(); }
	$page->load($installPath . 'modules/chat/actions/test.page.php');
	$page->render();

?>
