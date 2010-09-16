<?

	require_once($kapenta->installPath . 'core/kmodule.class.php');

//--------------------------------------------------------------------------------------------------
//|	make table of permissions available on a module (perm:manage)
//--------------------------------------------------------------------------------------------------
//arg: modulename - name of a module [string]

function mods_editpermissions($args) {
	global $theme;

	if (array_key_exists('modulename', $args) == false) { return false; }
	$m = new KModule($args['modulename'] . '');
	$html = '';

	foreach($m->permissions as $permName => $perms) {

		$cLine = $theme->loadBlock('modules/mods/views/editperms.block.php');
		$cLine = str_replace('%%permName%%', $permName, $cLine);
		$cLine = str_replace('%%permVal%%', implode("\n", $perms), $cLine);
		$html .= $cLine;
	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
