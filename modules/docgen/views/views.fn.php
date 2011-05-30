<?

require_once($kapenta->installPath . 'modules/docgen/inc/readcomments.inc.php');

//-------------------------------------------------------------------------------------------------
//|	list and describe all views on a module
//-------------------------------------------------------------------------------------------------
//arg: module - module to display views for [string]

function docgen_views($args) {
	if (array_key_exists('module', $args) == false) { return ''; }
	$modName = str_replace("/", '', $args['module']);	
	$html = '';

	//---------------------------------------------------------------------------------------------
	//	find all views and blocks
	//---------------------------------------------------------------------------------------------
	$views = listFiles($kapenta->installPath . 'modules/' . $modName . "/views/", '.fn.php');
	$blocks = listFiles($kapenta->installPath . 'modules/' . $modName . "/views/", '.block.php');

	foreach($views as $view) {
		$viewName = str_replace('.fn.php', '', $view);
		$html .= "<h3>$view</h3>\n";
		
		$fileName = $kapenta->installPath . 'modules/' . $modName . '/views/' . $view;
		$dc = docRead($fileName);

		foreach($dc['functions'] as $fn) {

			if (count($fn['summary']) > 0) { 
				$html .= "Summary: " . implode(" ", $fn['summary']) . "<br/>\n"; 
			} else {
				$html .= "summary: <span class='ajaxerror'>none.</span><br/>\n"; 
			}			

			if (count($fn['desc']) > 0) 
				{ $html .= "notes: " . implode("<br/>\n", $fn['desc']) . "<br/>\n"; }

			$html .= "Function name: " . $fn['name'] 
				  . " - parameters are passed in \$arg array<br/>\n";

			$blockExample = docMakeBlockExample($modName, $viewName, $fn);
			$blockExample = str_replace("[[", "[*[", $blockExample);
			$html .= "Block Example: " . $blockExample . "<br/><br/>\n";

			$html .= docMakeArgTable($fn) . "<br/>\n<hr/>\n";

		}

	}

	return $html;
}

?>
