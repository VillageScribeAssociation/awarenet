<?

//--------------------------------------------------------------------------------------------------
//	installer for moblog module (creates table)
//--------------------------------------------------------------------------------------------------

require_once($installPath . 'modules/moblog/models/moblog.mod.php');
require_once($installPath . 'modules/moblog/models/precache.mod.php');

function install_moblog_module() {
	global $user;
	if ($user->data['ofGroup'] != 'admin') { return false; }

	$report = '';

	//----------------------------------------------------------------------------------------------
	//	create database tables
	//----------------------------------------------------------------------------------------------
	$model = new Moblog();
	$report .= $model->install();

	$model = new MoblogPreCache();
	$model->install();

	return $report;
}
?>
