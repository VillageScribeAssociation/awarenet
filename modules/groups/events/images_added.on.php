<?php

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');

//-------------------------------------------------------------------------------------------------
//|	fired when an image is added 
//-------------------------------------------------------------------------------------------------
//arg: refModule - name of module to which a image was attached [string]
//arg: refModel - type of object to which image was attached [string]
//arg: refUID - UID of object to which image was attached [string]
//arg: imageUID - UID of the new image [string]
//arg: imageTitle - title of new image [string]

function groups__cb_images_added($args) {
	global $kapenta;
	global $kapenta;
	global $kapenta;

	//----------------------------------------------------------------------------------------------
	//	check event arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $args)) { return false; }
	if (false == array_key_exists('refUID', $args)) { return false; }
	if (false == array_key_exists('imageUID', $args)) { return false; }
	if (false == array_key_exists('imageTitle', $args)) { return false; }

	if ('groups' != $args['refModule']) { return false; }

	$model = new Groups_Group($args['refUID']);
	if (false == $model->loaded) { return false; }	

	//----------------------------------------------------------------------------------------------
	//	automatically tag the image with the name of the post
	//----------------------------------------------------------------------------------------------

	$detail = array(
		'refModule' => 'images',
		'refModel' => 'images_image',
		'refUID' => $args['imageUID'],
		'tagName' => $model->name
	);

	$kapenta->raiseEvent('tags', 'tags_add', $detail);

	return true;
}

//-------------------------------------------------------------------------------------------------
?>
