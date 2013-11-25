<?

	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//*	creates a web shell iframe
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check reference and permissions
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { $page->do403('Please log in to use the shell.', true); }	

	//----------------------------------------------------------------------------------------------
	//	render the page  //TODO: make a generic window template
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/live/actions/shell.page.php');
	$kapenta->page->blockArgs['jsUserName'] = $user->getName();
	$kapenta->page->render();

?>
