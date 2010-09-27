<?

	require_once($kapenta->installPath . 'core/kmodule.class.php');

//--------------------------------------------------------------------------------------------------
//|	select box of all permissions on all modules  TODO: upgrade this to new permissions system
//--------------------------------------------------------------------------------------------------
//role: admin - only administrators may use this

function mods_selectallpermissions($args) {
	global $user;
	$html = '';		//% return value [string]	

	//----------------------------------------------------------------------------------------------
	//	check user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$modList = listModules();
	$html = "<select name='permission'>\n";

	foreach ($modList as $module) {
		$module = new KModule($module);
		foreach($module->permissions as $permName) {
			$optVal = $module->modulename . '::' . $permName;
			$html .= "\t<option value='" . $optVal . "'>$optVal</option>\n";
		}
	}

	$html .= "</select>\n";
	return $html;
}


?>
