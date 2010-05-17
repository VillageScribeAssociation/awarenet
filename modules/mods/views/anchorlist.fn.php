<?

	require_once($installPath . 'modules/mods/models/kmodule.mod.php');

//--------------------------------------------------------------------------------------------------
//|	anchor list of modules (perm:manage)
//--------------------------------------------------------------------------------------------------

function mods_anchorlist($args) {
	$modList = listModules();
	$tAry = array();
	foreach($modList as $module) { $tAry[] = "\n<a href='#modList".  $module . "'>$module</a>"; }
	$html = implode(', ', $tAry) . ".\n";
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
