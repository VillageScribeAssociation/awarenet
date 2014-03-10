<?

//--------------------------------------------------------------------------------------------------
//|	show iframe console for editing tags, or list of tags if no permissions
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a kapenta module [string]
//arg: refModel - type of object which may have tags [string]
//arg: refUID - UID of object which may have tags [string]

function tags_addtagform($args) {
		global $kapenta;
		global $kapenta;
		global $theme;
		global $user;

	$html = '';		//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $args)) { return '(no refModule)'; }
	if (false == array_key_exists('refModel', $args)) { return '(no refModel)'; }
	if (false == array_key_exists('refUID', $args)) { return '(no refUID)'; }
	
	$refModule = $args['refModule'];
	$refModel = $args['refModel'];
	$refUID = $args['refUID'];

	if (false == $kapenta->moduleExists($refModule)) { return '(no such module)'; }
	if (false == $kapenta->db->objectExists($refModel, $refUID)) { return '(no such owner)'; }

	if (
		(false == $user->authHas($refModule, $refModel, 'tags-add', $refUID)) &&
		(false == $user->authHas($refModule, $refModel, 'tags-manage', $refUID))
	) {
		return '';
	}

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/tags/views/addtagform.block.php');
	$labels = array('refModule' => $refModule, 'refModel' => $refModel, 'refUID' => $refUID);
	$html = $theme->replaceLabels($labels, $block);
	return $html;
}

?>
