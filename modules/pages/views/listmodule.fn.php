<?

	require_once($installPath . 'modules/pages/models/page.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all pages on a module
//--------------------------------------------------------------------------------------------------

function pages_listmodule($args) {
	global $serverPath;
	$html = '';

	if (array_key_exists('module', $args)) {
		$pageList = listPages($args['module']);
		foreach($pageList as $page) {
			$editUrl = $serverPath . 'pages/edit/module_' . $args['module'] . '/' . $page;
			$html .= "\t\t<a href='" . $editUrl . "'>$page</a><br/>\n";
		}		
	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

