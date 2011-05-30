<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');
	require_once($kapenta->installPath . 'modules/images/inc/images__widthx.inc.php');

//--------------------------------------------------------------------------------------------------
//|	list all original image files known to image module and present on this peer
//--------------------------------------------------------------------------------------------------
//opt: status - status of files (all|present|missing), default all [string]
//opt: format - format of list to return (xml|csv|html), default xml [string]

function images_listfiles($args) {
	global $db, $user, $kapenta;

	$status = 'all';
	$format = 'xml';
	$list = '';
	$files = array();

	//---------------------------------------------------------------------------------------------
	//	check arguments
	//---------------------------------------------------------------------------------------------
	if (true == array_key_exists('status', $args)) { $status = $args['status']; }
	if (true == array_key_exists('format', $args)) { $format = $args['format']; }
	//if ('admin' != $user->role) { return ''; } // TODO: sync auth
	
	//---------------------------------------------------------------------------------------------
	//	consider files of all image records
	//---------------------------------------------------------------------------------------------

	//TODO: consider dbLoadRange (possible memory issue)
	$sql = "select UID, fileName from images_image";
	$result = $db->query($sql);
	while ($row = $db->fetchAssoc($result)) { 
		$row = $db->rmArray($row);
		$currFile = array($row['UID'], $row['fileName']);

		if ('all' == $status) {
			//-------------------------------------------------------------------------------------
			// all files, regardless of whether they're present on this peer 			
			//-------------------------------------------------------------------------------------
			$files[] = $currFile;

		} else {
			if (true == file_exists($kapenta->installPath . $row['fileName'])) {
				//---------------------------------------------------------------------------------
				// only files present on this peer
				//---------------------------------------------------------------------------------
				if ('present' == $status) { $files[] = $currFile; }

			} else {
				//---------------------------------------------------------------------------------
				// only files not present on this peer
				//---------------------------------------------------------------------------------
				if ('missing' == $status) { $files[] = $currFile; }
			}
		}

	}

	//---------------------------------------------------------------------------------------------
	//	format list of files
	//---------------------------------------------------------------------------------------------

	switch ($format) {

		case 'xml':
				//---------------------------------------------------------------------------------
				//	return file list in XML format
				//---------------------------------------------------------------------------------
				foreach($files as $file) {
					$list .= "<file>\n";
					$list .= "  <refModule>images</refModule>\n";
					$list .= "  <refModel>images_image</refModel>\n";
					$list .= "  <refUID>" . $file[0] . "</refUID>\n";
					$list .= "  <location>" . $file[1] . "</location>\n"; 
					$list .= "</file>\n";
				}
				break;

		case 'csv':
				//---------------------------------------------------------------------------------
				//	return file list in CSV format
				//---------------------------------------------------------------------------------
				foreach($files as $file) {
					$list .= "images, images_image, " . $file[0] . ", " . $file[1] . "\n";
				}
				break;

		case 'html':
				//---------------------------------------------------------------------------------
				//	make HTML table
				//---------------------------------------------------------------------------------
				// TODO
				break;

	}			

	return $list;
}

//--------------------------------------------------------------------------------------------------

?>
