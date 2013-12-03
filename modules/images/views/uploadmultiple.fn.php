<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//|	iframe to upload multiple images
//--------------------------------------------------------------------------------------------------
//arg: refModule - module to list on [string]
//arg: refModel - type of object [string]
//arg: refUID - UID of item which own images [string]
//opt: categories - comma delimited list of categories these pictures can belong to [string]
//opt: tags - display block tags instead of draggable buttons, default is no (yes|no) [string]

function images_uploadmultiple($args) {
	global $kapenta, $db, $user; 
	$html = '';				//%	return value [string]
	$categories = '';		//%	not yet implemented [string]
	$tags = 'no';			//%	diplay block tags instead of draggable buttons (yes|no) [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments as permissions
	//----------------------------------------------------------------------------------------------
	if ((true == array_key_exists('tags', $args)) && ('yes' == $args['tags'])) { $tags = 'yes'; }
	if (false == array_key_exists('refModule', $args)) { return '(no module)'; }
	if (false == array_key_exists('refModel', $args)) { return '(no module)'; }
	if (false == array_key_exists('refUID', $args)) { return '(no UID)'; }

	$refModule = $args['refModule'];
	$refModel = $args['refModel'];
	$refUID = $args['refUID'];

	if (false == $kapenta->moduleExists($refModule)) { return '(no such module)'; }
	if (false == $db->objectExists($refModel, $refUID)) { return '(no such object)'; }
	if (false == $user->authHas($refModule, $refModel, 'images-add', $refUID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the iframe
	//----------------------------------------------------------------------------------------------
	$srcUrl = $kapenta->serverPath . 'images/uploadmultiple/'
		 . 'refModule_' . $args['refModule'] . '/'
		 . 'refModel_' . $args['refModel'] . '/'
		 . 'refUID_' . $args['refUID'] . '/'
		 . 'tags_' . $tags . '/';
		
	if (array_key_exists('categories', $args)) { $srcUrl .= '/cats_' . $args['categories']; }
	$html = "<iframe src='" . $srcUrl . "' name='imgMul" . $refModule . $refUID . "'" 
		. " class='consoleif' width='100%' height='200' frameborder='0'></iframe>";
		
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

