<?

	require_once($kapenta->installPath . 'modules/code/models/bugs.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/code.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/codeproject.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/coderevision.mod.php');

//--------------------------------------------------------------------------------------------------
//	show a block template
//--------------------------------------------------------------------------------------------------
// * $args['docUID'] = recordAlias or UID or code entry, overrides raUID
// * $args['raUID'] = recordAlias or UID or code entry

function code_showblock($args) {
	if (array_key_exists('docUID', $args)) { $args['raUID'] = $args['docUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	global $kapenta;
	$html = '';

	$c = new Code($args['raUID']);
	$ext = $c->extArray();

	require_once($kapenta->installPath . 'modules/code/inc/format.inc.php');
	$htmlContent = formatDisplayHtml($ext['safeContent']);	

	$html .= "<div class='inlinequote'><small>" . $htmlContent . "</small></div><br/>\n";
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>