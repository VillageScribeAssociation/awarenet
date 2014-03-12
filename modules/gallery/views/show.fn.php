<?

	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show a record
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of a gallery [string]

function gallery_show($args) {
	global $theme;
	global $kapenta;
	global $cache;

	$html = '';				//% return value;

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('raUID', $args)) { return ''; }
	$model = new Gallery_Gallery($args['raUID']);
	if (false == $model->loaded) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	check block cache
	//----------------------------------------------------------------------------------------------
	if ($kapenta->user->UID != $model->createdBy) {
		$html = $cache->get($args['area'], $args['rawblock']);
		if ('' != $html) { return $html; }
	}

	//----------------------------------------------------------------------------------------------
	//	make and cache the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/gallery/views/show.block.php');
	$html = $theme->replaceLabels($model->extArray(), $block);

	if ($kapenta->user->UID != $model->createdBy) {
		$html = $theme->expandBlocks($html, $args['area']);
		$cache->set('gallery-show-' . $model->UID, $args['area'], $args['rawblock'], $html);
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
