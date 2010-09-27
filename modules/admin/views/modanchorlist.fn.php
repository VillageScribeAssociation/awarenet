<?

	require_once($kapenta->installPath . 'core/kmodule.class.php');

//--------------------------------------------------------------------------------------------------
//|	anchor list of modules (perm:manage)
//--------------------------------------------------------------------------------------------------

function admin_modanchorlist($args) {
	global $kapenta;
	$modList = $kapenta->listModules();
	$tAry = array();
	foreach($modList as $module) { $tAry[] = "\n<a href='#modList".  $module . "'>$module</a>"; }
	$html = implode(', ', $tAry) . ".\n";
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
