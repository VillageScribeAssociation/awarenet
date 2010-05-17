<?

	require_once($installPath . 'modules/mods/models/kmodule.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all modules (perm:view)
//--------------------------------------------------------------------------------------------------

function mods_list($args) {
	global $serverPath;
	$modList = listModules();
	$html = '';

	foreach($modList as $module) 
		{ $html .= "<a href='" . $serverPath . "mods/" . $module . "'>$module</a><br/>\n"; }

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
