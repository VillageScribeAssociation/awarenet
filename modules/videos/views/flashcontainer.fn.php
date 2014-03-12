<?

	require_once($kapenta->installPath . 'modules/videos/models/video.mod.php');

//--------------------------------------------------------------------------------------------------
//|	make swf object
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Videos_Video object, format must be swf [string]
//opt: videoUID - overrides raUID if present [string]
//opt: width - width of flash player (int) [string]
//opt: height - height of flash player (int) [string]

function videos_flashcontainer($args) {
		global $theme;
		global $kapenta;

	$html = '';			//%	return value [string]
	$swfWidth = 968;	//%	flash object width [int]	
	$swfHeight = 672;	//%	flash object height [int]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('width', $args)) { $swfWidth = (int)$args['width']; }
	if (true == array_key_exists('height', $args)) { $swfHeight = (int)$args['height']; }
	if (true == array_key_exists('videoUID', $args)) { $args['raUID'] = $args['videoUID']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }

	$model = new Videos_Video($args['raUID']);
	if (false == $model->loaded) { return ''; }
	//TODO: check permissions here

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/videos/views/flashcontainer.block.php');
	$ext = $model->extArray();

	$ext['swfWidth'] = $swfWidth;
	$ext['swfHeight'] = $swfHeight;
	$ext['swfFile'] = '%%serverPath%%' . $model->fileName;

	$html = $theme->replaceLabels($ext, $block);
	return $html;
}

?>
