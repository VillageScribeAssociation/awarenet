<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//|	full-page display of an image + caption, etc
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of image record [string]

function images_showfull($args) {
	global $db;

	global $theme;

	if (array_key_exists('raUID', $args) == false) { return ''; }
	if ($db->objectExists('images', $args['raUID']) == false) { return ''; }
	$i = new Images_Image($args['raUID']);
	if ($i->fileName == '') { return false; }
	
	$labels = $i->extArray();
	$html = $theme->replaceLabels($labels, $theme->loadBlock('modules/images/views/showfull.block.php'));
	
	
	
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>