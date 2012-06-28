<?

	require_once($kapenta->installPath . 'modules/files/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//|	make file info/download box
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID [string]
//opt: fileUID - overrides raUID [string]

function files_dnbox($args) {
	global $theme;

	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (array_key_exists('fileUID', $args)) { $args['raUID'] = $args['fileUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }

	$model = new Files_File($args['raUID']);
	if (false == $model->loaded) { return '(file not found)'; }
	
	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/files/views/dnbox.block.php');
	
	$labels = $model->extArray();
	if ('' != $labels['caption']) { $labels['caption'] .= "<br/>\n"; }

	$html =  $theme->replaceLabels($labels, $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
