<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/reply.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display a thread
//--------------------------------------------------------------------------------------------------
//arg: threadUID - UID of a forum thread [string]

function forums_showthread($args) {
    global $kapenta;

	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('threadUID', $args)) { return '(thread UID not given)'; }

	$model = new Forums_Thread($args['threadUID']);
	if (false == $model->loaded) { return '(thread not found)'; }
	// TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $kapenta->theme->loadBlock('modules/forums/views/showthread.block.php');
	$html = $kapenta->theme->replaceLabels($model->extArray(), $block);

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
