<?php

//--------------------------------------------------------------------------------------------------
//	show controls for editing attachments if permitted to do so
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a kapenta module [string]
//arg: refModel - type of object which may ahev attachments [string]
//arg: refUID - UID of object which may have attachments [string]

function live_manageattachments($args) {
	global $kapenta;	
	global $theme;
	global $kapenta;
	global $kapenta;

	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $args)) { return '(no refModule)'; }
	if (false == array_key_exists('refModel', $args)) { return '(no refModel)'; }
	if (false == array_key_exists('refUID', $args)) { return '(no refUID)'; }

	$refModule = $args['refModule'];
	$refModel = $args['refModel'];
	$refUID = $args['refUID'];

	if (false == $kapenta->user->authHas($refModule, $refModel, 'edit', $refUID)) { return ''; }
	
	if (false == $kapenta->moduleExists($refModule)) { return '(no such module)'; }
	if (false == $kapenta->db->objectExists($refModel, $refUID)) { return '(no such owner object)'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/live/views/manageattachments.block.php');
	$html = $theme->replaceLabels($args, $block);

	return $html;
}
