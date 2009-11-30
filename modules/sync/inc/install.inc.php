<?

//--------------------------------------------------------------------------------------------------
//	installer for gallery module (creates table)
//--------------------------------------------------------------------------------------------------

require_once($installPath . 'modules/sync/models/servers.mod.php');
require_once($installPath . 'modules/sync/models/deleted.mod.php');
require_once($installPath . 'modules/sync/models/sync.mod.php');
require_once($installPath . 'modules/sync/models/downloads.mod.php');

function install_sync_module() {
	global $installPath;
	global $user;

	if ($user->data['ofGroup'] != 'admin') { return false; }

	//----------------------------------------------------------------------------------------------
	//	servers table
	//----------------------------------------------------------------------------------------------
	$model = new Server();
	$report = $model->install();

	//----------------------------------------------------------------------------------------------
	//	deleted items table
	//----------------------------------------------------------------------------------------------
	$model = new DeletedItem();
	$report = $model->install();

	//----------------------------------------------------------------------------------------------
	//	sync events table
	//----------------------------------------------------------------------------------------------
	$model = new Sync();
	$report .= $model->install();

	//----------------------------------------------------------------------------------------------
	//	downloads table
	//----------------------------------------------------------------------------------------------
	$model = new Download();
	$report .= $model->install();

	return $report;
}

?>
