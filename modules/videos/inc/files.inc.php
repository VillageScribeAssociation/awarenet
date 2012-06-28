<?php

//--------------------------------------------------------------------------------------------------
//*	file management and ownership methods for this module
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//|	discover which object owns a file
//--------------------------------------------------------------------------------------------------
//returns: dict of 'module', 'model' and 'UID', or empty array if not found [array]

function videos_fileOwner($path) {
	global $db;
	$owner = array();
	$conditions = array("fileName='" . $db->addMarkup($path) . "'");
	$range = $db->loadRange('videos_video', '*', $conditions);

	foreach($range as $item) {
		$owner['module'] = 'videos';
		$owner['model'] = 'videos_video';
		$owner['UID'] = $item['UID'];
	}
	
	return $owner;
}

?>
