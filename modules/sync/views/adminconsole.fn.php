<?

//	require_once($kapenta->installPath . 'modules/sync/models/server.mod.php');
//	require_once($kapenta->installPath . 'modules/sync/models/notice.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list of controls for this module as displayed on the admin console
//--------------------------------------------------------------------------------------------------

function sync_adminconsole($args) {
	global $theme;

	global $user;
	if ('admin' != $user->role) { return ''; }

	$html = $theme->loadBlock('modules/sync/views/adminconsole.block.php');

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>