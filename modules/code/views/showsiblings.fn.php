<?

	require_once($kapenta->installPath . 'modules/code/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show sibling files
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Code_File object [string]
//opt: itemUID - alais or UID of a Code_File object, overrides raUID if present [string]

function code_showsiblings($args) {
	global $kapenta;
	global $theme;
	global $db;

	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('folderUID', $args)) { $args['raUID'] = $args['folderUID']; }
	if (false == array_key_exists('raUID', $args)) { return '(file not specified)'; }

	$model = new Code_File($args['raUID']);
	if (false == $model->loaded) { return '(file not found)'; }
	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "parent='" . $db->addMarkup($model->parent) . "'"; 
	$range = $db->loadRange('code_file', '*', $conditions, "type='folder', title");

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/code/views/itemnav.block.php');

	//if (0 == count($range)) { $html .= "(no sibling files)" }
	foreach ($range as $item) {
		$model->loadArray($item);
		$html .= $theme->replaceLabels($model->extArray(), $block);
	}
	
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
