<?php

//--------------------------------------------------------------------------------------------------
//|	make an ajax attachments widget
//--------------------------------------------------------------------------------------------------
//;	Note that set of allowed file types is set in the registry under the live.file prefix, relating
//;	the file extension to the module which handles files of this type.
//;
//;		live.file.jpg := images
//;		live.file.flv := videos
//;		live.file.doc := files
//;
//arg: refModule - module of object which will own attachments [string]
//arg: refModel - type of object which will own attachments [string]
//arg: refUID - UID of object which will own attachments [string]
//arg: allow - comma separated list of handler modules [string]

function live_attach($args) {
	global $user;
	global $theme;
	global $kapenta;
	
	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $args)) { return '(refModule not given)'; }
	if (false == array_key_exists('refModel', $args)) { return '(refModel not given)'; }
	if (false == array_key_exists('refUID', $args)) { return '(refUID not given)'; }

	//	TODO: permissions check here?

	$args['allow'] = 'any';

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/live/views/attach.block.php');
	$html = $theme->replaceLabels($args, $block);

	return $html;
}

?>
