<?

//--------------------------------------------------------------------------------------------------
//*	make a 'like' link for some object
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a kapenta module [string]
//arg: refModel - type of object being endorsed [string]
//arg: refUID - UID of object being endorsed [string]

function like_link($args) {
	global $theme;
	global $kapenta;
	global $kapenta;
	global $user;

	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if (('public' == $user->role) || ('banned' == $user->role)) { return ''; }

	if (false == array_key_exists('refModule', $args)) { return '(refModule not given)'; }
	if (false == array_key_exists('refModel', $args)) { return '(refModel not given)'; }
	if (false == array_key_exists('refUID', $args)) { return '(refUID not given)'; }

	$refModule = $args['refModule'];
	$refModel = $args['refModel'];
	$refUID = $args['refUID'];

	if (false == $kapenta->moduleExists($args['refModule'])) { return 'Unknown module.'; }
	if (false == $kapenta->db->objectExists($args['refModel'], $args['refUID'])) { return '(unk UID)'; }

	//----------------------------------------------------------------------------------------------
	//	check if user already likes this item
	//----------------------------------------------------------------------------------------------
	$byuser = ''
	 . "[[:like::byuser"
	 . "::userUID=" . $user->UID
	 . "::refModule=$refModule::refModel=$refModel::refUID=$refUID:]]";

	$exUID = $theme->expandBlocks($byuser, '');

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/like/views/link.block.php');

	$labels = array(
		'refModule' => $refModule,
		'refModel' => $refModel,
		'refUID' => $refUID,
		'jsCall' => 'like_assert',
		'jsLabel' => 'like'
	);

	if ('' != $exUID) {
		$labels['jsCall'] = 'like_unassert';
		$labels['jsLabel'] = 'unlike';
	}

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
