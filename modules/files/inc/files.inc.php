<?php

//--------------------------------------------------------------------------------------------------
//*	file management and ownership methods for this module (standard kapenta files API)
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//|	discover which object owns a file
//--------------------------------------------------------------------------------------------------
//returns: dict of 'module', 'model' and 'UID', or empty array if not found [array]

function files_fileOwner($path) {
	global $kapenta;

	$owner = array();
	$conditions = array("fileName='" . $kapenta->db->addMarkup($path) . "'");
	$range = $kapenta->db->loadRange('files_file', '*', $conditions);

	foreach($range as $item) {
		$owner['module'] = 'files';
		$owner['model'] = 'files_file';
		$owner['UID'] = $item['UID'];
	}
	
	return $owner;
}

?>
