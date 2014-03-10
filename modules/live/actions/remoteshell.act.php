<?

	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//*	creates a web shell iframe
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check reference and permissions
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $kapenta->page->do403('Only admins can do this.', true); }	

	//----------------------------------------------------------------------------------------------
	//	render the page  //TODO: make a generic window template
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/live/actions/remoteshell.page.php');
	$kapenta->page->blockArgs['jsUserName'] = $user->getName();
	$kapenta->page->blockArgs['jsUserUID'] = $user->UID;
	$kapenta->page->blockArgs['jsShellSession'] = $kapenta->createUID();
	$kapenta->page->render();

?>
