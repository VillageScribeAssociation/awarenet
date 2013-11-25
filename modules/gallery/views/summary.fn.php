<?

	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summarise
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of a gallery [string]
//opt: pageUID - overrides raUID [string]

function gallery_summary($args) {
	global $theme;
	global $user;
	global $cache;

	$html = '';		//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { return '[[:users::pleaselogin:]]'; }
	if (true == array_key_exists('pageUID', $args)) { $args['raUID'] = $args['pageUID']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }

	$model = new Gallery_Gallery($args['raUID']);
	if (false == $model->loaded) { return ''; }
	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	check the cache
	//----------------------------------------------------------------------------------------------
	if ($model->createdBy != $user->UID) {
		$html = $cache->get($args['area'], $args['rawblock']);
		if ('' != $html) { return $html; }
	}

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$labels = $model->extArray();
	//TODO: live JS

	$block = $theme->loadBlock('modules/gallery/views/summary.block.php');
	$html = $theme->replaceLabels($labels, $block);

	if ($model->createdBy != $user->UID) {
		$html = $theme->expandBlocks($html, $args['area']);
		$cache->set('gallery-show-' . $model->UID, $args['area'], $args['rawblock'], $html);
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

