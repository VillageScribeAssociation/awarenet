<?

//--------------------------------------------------------------------------------------------------
//*	show the admin console
//--------------------------------------------------------------------------------------------------

	if ('' != $kapenta->request->ref) { $page->do404('Admin page unkown.'); }

	if ('admin' == $user->role) {
		$kapenta->page->load('modules/admin/actions/console.page.php');
		$kapenta->page->render();
	} else {
		$page->do403();  // not logged in as admin, deny
	}

?>
