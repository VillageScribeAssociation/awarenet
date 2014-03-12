<?

//--------------------------------------------------------------------------------------------------
//|	drag and drop live file uploads
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a kapenta module [string]
//arg: refModel - typ of object which may own videos [string]
//arg: refUID - UID of object which may own videos [string]

function images_dragdrop($args) {
	global $kapenta;
	global $kapenta;

	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $args)) { return '(refModule not given)'; }
	if (false == array_key_exists('refModel', $args)) { return '(refModel not given)'; }
	if (false == array_key_exists('refUID', $args)) { return '(refUID not given)'; }

	$refModule = $args['refModule'];
	$refModel = $args['refModel'];
	$refUID = $args['refUID'];

	if (false == $kapenta->moduleExists($refModule)) { return '(unknown module)'; }

	//TODO: permissions check here	

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$scriptUrl = '%%serverPath%%modules/live/js/uploader.js';
	$notifyUrl = ''
	 . '%%serverPath%%video/ajaxupload/'
	 . 'refModule_' . $refModule . '/'
	 . 'refModel_' . $refModel . '/'
	 . 'refUID_' . $refUID . '/';

	$html = ''
	 . "<div id='divImageDragDrop'></div>"
	 . "<script src='$scriptUrl' language='Javascript'></script>\n"
	 . "<script language='Javascript'>\n"
	 . "\tvar imageDragDrop = new Live_Uploader("
		. "'divImageDragDrop', 'image', '$refModule', '$refModel', '$refUID'"
	 . ");\n"
	 . "\timageDragDrop.render();\n"
	 . "</script>\n";

	return $html;
}

?>
