<?

//--------------------------------------------------------------------------------------------------
//*	development/testing action for slideshow
//--------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $page->do403(); }

	$page->load('modules/images/actions/test.slideshow.page.php');
	$page->render();

?>
