<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/reply.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summarise
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of forums page [string]
//opt: forumUID - overrides raUID [string]

function forums_summary($args) {
	global $db;

	global $theme;

	if (array_key_exists('forumUID', $args)) { $args['forumUID'] = $args['pageUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new Forums_Board($db->addMarkup($args['raUID']));	
	return $theme->replaceLabels($model->extArray(), $theme->loadBlock('modules/forums/views/summary.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>