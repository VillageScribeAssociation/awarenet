<?

	require_once($kapenta->installPath . 'modules/videos/models/video.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form for uploading multiple videos
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a kapenta module [string]
//arg: refModel - object type [string]
//arg: refUID - UID of object which may own videos [string]
//opt: tags - display block tags on return instead of draggable buttons (yes|no) [string]

function videos_uploadmultipleform($args) {
		global $kapenta;
		global $theme;
		global $kapenta;
		global $kapenta;

	$html = '';							//%	return value [string]
	$tags = 'no';

	//----------------------------------------------------------------------------------------------
	//	check args and permissions
	//----------------------------------------------------------------------------------------------
	if ((true == array_key_exists('tags', $args)) && ('yes' == $args['tags'])) { $tags = 'yes'; }
	if (false == array_key_exists('refModule', $args)) { return '(no refModule)'; }
	if (false == array_key_exists('refModel', $args)) { return '(no refModel)'; }
	if (false == array_key_exists('refUID', $args)) { return '(no refUID)'; }

	$refModule = $args['refModule'];
	$refModel = $args['refModel'];
	$refUID = $args['refUID'];

	if (false == $kapenta->moduleExists($refModule)) { return '(no such module)'; }
	if (false == $kapenta->db->objectExists($refModel, $refUID)) { return '(owner not found)'; }
	if (false == $kapenta->user->authHas($refModule, $refModel, 'videos-add', $refUID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	add the form
	//----------------------------------------------------------------------------------------------
	$labels = array(
		'refModule' => $refModule,
		'refModel' => $refModel,
		'refUID' => $refUID,
		'tags' => $tags
	);

	$block = $theme->loadBlock('modules/videos/views/uploadmultiple.block.php');
	$html = $theme->replaceLabels($labels, $block);
	
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
