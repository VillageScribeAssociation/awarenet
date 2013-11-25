<?

//--------------------------------------------------------------------------------------------------
//*	list all moblog posts by date
//--------------------------------------------------------------------------------------------------

	if (true == $user->authHas('moblog', 'moblog_post', 'show')) { $page->do403(); }
	$kapenta->page->load('modules/moblog/actions/list.page.php');
	$kapenta->page->render();

?>
