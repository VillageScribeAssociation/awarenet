<?

	require_once($kapenta->installPath . 'modules/sync/models/server.mod.php');
	require_once($kapenta->installPath . 'modules/sync/models/notice.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to add a new server (formatted for nav)
//--------------------------------------------------------------------------------------------------

function sync_clearqueueform($args) {
	global $theme;

	global $user;
	if ('admin' != $user->role) { return false; }
	return $theme->loadBlock('modules/sync/views/clearqueueform.block.php');
}

?>