<?

	require_once($kapenta->installPath . 'modules/packages/inc/kupdatemanager.class.php');

//--------------------------------------------------------------------------------------------------
//|	list all sources configured on this instance
//--------------------------------------------------------------------------------------------------

function packages_listsources($args) {
	global $kapenta;
	global $theme;
	
	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check user role and load sources from registry
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { return ''; }

	$updateManager = new KUpdateManager();
	$sources = $updateManager->listSources();

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	if (0 == count($sources)) { 
		$html .= "<div class='inlinequote'>No sources configured.</div>";
		return $html;
	}
		
	$table = array();						//%	[array:array:string]
	$table[] = array('URL', '[x]');
	foreach($sources as $source) {
		$delUrl = '%%serverPath%%packages/removesource/source_' . base64_encode($source);
		$delLink = "<a href='" . $delUrl . "'>[del]</a>";
		$table[] = array($source, $delLink);
	}

	$html = $theme->arrayToHtmlTable($table, true, true);

	return $html;
}

?>
