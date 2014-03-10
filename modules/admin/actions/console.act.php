<?

//--------------------------------------------------------------------------------------------------
//*	show the admin console
//--------------------------------------------------------------------------------------------------

	if ('' != $kapenta->request->ref) { $kapenta->page->do404('Admin page unkown.'); }

	if ('admin' == $user->role) {
		$kapenta->page->load('modules/admin/actions/console.page.php');
		$kapenta->page->render();
	} else {
		$kapenta->page->do403();  // not logged in as admin, deny
	}

?>
