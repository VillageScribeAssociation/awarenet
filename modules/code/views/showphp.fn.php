<?

	require_once($kapenta->installPath . 'modules/code/models/bug.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//	show php code
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Code_File object [string]
//arg: docUID - overrides raUID if present [string]

function code_showphp($args) {
	global $kapenta;
	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('docUID', $args)) { $args['raUID'] = $args['docUID']; }
	if (false == array_key_exists('raUID', $args)) { return false; }

	$model = new Code_File($args['raUID']);
	if (false == $model->loaded) { return false; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$ext = $model->extArray();
	$html .= "<small><pre>" . $ext['safeContent'] . "</small></pre>";

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
