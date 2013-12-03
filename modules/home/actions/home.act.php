<?

//--------------------------------------------------------------------------------------------------
//*	action to show the home page
//--------------------------------------------------------------------------------------------------
//+	this is a stub.  a more advanced version would allow one to choose which static page is Home
//+	according to a system setting.  TODO

	//$kapenta->request->ref = $kapenta->registry->get('home.frontpage');			// default from registry
	//if ('' == $kapenta->request->ref) { $kapenta->request->ref = 'frontpage'; }		// fallback - previous default
	//include $kapenta->installPath . 'modules/home/actions/show.act.php';

	if ('public' == $user->role) {
		$kapenta->page->load('modules/home/actions/home.page.php');
		$kapenta->page->render();
	} else {
		$page->do302('notifications/');
	}

?>
