<?php

//--------------------------------------------------------------------------------------------------
//|	fired when a tag has been disassociated from an object
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a kapenta module [string]
//arg: refModel - type of object tag was added to [string]
//arg: refUID - UID of object tag was added to [string]
//arg: tagName - name of new tag [string]

function projects__cb_tags_removed($args) {
	global $cache;

	if (false == array_key_exists('refModel', $args)) { return false; }
	if (false == array_key_exists('refUID', $args)) { return false; }

	//----------------------------------------------------------------------------------------------
	//	clear cached project summary if tags have changed
	//----------------------------------------------------------------------------------------------
	
	if ('projects_project' == $args['refModel']) {
		$cache->clear('projects-summary-' . $args['refUID']);
	}

}

?>
