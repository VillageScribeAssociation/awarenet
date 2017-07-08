<?

	require_once($kapenta->installPath . 'core/dbdriver/mysqladmin.dbd.php');
	require_once($kapenta->installPath . 'core/dbdriver/mysqliadmin.dbd.php');
	require_once($kapenta->installPath . 'modules/polls/models/answer.mod.php');
	require_once($kapenta->installPath . 'modules/polls/models/question.mod.php');
	require_once($kapenta->installPath . 'modules/polls/models/vote.mod.php');

//--------------------------------------------------------------------------------------------------
//*	install script for Polls module
//--------------------------------------------------------------------------------------------------
//+	reports are human-readable HTML, with script-readable HTML comments

//--------------------------------------------------------------------------------------------------
//|	install the Polls module
//--------------------------------------------------------------------------------------------------
//returns: html report or empty string if not authorized [string][bool]

function polls_install_module() {
	global $kapenta;
	global $kapenta;

	if ('admin' != $kapenta->user->role) { return ''; }

	$report = '';				//% return value [string:html]

	$dba = $kapenta->getDBAdminDriver();

	//----------------------------------------------------------------------------------------------
	//	create or upgrade polls_answer table
	//----------------------------------------------------------------------------------------------
	$model = new Polls_Answer();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	create or upgrade polls_question table
	//----------------------------------------------------------------------------------------------
	$model = new Polls_Question();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	create or upgrade polls_vote table
	//----------------------------------------------------------------------------------------------
	$model = new Polls_Vote();
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

function polls_install_status_report() {
	global $kapenta;
	global $kapenta;

	if ('admin' != $kapenta->user->role) { return false; }

	$report = '';				//%	return value [string:html]
	$installNotice = '<!-- table installed correctly -->';
	$installed = true;

	$dba = $kapenta->getDBAdminDriver();

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Answer objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Polls_Answer();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Question objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Polls_Question();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Vote objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Polls_Vote();
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
