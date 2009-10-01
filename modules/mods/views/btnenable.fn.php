<?

	require_once($installPath . 'modules/mods/models/kmodule.mod.php');

//--------------------------------------------------------------------------------------------------
//	enable button (perm:manage)
//--------------------------------------------------------------------------------------------------
// * $args['modulename'] = name of a module

function mods_btnenable($args) {
	if (array_key_exists('modulename', $args) == false) { return false; }
	$m = new KModule($args['modulename'] . '');
	if ($m->enabled == 'yes') { return '[enabled]'; }
	if ($m->installed == 'no') { return '[enable]'; }
	else { return replaceLabels($m->toArray(), loadBlock('modules/mods/views/btnenable.block.php')); }
}

//--------------------------------------------------------------------------------------------------

?>