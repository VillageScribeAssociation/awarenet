<?

	require_once($kapenta->installPath . 'modules/code/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show contents of folder
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Code_File entry [string]
//opt: folderUID - alias or UID of a Code_File entry, overrides raUID if present [string]
//opt: UID - overrides raUID if present [string]

function code_showchildren($args) {
	global $db;
	global $kapenta;
	global $theme;

	$html = '';				//% return value [string:html]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('folderUID', $args)) { $args['raUID'] = $args['folderUID']; }
	if (true == array_key_exists('UID', $args)) { $args['raUID'] = $args['UID']; }
	if (false == array_key_exists('raUID', $args)) { return '(folder UID not given)'; }

	$model = new Code_File($args['raUID']);
	if (false == $model->loaded) { return '(folder not found)'; }

	//----------------------------------------------------------------------------------------------
	//	load children from database
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "parent='" . $db->addMarkup($model->UID) . "'";
	$range = $db->loadRange('code_file', '*', $conditions, "type='folder', title");

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/code/views/childsummary.block.php');
	if (0 == count($range)) { $html .= '(folder is empty)'; }

	foreach($range as $item) {
		$child = new Code_File();
		$child->loadArray($item);
		$ext = $child->extArray();
		$ext['fileUID'] = $ext['UID'];
		$html .= $theme->replaceLabels($ext, $block);
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
