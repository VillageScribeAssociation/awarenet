<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//-------------------------------------------------------------------------------------------------
//	look for dead images (those with a record but not file on any peer)
//-------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------
	//	admins only
	//---------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }


	//---------------------------------------------------------------------------------------------
	//	go through all images
	//---------------------------------------------------------------------------------------------

	$sql = "select * from Images_Image";
	$result =  $db->query($sql);
	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);

		//-----------------------------------------------------------------------------------------
		//	check local file
		//-----------------------------------------------------------------------------------------
		if (false == file_exists($installPath . $row['fileName'])) {
			echo "file " . $row['fileName'] . " does not exist on this server.<br/>\n";

			//-------------------------------------------------------------------------------------
			//	does not exist on local server, check awarenet.org.za
			//-------------------------------------------------------------------------------------
			
			$raw = implode(@file('http://awarenet.org.za/' . $row['fileName']));
			
			if (strlen($raw) > 0) {
				echo "file exists on awarenet.org.za...<br/>";
			}

			//echo "<textarea rows='10' cols='80'>$raw</textarea>";

		}

	}

?>
