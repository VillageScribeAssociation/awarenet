<?

	require_once($kapenta->installPath . 'modules/videos/models/video.mod.php');

//--------------------------------------------------------------------------------------------------
//|	make a flowplayer to load a given video
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Videos_Video object [string]
//opt: videoUID - overrides raUID if present [string]

function videos_player($args) {
	global $theme, $user;
	$html = '';		//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('videoUID', $args)) { $args['raUID'] = $args['videoUID']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }

	$model = new Videos_Video($args['raUID']);
	if (false == $model->loaded) { return ''; }
	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	if ('swf' == $model->format) {
		//------------------------------------------------------------------------------------------
		//	flash animation
		//------------------------------------------------------------------------------------------
		$html .= "(cannot preview this object)";

	} else {
		//------------------------------------------------------------------------------------------
		//	flash or mp4 video
		//------------------------------------------------------------------------------------------
		$block = $theme->loadBlock('modules/videos/views/player.block.php');
		$ext = $model->extArray();

		$ext['width'] = '560';
		$ext['height'] = '420';

		$html = $theme->replaceLabels($ext, $block);

	}

	return $html;
}

?>
