<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//|	full-page display of an image + caption, etc
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of image record [string]

function images_showfull($args) {
	global $db, $theme;

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('raUID', $args)) { return '(no image specified)'; }

	$model = new Images_Image($args['raUID']);
	if (false == $model->loaded) { return '(image not found)'; }
	if ('' == $model->fileName) { return '(invalid or corrupt image)'; }
	
	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$labels = $model->extArray();
	$block = $theme->loadBlock('modules/images/views/showfull.block.php');
	$html = $theme->replaceLabels($labels, $block);
	
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
