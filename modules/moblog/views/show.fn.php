<?

	require_once($kapenta->installPath . 'modules/moblog/models/post.mod.php');
	require_once($kapenta->installPath . 'modules/moblog/models/precache.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of a post [string]
//opt: UID - overrides raUID if present [string]

function moblog_show($args) {
	global $theme;
	global $kapenta;
	global $kapenta;

	$html = '';				//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions	
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('UID', $args)) { $args['raUID'] = $args['UID']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }
	$model = new Moblog_Post($args['raUID']);
	if (false == $model->loaded) { return ''; }
	if (false == $kapenta->user->authHas('moblog', 'moblog_post', 'show', $model->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$labels = $model->extArray();
	$labels['rawblock64'] = base64_encode($args['rawblock']);

	$block = $theme->loadBlock('modules/moblog/views/show.block.php');
	$html = $theme->replaceLabels($labels, $block);

	//----------------------------------------------------------------------------------------------
	//	set AJAX triggers
	//----------------------------------------------------------------------------------------------
	$channel = 'post-' . $model->UID;
	// $kapenta->page->setTrigger('moblog', $channel, $args['rawblock']);

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
