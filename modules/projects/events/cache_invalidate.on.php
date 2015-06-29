<?php

//--------------------------------------------------------------------------------------------------
//*	an object has been updated, clear memory and disk caches
//--------------------------------------------------------------------------------------------------
//arg: model - type of object to be removed from cache [string]
//arg: UID - UID of object to be removed from cache [string]

function projects__cb_cache_invalidate($args) {
	global $cache;

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('model', $args)) { return false; }
	if (false == array_key_exists('UID', $args)) { return false; }

	//----------------------------------------------------------------------------------------------
	//	discover what must be invalidated
	//----------------------------------------------------------------------------------------------
	
	if ('projects_project' == $args['model']) {
		$cache->clear('projects-summary-' . $args['UID']);
		$cache->clear('projects-summarynav-' . $args['UID']);
	}

	if ('projects_membership' == $args['model']) {
		$cache->clear('projects-membersnav-' . $args['UID']);
		$cache->clear('projects-samemembersnav-' . $args['UID']);		
	}

}

?>
