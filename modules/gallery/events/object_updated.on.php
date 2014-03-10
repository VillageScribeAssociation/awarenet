<?php

//--------------------------------------------------------------------------------------------------
//*	called when an object is saved to the database
//--------------------------------------------------------------------------------------------------
//arg: module - module which owned the deleted object [string]
//arg: model - type of deleted object [string]
//arg: UID - UID of deleted object [string]

function gallery__cb_object_updated($args) {
	global $cache;
	global $kapenta;

	if (false == array_key_exists('model', $args)) { return false; }
	if (false == array_key_exists('UID', $args)) { return false; }
	if (false == array_key_exists('data', $args)) { return false; }

	$data = $args['data'];

	//----------------------------------------------------------------------------------------------
	//	clear cached summary and display when a gallery_gallery object is saved
	//----------------------------------------------------------------------------------------------

	if ('gallery_gallery' == $args['model']) {
		$cache->clear('gallery-show-' . $args['UID']);
		$cache->clear('gallery-summary-' . $args['UID']);
		$cache->clear('gallery-summarynav-' . $args['UID']);
		//	^	add more channels here
	}

	//----------------------------------------------------------------------------------------------
	//	clear cached gallery summary / show views when something is added
	//----------------------------------------------------------------------------------------------
	//	ie, an image, comment, tag, etc has been added to this object

	if (
		(true == array_key_exists('refUID', $data)) &&
		(true == array_key_exists('refModel', $data)) &&
		('gallery_gallery' == $data['refModel'])
	) { 	
		$cache->clear('gallery-show-' . $data['refUID']);
		$cache->clear('gallery-summary-' . $data['refUID']);
		$cache->clear('gallery-summarynav-' . $data['refUID']);
	}

	return true;
}

?>
