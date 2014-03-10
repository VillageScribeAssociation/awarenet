<?

	require_once($kapenta->installPath . 'modules/alias/models/alias.mod.php');

//--------------------------------------------------------------------------------------------------
//*	development action (reserved)
//--------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $kapenta->page->do403(); }

?>
