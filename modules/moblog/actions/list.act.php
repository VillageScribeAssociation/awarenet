<?

//--------------------------------------------------------------------------------------------------
//*	list all moblog posts by date
//--------------------------------------------------------------------------------------------------

	if (true == $kapenta->user->authHas('moblog', 'moblog_post', 'show')) { $kapenta->page->do403(); }
	$kapenta->page->load('modules/moblog/actions/list.page.php');
	$kapenta->page->render();

?>
