<?

require_once($kapenta->installPath . 'modules/docgen/inc/readcomments.inc.php');

//-------------------------------------------------------------------------------------------------
//|	list and all modules for the nav
//-------------------------------------------------------------------------------------------------

function docgen_listmodulesnav($args) {
	global $kapenta, $user;
	$html = '';		//%	return value [string]

	$mods = $kapenta->listModules();
	foreach($mods as $mod) 
		{ $html .= "[[:docgen::modulesummarynav::module=" . $mod . ":]]\n"; }

	return $html;
}

?>
