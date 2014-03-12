<?

	//require_once($kapenta->installPath . 'modules/pages/models/page.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all pages (and blocks) on all modules
//--------------------------------------------------------------------------------------------------

function admin_listallpages($args) {
		global $kapenta;
		global $theme;
		global $kapenta;

	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check user is admin and load modules
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { return ''; }
	$modList = $kapenta->listModules();

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/admin/views/pagesummary.block.php');

	foreach($modList as $module) {
		//------------------------------------------------------------------------------------------
		//	list pages and blocks on this module
		//------------------------------------------------------------------------------------------
		$labels = array('moduleName' => $module);
		$html .= $theme->replaceLabels($labels, $block);
	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
