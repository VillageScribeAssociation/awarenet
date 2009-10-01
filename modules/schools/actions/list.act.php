<?

//--------------------------------------------------------------------------------------------------
//	list all schools on the system
//--------------------------------------------------------------------------------------------------

	if (authHas('schools', 'view', '') == false) { do403(); }
	$page->load($installPath . 'modules/schools/actions/list.page.php');
	$page->render();

?>
