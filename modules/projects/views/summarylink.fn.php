<?php

//--------------------------------------------------------------------------------------------------
//|	provides a summary of an object as used by search
//--------------------------------------------------------------------------------------------------
//arg: model - type of object to display [string]
//arg: raUID - UID of object to display [string]

function projects_summarylink($args) {
	global $theme;

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('model', $args)) { return '(model not given)'; }
	if (false == array_key_exists('raUID', $args)) { return '(raUID not given)'; }
	if ('projects_project' != $args['model']) { return '(object type not supported)'; }
	//TODO: check permissions

	//----------------------------------------------------------------------------------------------
	//	show the object
	//----------------------------------------------------------------------------------------------
	$html = "[[:projects::summarynav::projectUID=" . $args['raUID'] . ":]]";
	$html = $theme->expandBlocks($html);

	return $html;
}

?>
