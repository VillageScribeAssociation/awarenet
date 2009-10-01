<?

	require_once($installPath . 'modules/mods/models/kmodule.mod.php');

//--------------------------------------------------------------------------------------------------
//	make table of permissions available on a module (perm:manage)
//--------------------------------------------------------------------------------------------------
// * $args['modulename'] = name of a module

function mods_editpermissions($args) {
	if (array_key_exists('modulename', $args) == false) { return false; }
	$m = new KModule($args['modulename'] . '');
	$html = '';

	foreach($m->permissions as $permName => $perms) {

		$cLine = loadBlock('modules/mods/views/editperms.block.php');
		$cLine = str_replace('%%permName%%', $permName, $cLine);
		$cLine = str_replace('%%permVal%%', implode("\n", $perms), $cLine);
		$html .= $cLine;
	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>