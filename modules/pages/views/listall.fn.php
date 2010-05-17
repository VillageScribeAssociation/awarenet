<?

	require_once($installPath . 'modules/pages/models/page.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all pages (and blocks) on all modules
//--------------------------------------------------------------------------------------------------------------

function pages_listall($args) {
	$modList = listModules();
	$block = loadBlock('modules/pages/views/summary.block.php');
	$html = '';

	foreach($modList as $module) {
		//----------------------------------------------------------------------------------------------
		//	list pages and blocks on this module
		//----------------------------------------------------------------------------------------------
		$labels = array('moduleName' => $module);
		$html .= replaceLabels($labels, $block);
	}
	return $html;
}

//--------------------------------------------------------------------------------------------------------------

?>

