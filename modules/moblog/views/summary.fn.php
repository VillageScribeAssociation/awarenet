<?

	require_once($kapenta->installPath . 'modules/moblog/models/post.mod.php');
	require_once($kapenta->installPath . 'modules/moblog/models/precache.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summary
//--------------------------------------------------------------------------------------------------
//arg: raUID - Alias or UID of a blog post [string]
//opt: UID - overrides raUID if present [string]

function moblog_summary($args) {
	global $theme;
	global $user;
	global $page;

	$html = '';			//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('UID', $args)) { $args['raUID'] = $args['UID']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }
	$model = new Moblog_Post($args['raUID']);
	if (false == $model->loaded) { return ''; }
	if (false == $user->authHas('moblog', 'moblog_post', 'show', $model->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make and return the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/moblog/views/summary.block.php');
	$labels = $model->extArray();
	$labels['rawblock64'] = base64_encode($args['rawblock']);
	$html = $theme->replaceLabels($labels, $block);

	//----------------------------------------------------------------------------------------------
	//	set AJAX triggers
	//----------------------------------------------------------------------------------------------
	//$channel = 'post-' . $model->UID;
	//$page->setTrigger('moblog', $channel, $args['rawblock']);

	$html = $theme->expandBlocks($html, $args['area']);
	$html = str_replace('[1 comments]', '[1 comment]', $html);

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
