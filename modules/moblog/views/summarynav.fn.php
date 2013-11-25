<?

	require_once($kapenta->installPath . 'modules/moblog/models/post.mod.php');
	require_once($kapenta->installPath . 'modules/moblog/models/precache.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summarise a post for the nav (300 wide)
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or groups entry [string]
//opt: postUID - overrides raUID [string]

function moblog_summarynav($args) {
	global $theme;
	global $user;
	global $cache;

	$html = '';			//% return value [html]

	//----------------------------------------------------------------------------------------------
	//	check view cache
	//----------------------------------------------------------------------------------------------
	$html = $cache->get($args['area'], $args['rawblock']);
	if ('' != $html) { return $html; }

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('postUID', $args)) { $args['raUID'] = $args['postUID']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }
	$model = new Moblog_Post($args['raUID']);
	if (false == $model->loaded) { return ''; }
	if (false == $user->authHas('moblog', 'moblog_post', 'show', $model->UID)) { return ''; }
	
	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/moblog/views/summarynav.block.php');
	$html = $theme->replaceLabels($model->extArray(), $block);

	$html = $theme->expandBlocks($html, $args['area']);
	$cache->set('moblog-summarynav-' . $model->UID, $args['area'], $args['rawblock'], $html);

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
