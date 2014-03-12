<?php

//--------------------------------------------------------------------------------------------------
//|	makes a link to edit an images_image object with the sketchpad
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of an images_image object [string]
//opt: UID - overrides raUID if present [string]
//opt: imageUID - overrrides raUID if present [string]

function sketchpad_sketchlink($args) {
	global $kapenta;
	global $theme;

	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (('public' == $kapenta->user->role) || ('banned' == $kapenta->user->role)) { return ''; }

	if (true == array_key_exists('UID', $args)) { $args['raUID'] = $args['UID']; }
	if (true == array_key_exists('imageUID', $args)) { $args['raUID'] = $args['imageUID']; }
	if (false == array_key_exists('raUID', $args)) { return '(raUID not given)'; }

	$model = new Images_Image($args['raUID']);
	if (false == $model->loaded) { return '(image not found)'; }

	//----------------------------------------------------------------------------------------------
	//	make the link
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/sketchpad/views/sketchlink.block.php');
	$html = $theme->replaceLabels($model->extArray(), $block);

	return $html;
}

?>
