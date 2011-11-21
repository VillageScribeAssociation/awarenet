<?

//--------------------------------------------------------------------------------------------------
//*	show the admin console
//--------------------------------------------------------------------------------------------------

	if ('' != $req->ref) { $page->do404('Admin page unkown.'); }

	if ('admin' == $user->role) {
		$page->load('modules/admin/actions/console.page.php');
		$page->render();
	} else {
		$page->do403();  // not logged in as admin, deny
	}

?>
