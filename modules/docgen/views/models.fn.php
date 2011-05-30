<?

	require_once($kapenta->installPath . 'modules/docgen/inc/readcomments.inc.php');

//-------------------------------------------------------------------------------------------------
//|	list and describe all models on a module
//-------------------------------------------------------------------------------------------------
//arg: module - module to display views for [string]

function docgen_models($args) {
	if (array_key_exists('module', $args) == false) { return ''; }
	$modName = str_replace("/", '', $args['module']);	
	$html = '';

	//---------------------------------------------------------------------------------------------
	//	find all models
	//---------------------------------------------------------------------------------------------
	$models = listFiles($kapenta->installPath . 'modules/' . $modName . "/models/", '.mod.php');

	foreach($models as $model) {
		$modelName = str_replace('.mod.php', '', $model);

		$fileName = $kapenta->installPath . 'modules/' . $modName . '/models/' . $model;
		$dc = docRead($fileName);

		//-----------------------------------------------------------------------------------------
		//	show model summary and notes
		//-----------------------------------------------------------------------------------------

		if (count($dc['functions']) > 0) {
			$html .= "<h2>$model - " . implode($dc['summary']) . "</h2>\n";
			$modelDesc = txtToHtml(implode("\n", $dc['desc']));
			$html .= "<div class='inlinequote'>$modelDesc</div></small><br/>";
		}

		//-----------------------------------------------------------------------------------------
		//	show model summary and notes
		//-----------------------------------------------------------------------------------------

		foreach($dc['functions'] as $fn) {

			$method = '$model->' . $fn['name'] . '(...)';
			if (count($fn['arg']) == 0) { $method = str_replace('(...)', '()', $method); }

			$summary = implode(" ", $fn['summary']) . "\n";
			$summary = txtToHtml($summary);
			if (count($fn['summary']) == 0) 
				{ $summary = "<span class='ajaxerror'>none.</span><br/>\n"; }			

			$html .= "<b>$method - $summary</b><br/>\n";

			$html .= docMakeArgTable($fn) . "<br/>\n";

			if (count($fn['desc']) > 0) { 
				$notes = implode("\n", $fn['desc']);
				$html .= "notes: " . txtToHtml($notes) . "<br/>\n"; 
			}

			$html .= "<hr/>\n";

		}

		if (count($dc['functions']) > 0) { $html .= "<br/>"; }

	}

	//----------------------------------------------------------------------------------------------
	//	if no models on this module
	//----------------------------------------------------------------------------------------------
	if (count($models) == 0) { $html .= "<p>(this module has no models)</p>"; }

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------
	return $html;
}

?>
