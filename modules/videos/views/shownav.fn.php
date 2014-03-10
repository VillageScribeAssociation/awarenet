<?php

//--------------------------------------------------------------------------------------------------
//|	show a video formatted for display in the nav
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Videos_Video object [string]
//opt: videoUID - overrides raUID if present [string]
//opt: like - show 'like' link (yes|no) [string]

function videos_shownav($args) {
	global $kapenta;
	global $theme;
	global $user;
	global $kapenta;

	$area = 'nav1';						//%	assume nav formatting [string]
	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('videoUID', $args)) { $args['raUID'] = $args['videoUID']; }
	if (false == array_key_exists('raUID', $args)) { return '(video raUID not given)'; }

	$model = new Videos_Video($args['raUID']);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$kapenta->page->requireJs($kapenta->serverPath . 'modules/videos/js/editor.js');
	$block = $theme->loadBlock('modules/videos/views/shownav.block.php');

	$labels = $model->extArray();
	if ('' !== trim($labels['caption'])) { $labels['caption'] .= "<br/>\n"; }

	$html = $theme->replaceLabels($labels, $block);

	return $html;	
}

?>
