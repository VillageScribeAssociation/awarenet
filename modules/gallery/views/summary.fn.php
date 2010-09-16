<?

	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summarise
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of a gallery [string]
//opt: pageUID - overrides raUID [string]

function gallery_summary($args) {
	global $theme;
	$html = '';		//% return value [string]

	if (true == array_key_exists('pageUID', $args)) { $args['raUID'] = $args['pageUID']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }

	$model = new Gallery_Gallery($args['raUID']);

	$block = $theme->loadBlock('modules/gallery/views/summary.block.php');
	$html = $theme->replaceLabels($model->extArray(), $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

