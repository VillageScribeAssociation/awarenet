<?

//--------------------------------------------------------------------------------------------------
//	installer for images module (creates table)
//--------------------------------------------------------------------------------------------------

require_once($installPath . 'modules/chat/models/chat.mod.php');

function install_chat_module() {
	global $installPath;
	global $user;
	if ($user->data['ofGroup'] != 'admin') { return false; }

	$model = new Chat();

	$report = '';
	$report .= $model->install();

	return $report;
}

?>
