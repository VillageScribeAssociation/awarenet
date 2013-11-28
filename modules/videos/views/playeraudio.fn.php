<?

	require_once($kapenta->installPath . 'modules/videos/models/video.mod.php');

//--------------------------------------------------------------------------------------------------
//|	make a flowplayer to load a given video
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Videos_Video object [string]
//opt: videoUID - overrides raUID if present [string]

function videos_playeraudio($args) {
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

	$block = $theme->loadBlock('modules/videos/views/playeraudio.block.php');
	$ext = $model->extArray();

	$ext['width'] = '560';
	$ext['height'] = '440';

	$ext['coverImage'] = $kapenta->serverPath . "modules/videos/assets/audio-icon_width570.png";

	$html = $theme->replaceLabels($ext, $block);

	return $html;
}

?>
