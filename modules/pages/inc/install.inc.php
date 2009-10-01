<?

//--------------------------------------------------------------------------------------------------------------
//	installation script for pages module
//--------------------------------------------------------------------------------------------------------------

function install_pages_module() {
	global $user;
	if ($user->data['ofGroup'] != 'admin') { return false; }

	require_once($installPath . 'modules/mods/models/kmodule.mod.php');
	$m = new KModule('pages');	
	$m->installed = 'yes';
	$m->save();

	$report = "Pages module requires no installation.<br/>"

	return $report;
}

?>
