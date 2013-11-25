<?

	require_once($kapenta->installPath . 'modules/code/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show a text document
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Code_File object [string]
//opt: docUID - alias or UID of a Code_File object, overrides raUID if present [string]
//opt: UID - alias or UID of a Code_File object, overrides raUID if present [string]

function code_showtext($args) {
	global $kapenta;
	global $theme;
	$html = '';						//%	return value [string:html]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('docUID', $args)) { $args['raUID'] = $args['docUID']; }
	if (true == array_key_exists('UID', $args)) { $args['raUID'] = $args['UID']; }
	if (false == array_key_exists('raUID', $args)) { return '(file not specified)'; }

	$model = new Code_File($args['raUID']);
	if (false == $model->loaded) { return '(file not found)'; }
	//TODO: permission check here

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$ext = $model->extArray();
	$html .= "<small><pre>" . $ext['safeContent'] . "</small></pre>";

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
