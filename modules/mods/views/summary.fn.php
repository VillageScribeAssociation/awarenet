<?

	require_once($installPath . 'modules/mods/models/kmodule.mod.php');

//--------------------------------------------------------------------------------------------------
//	summary of a module, with buttons to install/enable/disable a given module (perm:manage)
//--------------------------------------------------------------------------------------------------
// * $args['modulename'] = name of a module

function mods_summary($args) {
	global $user;
	if ($user->data['ofGroup'] != 'admin') { return false; }
	if (array_key_exists('modulename', $args) == false) { return false; }
	$m = new KModule($args['modulename']);
	return replaceLabels($m->toArray(), loadBlock('modules/mods/views/summary.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>