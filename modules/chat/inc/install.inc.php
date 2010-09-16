<?

//--------------------------------------------------------------------------------------------------
//	installer for images module (creates table)
//--------------------------------------------------------------------------------------------------

require_once($kapenta->installPath . 'modules/chat/models/chat.mod.php');

function install_chat_module() {
	global $installPath;
	global $user;
	if ('admin' != $user->role) { return false; }

	$model = new Chat();

	$report = '';
	$report .= $model->install();

	return $report;
}

?>
