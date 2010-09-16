<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/reply.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show a record
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or forums entry [string]

function forums_show($args) {
	global $theme;

	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new Forums_Board($args['raUID']);
	return $theme->replaceLabels($model->extArray(), $theme->loadBlock('modules/forums/views/show.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>