<?

	require_once($installPath . 'modules/sync/models/servers.mod.php');
	require_once($installPath . 'modules/sync/models/sync.mod.php');

//--------------------------------------------------------------------------------------------------
//	form to add a new server (formatted for nav)
//--------------------------------------------------------------------------------------------------

function sync_addserverform($args) {
	global $user;
	if ($user->data['ofGroup'] != 'admin') { return false; }
	return loadBlock('modules/sync/views/addserverform.block.php');
}



?>