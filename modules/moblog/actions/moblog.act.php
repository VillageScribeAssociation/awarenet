<?

//--------------------------------------------------------------------------------------------------
//	moblog, all posts of from all blogs ordered by date
//--------------------------------------------------------------------------------------------------
	
	if (authHas('moblog', 'view', '') == false) { do403(''); }

	$page->load($installPath . 'modules/moblog/actions/moblog.page.php');
	$page->allowBlockArgs('page,tag');
	$page->render();

?>
