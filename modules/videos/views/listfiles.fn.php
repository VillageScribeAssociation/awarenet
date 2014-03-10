<?

	require_once($kapenta->installPath . 'modules/videos/models/video.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all original image files known to image module and present on this peer
//--------------------------------------------------------------------------------------------------
//opt: status - status of files (all|present|missing), default all [string]
//opt: format - format of list to return (xml|csv|html), default xml [string]

function videos_listfiles($args) {
		global $kapenta;
		global $user;
		global $kapenta;


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
	$sql = "select UID, fileName, hash from videos_video";
	$result = $kapenta->db->query($sql);
	while ($row = $kapenta->db->fetchAssoc($result)) { 
		$row = $kapenta->db->rmArray($row);
		$currFile = array($row['UID'], $row['fileName'], $row['hash']);

		if ('all' == $status) {
			//-------------------------------------------------------------------------------------
			// all files, regardless of whether they're present on this peer 			
			//-------------------------------------------------------------------------------------
			$files[] = $currFile;

		} else {
			if (file_exists($kapenta->installPath . $row['fileName']) == true) {
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
					$list .= "  <refModule>videos</refModule>\n";
					$list .= "  <refModel>videos_video</refModel>\n";
					$list .= "  <refUID>" . $file[0] . "</refUID>\n";
					$list .= "  <location>" . $file[1] . "</location>\n";
					$list .= "  <hash>" . $file[2] . "</hash>\n";  
					$list .= "</file>\n";
				}
				break;

		case 'csv':
				//---------------------------------------------------------------------------------
				//	return file list in CSV format
				//---------------------------------------------------------------------------------
				foreach($files as $f) {
					$list .= "videos, videos_video, " . $f[0] . ", " . $f[1] . ", " . $f[2] . "\n";
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
