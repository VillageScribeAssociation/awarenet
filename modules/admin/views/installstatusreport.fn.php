<?

	require_once($kapenta->installPath . 'core/kmodule.class.php');

//--------------------------------------------------------------------------------------------------
//	display the installation status report for a givne module
//--------------------------------------------------------------------------------------------------
//arg: modulename - name of a module [string]

function admin_installstatusreport($args) {
	global $user;
	$html = '';

	if ('admin' != $user->role) { return ''; }

	if (false == array_key_exists('modulename', $args)) { return false; }

	$model = new KModule($args['modulename']);
	if (false == $model->loaded) { return '(no such module)'; }

	$html = $model->getInstallStatusReport();
	return $html;
}

?>
