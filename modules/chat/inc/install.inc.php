<?

	require_once($kapenta->installPath . 'core/dbdriver/mysqladmin.dbd.php');
	require_once($kapenta->installPath . 'modules/chat/models/hash.mod.php');
	require_once($kapenta->installPath . 'modules/chat/models/inbox.mod.php');
	require_once($kapenta->installPath . 'modules/chat/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/chat/models/messageout.mod.php');
	require_once($kapenta->installPath . 'modules/chat/models/peer.mod.php');
	require_once($kapenta->installPath . 'modules/chat/models/room.mod.php');
	require_once($kapenta->installPath . 'modules/chat/models/session.mod.php');

//--------------------------------------------------------------------------------------------------
//*	install script for Chat module
//--------------------------------------------------------------------------------------------------
//+	reports are human-readable HTML, with script-readable HTML comments

//--------------------------------------------------------------------------------------------------
//|	install the Chat module
//--------------------------------------------------------------------------------------------------
//returns: html report or empty string if not authorized [string][bool]

function chat_install_module() {
	global $user;
	global $kapenta;

	if ('admin' != $user->role) { return ''; }

	$report = '';				//% return value [string:html]

	$dba = $kapenta->getDBAdminDriver();

	//----------------------------------------------------------------------------------------------
	//	create or upgrade chat_hash table
	//----------------------------------------------------------------------------------------------
	$model = new Chat_Hash();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	create or upgrade chat_inbox table
	//----------------------------------------------------------------------------------------------
	$model = new Chat_Inbox();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	create or upgrade chat_membership table
	//----------------------------------------------------------------------------------------------
	$model = new Chat_Membership();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	create or upgrade chat_messageout table
	//----------------------------------------------------------------------------------------------
	$model = new Chat_MessageOut();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	create or upgrade chat_peer table
	//----------------------------------------------------------------------------------------------
	$model = new Chat_Peer();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	create or upgrade chat_room table
	//----------------------------------------------------------------------------------------------
	$model = new Chat_Room();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	create or upgrade chat_session table
	//----------------------------------------------------------------------------------------------
	$model = new Chat_Session();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------
	return $report;
}

//--------------------------------------------------------------------------------------------------
//|	discover if this module is installed
//--------------------------------------------------------------------------------------------------
//:	if installed correctly report will contain HTML comment <!-- installed correctly -->
//returns: HTML installation status report or empty string if not authorized [string]

function chat_install_status_report() {
	global $user;
	global $kapenta;

	if ('admin' != $user->role) { return false; }

	$report = '';											//%	return value [string:html]
	$installNotice = '<!-- table installed correctly -->';
	$installed = true;

	$dba = $kapenta->getDBAdminDriver();

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Hash objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Chat_Hash();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Inbox objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Chat_Inbox();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Membership objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Chat_Membership();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores MessageOut objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Chat_MessageOut();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Peer objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Chat_Peer();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Room objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Chat_Room();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Session objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Chat_Session();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------
	if (true == $installed) { $report .= '<!-- module installed correctly -->'; }
	return $report;
}

?>
