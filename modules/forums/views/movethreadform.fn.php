<?

	require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');

//-------------------------------------------------------------------------------------------------
//	makes a form for moving a forum thread
//-------------------------------------------------------------------------------------------------
//arg: raUID - UID or recordAlias fo a forum thread
//opt: thread - replaces raUID if present
//opt: UID - replaces raUID if present

function forums_movethreadform($args) {
	global $theme;

	$html = '';	

	//---------------------------------------------------------------------------------------------
	//	check arguments and auth
	//---------------------------------------------------------------------------------------------
	if (true == array_key_exists('thread', $args)) { $args['raUID'] == $args['thread']; }
	if (true == array_key_exists('UID', $args)) { $args['raUID'] == $args['UID']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }
	//TODO: permissions check here	

	//---------------------------------------------------------------------------------------------
	//	load the model
	//---------------------------------------------------------------------------------------------
	$model = new Forums_Thread($args['raUID']);
	if (false == $model->loaded) { return ''; }

	//---------------------------------------------------------------------------------------------
	//	make and return the block
	//---------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/forums/views/movethreadform.block.php');
	$html = $theme->replaceLabels($model->extArray(), $block);
	return $html;
}

?>