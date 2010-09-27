<?

//--------------------------------------------------------------------------------------------------
//	installer for folders module (creates table)
//--------------------------------------------------------------------------------------------------

require_once($kapenta->installPath . 'modules/folders/models/folder.mod.php');

function install_folders_module() {
	global $installPath;
	global $user;

	if ('admin' != $user->role) { return false; }
	$model = new Folder();
	$report = $model->install();
	return $report;
}

?>
