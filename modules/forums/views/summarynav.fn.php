<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/reply.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summarise for the nav (300 wide)
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of forum [string]
//opt: forumUID - overrides raUID [string]

function forums_summarynav($args) {
	global $theme;

	if (array_key_exists('forumUID', $args)) { $args['raUID'] = $args['forumUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new Forums_Board($args['raUID']);	
	$labels = $model->extArray();
	return $theme->replaceLabels($labels, $theme->loadBlock('modules/forums/views/summarynav.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>