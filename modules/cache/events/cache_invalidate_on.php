<?php

//-------------------------------------------------------------------------------------------------
//|	fired when some other module requests that all caches be invalidated
//-------------------------------------------------------------------------------------------------
//arg: model - type of object which has been changed or deleted [string]
//arg: UID - UID of changed / deleted object [string]
//arg: data - array of key => value pairs [array:string]

function cache__cb_cache_invalidate($args) {
	global $kapenta;

	//---------------------------------------------------------------------------------------------
	//	check arguments
	//---------------------------------------------------------------------------------------------
	if (false == array_key_exists('model', $args)) { return false; }
	if (false == array_key_exists('UID', $args)) { return false; }

	//---------------------------------------------------------------------------------------------
	//	wipe this from basic memcache
	//---------------------------------------------------------------------------------------------
	$cacheKey = $args['model'] . '::' . $args['UID'];
	$kapenta->cacheDelete($cacheKey);

	return true;
}

?>
