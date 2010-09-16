<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/reply.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display a thread
//--------------------------------------------------------------------------------------------------
//arg: threadUID - UID of a forum thread [string]

function forums_showthread($args) {
	global $theme;

	// TODO: auth
	if (array_key_exists('threadUID', $args) == false) { return false; }
	$model = new Forums_Thread($args['threadUID']);
	$html = $theme->replaceLabels($model->extArray(), $theme->loadBlock('modules/forums/views/showthread.block.php'));
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>