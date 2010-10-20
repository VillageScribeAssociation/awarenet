<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form for downloading multiple images
//--------------------------------------------------------------------------------------------------
//arg: refModule - module to which image may be exported [string]
//arg: refModel - type of object which will own image [string]
//arg: refUID - object which will own the downloaded image [string]
//opt: tags - display block tags on return instead of draggable buttons (yes|no) [string]

function images_downloadmultipleform($args) {
	global $theme, $user;
	$html = '';					//%	return value [string]
	$tags = 'no';				

	//----------------------------------------------------------------------------------------------
	//	check args and authorisation
	//----------------------------------------------------------------------------------------------
	if ((true == array_key_exists('tags', $args)) && ('yes' == $args['tags'])) { $tags = 'yes'; }
	if (false == array_key_exists('refModule', $args)) { return '(no refModule)'; }
	if (false == array_key_exists('refModel', $args)) { return '(no refModel)'; }
	if (false == array_key_exists('refUID', $args)) { return '(no refUID)'; }

	if (false == $user->authHas($args['refModule'], $args['refModel'], 'images-add', $args['refUID']))
		{ return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the form
	//----------------------------------------------------------------------------------------------
	$labels = array(
		'refModule' => $args['refModule'],
		'refModel' => $args['refModel'],
		'refUID' => $args['refUID'],
		'tags' => $args['tags']
	);

	$block = $theme->loadBlock('modules/images/views/downloadmultiple.block.php');
	$html = $theme->replaceLabels($labels, $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
