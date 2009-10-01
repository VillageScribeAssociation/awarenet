<?

	require_once($installPath . 'modules/files/models/files.mod.php');

//--------------------------------------------------------------------------------------------------
//	full-page display of an file + caption, etc
//--------------------------------------------------------------------------------------------------
// * $args['raUID'] = recordAlias or UID of record

function files_showfull($args) {
	if (array_key_exists('raUID', $args) == false) { return false; }
	$i = new file($args['raUID']);
	if ($i->data['fileName'] == '') { return false; }
	
	$labels = $i->extArray();
	$html = replaceLabels($labels, loadBlock('modules/files/showfull.block.php'));
	
	
	
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>