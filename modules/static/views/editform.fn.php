<?

	require_once($installPath . 'modules/static/models/static.mod.php');

//--------------------------------------------------------------------------------------------------
//|	editform
//--------------------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of static page to edit [string]

function static_editform($args) {
	if (authHas('static', 'edit', $args) == false) { return false; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new StaticPage($args['raUID']);
	if ($model->data['UID'] == '') { return false; }
	
	$extArray = $model->extArray();
	$labels = array();
	
	foreach($extArray as $key => $val) {
		$val = str_replace('[', '&#91;', $val);			
		$val = str_replace(']', '&#93;', $val);			
		$val = str_replace('<', '&lt;', $val);			
		$val = str_replace('>', '&gt;', $val);
		$labels['p_' . $key] = $val;
	}
	
	return replaceLabels($labels, loadBlock('modules/static/views/editform.block.php'));
}


?>
