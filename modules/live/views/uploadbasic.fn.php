<?

//--------------------------------------------------------------------------------------------------
//|	make a basic multipart/form-data upload form, legacy mode
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a kapenta module [string]
//arg: refModel - type of object which may have attached files [string]
//arg: refUID - UID of object which may have attached files [string]

function live_uploadbasic($args) {
	global $user;
	global $theme;
	global $kapenta;
	global $kapenta;

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	//TODO: hardcoded for now, remove when permissions are more solid on attachments
	if (('banned' == $user->role) || ('public' == $user->role)) { return ''; }

	if (false == array_key_exists('refModule', $args)) { return '(no refModule given)'; }
	if (false == array_key_exists('refModel', $args)) { return '(no refModel given)'; }
	if (false == array_key_exists('refUID', $args)) { return '(no refUID given)'; }

	$refModule = $args['refModule'];
	$refModel = $args['refModel'];
	$refUID = $args['refUID'];

	if (false == $kapenta->moduleExists($refModule)) { return '(unknown module)'; }
	if (false == $kapenta->db->objectExists($refModel, $refUID)) { return '(owner object not found)'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	$block = $theme->loadBlock('modules/live/views/uploadbasic.block.php');

	$labels = array(
		'refModule' => $refModule,
		'refModel' => $refModel,
		'refUID' => $refUID
	);
	$html = $theme->replaceLabels($labels, $block);
	return $html;
}

?>
