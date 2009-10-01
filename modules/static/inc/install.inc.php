<?

//--------------------------------------------------------------------------------------------------------------
//	installation script for pages module
//--------------------------------------------------------------------------------------------------------------

require_once($installPath . 'modules/static/models/static.mod.php');

function install_static_module() {
	global $user;
	if ($user->data['ofGroup'] != 'admin') { return false; }

	require_once($installPath . 'modules/mods/models/kmodule.mod.php');
	$m = new KModule('static');	
	$m->installed = 'yes';
	$m->save();

	$model = new StaticPage();
	$report = $model->install();

	return $report;
}

?>
