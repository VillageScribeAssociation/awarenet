<?

	require_once($installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//	form for editing images
//--------------------------------------------------------------------------------------------------
// * $args['raUID'] = recordAlias or UID of record
// * $args['return'] = return to upload dialog

function images_editform($args) {
	$return = '';
	if (array_key_exists('raUID', $args) == false) { return false; }
	if (array_key_exists('return', $args)) { $return = $args['return']; }
	
	$i = new Image($args['raUID']);
	if ($i->data['fileName'] == '') { return '(image not found)'; }
	
	$labels = $i->extArray();
	$labels['return'] = $return;
	$labels['returnLink'] = '';
	
	if ($return == 'uploadmultiple') { 
		$labels['returnUrl'] = '/images/uploadmultiple/refModule_' . $labels['refModule'] 
				     . '/refUID_' . $labels['refUID'] . '/';
				     
		$labels['returnLink'] = "<a href='" . $labels['returnUrl'] 
				      . "'>[&lt;&lt; return to upload form ]</a>";
	}
	
	return replaceLabels($labels, loadBlock('modules/images/views/editform.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>