<?

	require_once($kapenta->installPath . 'modules/code/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show tree of children starting at a given node
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of Code_File object [string]
//opt: itemUID - alias or UID of Code_File object, overrides raUID [string]
//opt: indent - indent level [int]
//TODO: this is inefficient, fix it up..

function code_showtree($args) {
	global $kapenta;
	global $db;
	global $user;
	global $theme;

	$html = '';				//%	return value [string]
	$indent = 0;			//%	indent level of tree [int]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('itemUID', $args)) { $args['raUID'] = $args['itemUID']; }
	if (false == array_key_exists('raUID', $args)) { return '(UID not given)'; }
	if (true == array_key_exists('indent', $args)) { $indent = (int)$args['indent']; }

	$model = new Code_File($args['raUID']);
	if (false == $model->loaded) { return '(unkown file)'; }
	//TODO: permissions check here 

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "type='folder'";
	$conditions[] = "parent='" . $db->addMarkup($model->UID) . "'";

	$range = $db->loadRange('code_file', '*', $conditions, 'title ASC');

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$ext = $model->extArray();
	$ext['fileUID'] = $ext['UID'];
	$block = $theme->loadBlock('modules/code/views/childsummary.block.php');

	$html .= "<table noborder>\n\t<tr><td width='" . ($indent * 50) . "'></td><td>";
	$html .= $theme->replaceLabels($ext, $block);
	$html .= "</td></tr></table>";

	foreach($range as $item) {
		$cb = "[[:code::showtree::itemUID=" . $item['UID'] . "::indent=" . ($indent + 1) . ":]]";
		$html .= $theme->expandBlocks($cb, '');
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
