<?

	require_once($kapenta->installPath . 'modules/videos/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summarise
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Videos_Gallery object [string]
//opt: pageUID - overrides raUID [string]

function videos_summary($args) {
		global $theme;
		global $kapenta;

	$html = '';		//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('public' == $kapenta->user->role) { return '[[:users::pleaselogin:]]'; }
	if (true == array_key_exists('pageUID', $args)) { $args['raUID'] = $args['pageUID']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }

	$model = new Videos_Gallery($args['raUID']);
	if (false == $model->loaded) { return ''; }
	//TODO: permissions check here

	$labels = $model->extArray();
	//TODO: live JS

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/videos/views/summary.block.php');
	$html = $theme->replaceLabels($labels, $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

