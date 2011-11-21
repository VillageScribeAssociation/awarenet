<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/reply.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show the edit form
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or forums entry [string]

function forums_editform($args) {
	global $theme, $user, $utils;

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('raUID', $args)) { return ''; }
	$model = new Forums_Board($args['raUID']);
	if (false == $model->loaded) { return ''; }
	if (false == $user->authHas('forums', 'forums_board', 'edit', $model->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$ext = $model->extArray();
	$ext['description64'] = $utils->b64wrap($ext['description']);
	$block = $theme->loadBlock('modules/forums/views/editform.block.php');
	$html = $theme->replaceLabels($ext, $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
