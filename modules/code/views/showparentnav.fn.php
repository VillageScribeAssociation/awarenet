<?

	require_once($kapenta->installPath . 'modules/code/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show summary of parent folder in the nav
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of Code_File object [string]
//opt: itemUID = alias or UID of Code_File object, overrides raUID if present [string]
//opt: folderUID = alias or UID of Code_File object, overrides raUID if present [string]
//opt: UID = alias or UID of Code_File object, overrides raUID if present [string]

function code_showparentnav($args) {
	global $kapenta;
	global $theme;
	$html = '';					//%	return value [string:html]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('UID', $args)) { $args['raUID'] = $args['UID']; }
	if (true == array_key_exists('folderUID', $args)) { $args['raUID'] = $args['folderUID']; }
	if (true == array_key_exists('itemUID', $args)) { $args['raUID'] = $args['itemUID']; }
	if (false == array_key_exists('raUID', $args)) { return false; }

	$model = new Code_File($args['raUID']);
	if (false == $model->loaded) { return '(folder not found)'; }
	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	if (('none' == $model->parent) OR ('' == $model->parent)) {
		$html .= "This is the root folder for this project.<br/>\n";
	} else {
		$block = $theme->loadBlock('modules/code/views/parentnav.block.php');
		$model->load($model->parent);
		$ext = $model->extArray();
		$html = $theme->replaceLabels($ext, $block);
	}

	return $html;
}


//--------------------------------------------------------------------------------------------------

?>
