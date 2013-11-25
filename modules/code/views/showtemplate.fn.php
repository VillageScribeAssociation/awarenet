<?

	require_once($kapenta->installPath . 'modules/code/models/bugs.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/code.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/codeproject.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/coderevision.mod.php');

//--------------------------------------------------------------------------------------------------
//	show a page template
//--------------------------------------------------------------------------------------------------
// * $args['docUID'] = recordAlias or UID of code entry, overrides raUID
// * $args['raUID'] = recordAlias or UID of code entry

function code_showtemplate($args) {
	if (array_key_exists('docUID', $args)) { $args['raUID'] = $args['docUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	global $kapenta;
	$html = '';

	$model = new Code($args['raUID']);
	$ext = $model->extArray();

	$cleanTemplate = $ext['safeContent'];
	$cleanTemplate = str_replace('%%%', '%&#38;%', $cleanTemplate);
	$cleanTemplate = str_replace('%%', '%&#38;', $cleanTemplate);
	$cleanTemplate = str_replace("\n", "<br/>\n", $cleanTemplate);

	return "<small>\n" . $cleanTemplate . "</small>\n";
}

//--------------------------------------------------------------------------------------------------

?>