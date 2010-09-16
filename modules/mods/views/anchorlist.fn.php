<?

	require_once($kapenta->installPath . 'core/kmodule.class.php');

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
