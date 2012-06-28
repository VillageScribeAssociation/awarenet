<?php

//--------------------------------------------------------------------------------------------------
//|	list attachments formatted for injection into kapenta HyperTextArea
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a kapenta module [string]
//arg: refModel - type of object HTA is bound to [string]
//arg: refUID - UID of object HTA is bound to [string]
//arg: hta - name of a HyperTextArea on the client page [string]

function live_listattachmentshta($args) {
	global $user;
	global $kapenta;
	global $db;
	global $theme;	

	$html = '';				//%	return value

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $args)) { return '(no refModule given)'; }
	if (false == array_key_exists('refModel', $args)) { return '(no refModel given)'; }
	if (false == array_key_exists('refUID', $args)) { return '(no refUID given)'; }
	if (false == array_key_exists('hta', $args)) { return '(no hta given)'; }
	if (false == array_key_exists('display', $args)) { $args['display'] = 'videos,images,files'; }

	$refModule = $args['refModule'];
	$refModel = $args['refModel'];
	$refUID = $args['refUID'];
	$hta = $args['hta'];
	$display = $args['display'];

	if (false == $kapenta->moduleExists($refModule)) { return "(Unknown module: $refModule)"; }
	if (false == $db->objectExists($refModel, $refUID)) { return "(missing owner object)"; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$displaySet = explode(',', $display);

	$html .= ''
	 . "<a href=\""
		 . "javascript:"
		 . "$('#divFloatAttach" . $hta . "').html('');"
		 . "Live_FindAttachmentsModal('" . $hta . "', '" . $display . "');"
	 . "\">[search for media]</a><hr/>";

	foreach($displaySet as $modName) {
		$html .= ''
		 . '[[:' . $modName
		 . '::listattachmentshta'
		 . '::refModule=' . $refModule
		 . '::refModel=' . $refModel
		 . '::refUID=' . $refUID
		 . '::hta=' . $hta
		 . ':]]';
	}

	$html = "<div style='border-style: solid; border-width: 1px; border-color: #eeeeee;'>"
	 . $theme->expandBlocks($html, 'nav1')
	 . "</div>";

	return $html;
}


?>
