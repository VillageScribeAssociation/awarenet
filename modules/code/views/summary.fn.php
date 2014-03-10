<?

	require_once($kapenta->installPath . 'modules/code/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show summary of a Code_File object
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Code_File object [string]
//opt: UID - overrides raUID if present [string]
//opt: fileUID - overrides raUID if present [string]

function code_summary($args) {
	global $kapenta;
	global $theme;

	$html = '';					//%	return value [string:html]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('UID', $args)) { $args['raUID'] = $args['UID']; }
	if (true == array_key_exists('fileUID', $args)) { $args['raUID'] = $args['fileUID']; }
	if (false == array_key_exists('raUID', $args)) { return '(file not specified)'; }

	$model = new Code_File($args['raUID']);	
	if (false == $model->loaded) { return '(file not found)'; }	
	//TODO: check permissions here
	
	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/code/views/summary.block.php');
	$html = $theme->replaceLabels($model->extArray(), $block);

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
