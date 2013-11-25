<?php

//--------------------------------------------------------------------------------------------------
//|	fired when an object is removed from the database
//--------------------------------------------------------------------------------------------------
//arg: module - name of a kapenta module [string]
//arg: model - type of object which was deleted [string]
//arg: UID - UID of object which was deleted [string]
//arg: data - dict of deleted object's properties [string]

function gallery__cb_object_deleted($args) {
	global $cache;

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('module', $args)) { return false; }
	if (false == array_key_exists('model', $args)) { return false; }
	if (false == array_key_exists('UID', $args)) { return false; }
	if (false == array_key_exists('data', $args)) { return false; }

	$data = $args['data'];

	//----------------------------------------------------------------------------------------------
	//	check if deleted object was owned by a gallery_gallery object
	//----------------------------------------------------------------------------------------------

	if (
		(true == array_key_exists('refUID', $data)) &&
		(true == array_key_exists('refModel', $data)) &&
		('gallery_gallery' == $data['refModel'])
	) {
		//------------------------------------------------------------------------------------------
		//	clear cached views of this gallery
		//------------------------------------------------------------------------------------------
		$cache->clear('gallery-show-' . $data['refUID']);
		$cache->clear('gallery-summary-' . $data['refUID']);
		$cache->clear('gallery-summarynav-' . $data['refUID']);
	}
}

?>
