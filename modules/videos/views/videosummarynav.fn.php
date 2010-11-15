<?

	require_once($kapenta->installPath . 'modules/videos/models/video.mod.php');

//--------------------------------------------------------------------------------------------------
//|	short summary of video for the nav
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Videos_Video object [string]
//opt: videoUID - replaces raUID if present [string]

function videos_videosummarynav($args) {
	global $user, $theme;
	$html = '';		//%	return value [html]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('videoUID', $args)) { $args['raUID'] = $args['videoUID']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }

	$model = new Videos_Video($args['raUID']);
	if (false == $model->loaded) { return ''; }
	//TODO: check permissions here

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/videos/views/videosummarynav.block.php');
	$ext = $model->extArray();
	$html = $theme->replaceLabels($ext, $block);

	return $html;
}

?>
