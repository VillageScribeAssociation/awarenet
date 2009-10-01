<?

	require_once($installPath . 'modules/mods/models/kmodule.mod.php');

//--------------------------------------------------------------------------------------------------
//	enable button (perm:manage)
//--------------------------------------------------------------------------------------------------
// * $args['modulename'] = name of a module

function mods_btndisable($args) {
	if (array_key_exists('modulename', $args) == false) { return false; }
	$m = new KModule($args['modulename'] . '');
	if ($m->enabled == 'no') { return '[disabled]'; } 
	else { return replaceLabels($m->toArray(), loadBlock('modules/mods/views/btndisable.block.php')); }
}

//--------------------------------------------------------------------------------------------------

?>