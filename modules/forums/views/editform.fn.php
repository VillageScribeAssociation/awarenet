<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/reply.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show the edit form
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or forums entry [string]

function forums_editform($args) {
	global $theme;

	if ($user->authHas('forums', 'Forums_Board', 'edit', 'TODO:UIDHERE') == false) { return false; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new Forums_Board($args['raUID']);
	$ext = $model->extArray();
	$ext['descriptionJs64'] = base64EncodeJs('descriptionJs64', $ext['description']);
	return $theme->replaceLabels($ext, $theme->loadBlock('modules/forums/views/editform.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>