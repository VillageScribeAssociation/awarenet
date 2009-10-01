<?

	require_once($installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//	full-page display of an image + caption, etc
//--------------------------------------------------------------------------------------------------
// * $args['raUID'] = recordAlias or UID of record

function images_showfull($args) {
	if (array_key_exists('raUID', $args) == false) { return false; }
	$i = new Image($args['raUID']);
	if ($i->data['fileName'] == '') { return false; }
	
	$labels = $i->extArray();
	$html = replaceLabels($labels, loadBlock('modules/images/views/showfull.block.php'));
	
	
	
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>