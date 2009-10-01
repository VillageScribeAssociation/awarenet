<?

//--------------------------------------------------------------------------------------------------
//	installer for gallery module (creates table)
//--------------------------------------------------------------------------------------------------

require_once($installPath . 'modules/messages/models/message.mod.php');

function install_messages_module() {
	global $installPath;
	global $user;

	if ($user->data['ofGroup'] != 'admin') { return false; }

	//----------------------------------------------------------------------------------------------
	//	main messages table
	//----------------------------------------------------------------------------------------------
	$model = new Message();
	$report = $model->install();

	return $report;
}

?>
