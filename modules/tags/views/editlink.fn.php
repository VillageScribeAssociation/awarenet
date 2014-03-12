<?php

//--------------------------------------------------------------------------------------------------
//|	make an 'edit tags' link to launch tag editor window for some object
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a kapenta module [string]
//arg: refModel - type of object which may have tags [string]
//arg: refUID - UID of object which may have tags [string]

function tags_editlink($args) {
	global $kapenta;
	global $theme;

	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $args)) { return '(no refModule)'; }
	if (false == array_key_exists('refModel', $args)) { return '(no refModel)'; }
	if (false == array_key_exists('refUID', $args)) { return '(no refUID)'; }

	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$editUrl = '%%serverPath%%tags/edittags'
	 . '/refModule_' . $args['refModule']
	 . '/refModel_' . $args['refModel']
	 . '/refUID_' . $args['refUID']
	 . '/';

	$editJs = "kwindowmanager.createWindow('Edit Tags', '" . $editUrl . "', 570, 400, false);";

	$html = "<a href=\"" . $editJs . "\">[edit tags]</a>"

	return $html;
}
