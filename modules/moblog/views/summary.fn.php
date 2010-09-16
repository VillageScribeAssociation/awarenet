<?

	require_once($kapenta->installPath . 'modules/moblog/models/post.mod.php');
	require_once($kapenta->installPath . 'modules/moblog/models/precache.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summary
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a blog post

function moblog_summary($args) {
	global $theme;
	$html = '';			//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('UID', $args)) { return ''; }
	$model = new Moblog_Post($args['UID']);
	if (false == $model->loaded) { return ''; }
	if (false == $user->authHas('moblog', 'Moblog_Post', 'show', $model->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make and return the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/moblog/views/summary.block.php')
	$html = $theme->replaceLabels($model->extArray(), $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
