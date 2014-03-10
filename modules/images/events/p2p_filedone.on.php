<?

//--------------------------------------------------------------------------------------------------
//|	raised when a file has been downloaded from a peer
//--------------------------------------------------------------------------------------------------
//arg: fileName - location relative to installPath [string]

function images__cb_p2p_filedone($args) {
	global $kapenta;
	global $kapenta;

	if (false == array_key_exists('fileName', $args)) { return false; }

	//----------------------------------------------------------------------------------------------
	//	check if this file is attached to an images_image object
	//----------------------------------------------------------------------------------------------
	$conditions = array("fileName='" . $kapenta->db->addMarkup($args['fileName']) . "'");
	$range = $kapenta->db->loadRange('images_image', '*', $conditions);

	foreach($range as $item) {
		$msg = '' 
		 . 'Images module claims file: ' . $args['fileName'] . ' '
		 . 'for object images_image::' . $item['UID'];

		$kapenta->logP2P($msg);

		$args = array(
			'type' => 'file',
			'model' => 'images_image',
			'UID' => $item['UID'],
			'fileName' => $args['fileName']
		);

		$kapenta->raiseEvent('*', 'file_received', $args);
		return true;
	}

	return false;
}

?>
