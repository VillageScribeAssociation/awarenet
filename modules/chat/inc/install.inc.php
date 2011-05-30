<?

//--------------------------------------------------------------------------------------------------
//*	installer for chat module (creates table)
//--------------------------------------------------------------------------------------------------
//TODO: remove, this is obsolete

require_once($kapenta->installPath . 'modules/chat/models/chat.mod.php');

function install_chat_module() {
	global $kapenta, $user;
	if ('admin' != $user->role) { return false; }

	$model = new Chat();

	$report = '';
	$report .= $model->install();

	return $report;
}

?>
