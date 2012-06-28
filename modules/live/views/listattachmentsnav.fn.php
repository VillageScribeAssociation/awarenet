<?php

//--------------------------------------------------------------------------------------------------
//*	list attachments belonging to a given object
//--------------------------------------------------------------------------------------------------
//arg: refModule - module of object which may own attachments [string]
//arg: refModel - type of object which may own attachments [string]
//arg: refUID - UID of object which may own attachments [string]
//arg: display - display attachments from these modules, in this order [string]

function live_listattachmentsnav($args) {
	global $kapenta;
	global $db;
	global $user;
	global $page;
	
	$html = '';				//%	return value

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $args)) { return '(no refModule given)'; }
	if (false == array_key_exists('refModel', $args)) { return '(no refModel given)'; }
	if (false == array_key_exists('refUID', $args)) { return '(no refUID given)'; }
	if (false == array_key_exists('display', $args)) { $args['display'] = 'videos,images,files'; }

	$refModule = $args['refModule'];
	$refModel = $args['refModel'];
	$refUID = $args['refUID'];
	$display = $args['display'];

	if (false == $kapenta->moduleExists($refModule)) { return "(Unknown module: $refModule)"; }
	if (false == $db->objectExists($refModel, $refUID)) { return "(missing owner object)"; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$page->requireJs($kapenta->serverPath . 'modules/live/js/attachments.js');
	$page->requireJs($kapenta->serverPath . 'modules/images/js/editor.js');
	$page->requireJs($kapenta->serverPath . 'modules/videos/js/editor.js');
	$page->requireJs($kapenta->serverPath . 'modules/files/js/editor.js');

	$displaySet = explode(',', $display);

	foreach($displaySet as $modName) {
		$html .= ''
		 . '[[:' . $modName
		 . '::listattachmentsnav'
		 . '::refModule=' . $refModule
		 . '::refModel=' . $refModel
		 . '::refUID=' . $refUID
		 . ':]]';
	}

	$html = ''
	 . "<div"
	 . " id='divAttNav$refUID'"
	 . " class='attachmentsnav'"
	 . " refModule='$refModule'"
	 . " refModel='$refModel'"
	 . " refUID='$refUID'"
	 . " kdisplay='$display'"
	 . ">\n"
	 . $html
	 . "</div>\n";

	return $html;
}

?>
