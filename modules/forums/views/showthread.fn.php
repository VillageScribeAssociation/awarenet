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
	global $user;

	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('threadUID', $args)) { return '(thread UID not given)'; }

	$model = new Forums_Thread($args['threadUID']);
	if (false == $model->loaded) { return '(thread nto found)'; }
	// TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/forums/views/showthread.block.php');
	$html = $theme->replaceLabels($model->extArray(), $block);

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
