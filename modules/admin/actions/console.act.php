<?

//--------------------------------------------------------------------------------------------------
//	show the admin console
//--------------------------------------------------------------------------------------------------

	if ($user->data['ofGroup'] == 'admin') {
		$page->load($installPath . 'modules/admin/actions/console.page.php');
		$page->render();
	} else {
		do403();  // not logged in as admin, deny
	}

?>
