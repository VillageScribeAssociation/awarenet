<?

	require_once($kapenta->installPath . 'modules/videos/models/video.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summarize an item for inserting into a HyperTextArea
//--------------------------------------------------------------------------------------------------
//arg: raUID - UID or alias of a Videos_Video object [string]
//arg: hta - name of a HyperTextArea on the client page [string]
//opt: model - type of object to be inserted (will always be videos_video for this module) [string]
//opt: UID - overrides raUID if present [string]
//opt: fileUID - overrides raUID if present [string]

function videos_summaryhta($args) {
	global $kapenta;
	global $utils;
	global $kapenta;
	global $theme;
	
	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('public' == $kapenta->user->role) { return ''; }

	if (true == array_key_exists('UID', $args)) { $args['raUID'] = $args['UID']; }
	if (true == array_key_exists('fileUID', $args)) { $args['raUID'] = $args['fileUID']; }
	if (false == array_key_exists('raUID', $args)) { return '(no file specified)'; }
	if (false == array_key_exists('hta', $args)) { return '(no hta specified)'; }

	$model = new Videos_Video($args['raUID']);
	if (false == $model->loaded) { return '(file not found)'; }

	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/videos/views/summaryhta.block.php');
	
	$labels = $model->extArray();
	$labels['hta'] = $args['hta'];
	$labels['fileSize'] = $kapenta->fs->size($model->fileName);
	$labels['printFileSize'] = $utils->printFileSize((int)$labels['fileSize']);

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
