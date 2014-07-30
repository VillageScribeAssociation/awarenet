<?

	require_once($kapenta->installPath . 'core/dbdriver/mysqladmin.dbd.php');
	require_once($kapenta->installPath . 'modules/chatserver/models/hash.mod.php');
	require_once($kapenta->installPath . 'modules/chatserver/models/history.mod.php');
	require_once($kapenta->installPath . 'modules/chatserver/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/chatserver/models/outbox.mod.php');
	require_once($kapenta->installPath . 'modules/chatserver/models/peer.mod.php');
	require_once($kapenta->installPath . 'modules/chatserver/models/room.mod.php');
	require_once($kapenta->installPath . 'modules/chatserver/models/session.mod.php');

//--------------------------------------------------------------------------------------------------
//*	install script for Chatserver module --
//--------------------------------------------------------------------------------------------------
//+	reports are human-readable HTML, with script-readable HTML comments

//--------------------------------------------------------------------------------------------------
//|	install the Chatserver module
//--------------------------------------------------------------------------------------------------
//returns: html report or empty string if not authorized [string][bool]

function chatserver_install_module() {
	global $user;
	global $kapenta;

	if ('admin' != $user->role) { return ''; }

	$report = '';				//% return value [string:html]

	$dba = $kapenta->getDBAdminDriver();

	//----------------------------------------------------------------------------------------------
	//	create or upgrade chatserver_hash table
	//----------------------------------------------------------------------------------------------
	$model = new Chatserver_Hash();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	create or upgrade chatserver_history table
	//----------------------------------------------------------------------------------------------
	$model = new Chatserver_History();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	create or upgrade chatserver_membership table
	//----------------------------------------------------------------------------------------------
	$model = new Chatserver_Membership();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	create or upgrade chatserver_outbox table
	//----------------------------------------------------------------------------------------------
	$model = new Chatserver_Outbox();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	create or upgrade chatserver_peer table
	//----------------------------------------------------------------------------------------------
	$model = new Chatserver_Peer();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	create or upgrade chatserver_room table
	//----------------------------------------------------------------------------------------------
	$model = new Chatserver_Room();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	create or upgrade chatserver_session table
	//----------------------------------------------------------------------------------------------
	$model = new Chatserver_Session();
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

function chatserver_install_status_report() {
	global $user;
	global $kapenta;

	if ('admin' != $user->role) { return false; }

	$report = '';												//%	return value [string:html]
	$installNotice = '<!-- table installed correctly -->';
	$installed = true;

	$dba = $kapenta->getDBAdminDriver();

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Hash objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Chatserver_Hash();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores History objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Chatserver_History();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Membership objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Chatserver_Membership();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Outbox objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Chatserver_Outbox();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Peer objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Chatserver_Peer();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Room objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Chatserver_Room();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Session objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Chatserver_Session();
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
