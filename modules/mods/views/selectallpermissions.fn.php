<?

	require_once($installPath . 'modules/mods/models/kmodule.mod.php');

//--------------------------------------------------------------------------------------------------
//	select box of all permissions on all modules
//--------------------------------------------------------------------------------------------------

function mods_selectallpermissions($args) {
	global $serverPath;
	$modList = listModules();
	$html = "<select name='permission'>\n";

	foreach ($modList as $module) {
		$m = new KModule($module);
		foreach($m->permissions as $permName) {
			$optVal = $m->modulename . '::' . $permName;
			$html .= "\t<option value='" . $optVal . "'>$optVal</option>\n";
		}
	}

	$html .= "</select>\n";
	return $html;
}


?>