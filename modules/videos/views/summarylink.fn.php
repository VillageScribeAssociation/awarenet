<?php

//--------------------------------------------------------------------------------------------------
//|	provides a summary of an object as used by search
//--------------------------------------------------------------------------------------------------
//arg: model - type of object to display [string]
//arg: raUID - UID of object to display [string]

function videos_summarylink($args) {
	global $theme;

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('model', $args)) { return '(model not given)'; }
	if (false == array_key_exists('raUID', $args)) { return '(raUID not given)'; }
	//TODO: check permissions

	//----------------------------------------------------------------------------------------------
	//	show the object
	//----------------------------------------------------------------------------------------------
	switch($args['model']) {
		case 'videos_gallery':
			$html = "[[:videos::summarynav::galleryUID=" . $args['raUID'] . ":]]";
			$html = $theme->expandBlocks($html);
			break;		

		case 'videos_video':
			$html = "[[:videos::videosummarynav::videoUID=" . $args['raUID'] . ":]]";
			$html = $theme->expandBlocks($html);
			break;		

	}

	return $html;
}

?>
