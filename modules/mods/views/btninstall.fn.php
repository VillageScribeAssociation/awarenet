<?

	require_once($installPath . 'modules/mods/models/kmodule.mod.php');

//--------------------------------------------------------------------------------------------------
//|	install button (perm:manage)
//--------------------------------------------------------------------------------------------------
//arg: modulename - name of a module [string]

function mods_btninstall($args) {
	if (array_key_exists('modulename', $args) == false) { return false; }
	$m = new KModule($args['modulename'] . '');
	if ($m->installed == 'yes') { return '[installed]'; } 
	else { return replaceLabels($m->toArray(), loadBlock('modules/mods/views/btninstall.block.php')); }
}

//--------------------------------------------------------------------------------------------------

?>

