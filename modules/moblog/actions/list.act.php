<?

//--------------------------------------------------------------------------------------------------
//	list all moblog posts by date
//--------------------------------------------------------------------------------------------------

	if (authHas('moblog', 'view', '') == false) { do403(); }
	$page->load($installPath . 'modules/moblog/actions/list.page.php');
	$page->render();

?>
