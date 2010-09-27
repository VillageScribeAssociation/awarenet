<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');
	require_once($kapenta->installPath . 'modules/images/inc/images__widthx.inc.php');

//--------------------------------------------------------------------------------------------------
//|	list all original image files known to image module and present on this peer
//--------------------------------------------------------------------------------------------------
//opt: status - status of files (all|present|missing), default all [string]
//opt: format - format of list to return (xml|csv|html), default xml [string]

function images_listfiles($args) {
	global $db;
 
	global $user;
	global $installPath;

	$status = 'all';
	$format = 'xml';
	$list = '';
	$files = array();

	//---------------------------------------------------------------------------------------------
	//	check arguments
	//---------------------------------------------------------------------------------------------
	if (array_key_exists('status', $args) == true) { $status = $args['status']; }
	if (array_key_exists('format', $args) == true) { $format = $args['format']; }
	//if ('admin' != $user->role) { return ''; } // TODO: sync auth
	
	//---------------------------------------------------------------------------------------------
	//	consider files of all image records
	//---------------------------------------------------------------------------------------------

	//TODO: consider dbLoadRange (possible memory issue)
	$sql = "select UID, fileName from Images_Image";
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
			if (file_exists($installPath . $row['fileName']) == true) {
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
					$list .= "  <reftable>images</reftable>\n";
					$list .= "  <refuid>" . $file[0] . "</refuid>\n";
					$list .= "  <location>" . $file[1] . "</location>\n"; 
					$list .= "</file>\n";
				}
				break;

		case 'csv':
				//---------------------------------------------------------------------------------
				//	return file list in CSV format
				//---------------------------------------------------------------------------------
				foreach($files as $file) {
					$list .= "images, " . $file[0] . ", " . $file[1] . "\n";
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