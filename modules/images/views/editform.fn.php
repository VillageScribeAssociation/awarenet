<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form for editing images
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of image record [string]
//opt: return - return to upload dialog (set to 'uploadmultiple') [string]

function images_editform($args) {
	global $theme;

	$return = '';
	if (array_key_exists('raUID', $args) == false) { return false; }
	if (array_key_exists('return', $args)) { $return = $args['return']; }
	
	$i = new Images_Image($args['raUID']);
	if ($i->fileName == '') { return '(image not found)'; }
	
	$labels = $i->extArray();
	$labels['return'] = $return;
	$labels['returnLink'] = '';
	
	if ($return == 'uploadmultiple') { 
		$labels['returnUrl'] = '/images/uploadmultiple/refModule_' . $labels['refModule'] 
				     . '/refUID_' . $labels['refUID'] . '/';
				     
		$labels['returnLink'] = "<a href='" . $labels['returnUrl'] 
				      . "'>[&lt;&lt; return to upload form ]</a>";
	}
	
	return $theme->replaceLabels($labels, $theme->loadBlock('modules/images/views/editform.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>