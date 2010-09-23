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
		if (false == $kapenta->fileExists($installPath . $row['fileName'])) {
			echo "file " . $row['fileName'] . " does not exist on this server.<br/>\n"; flush();

			//-------------------------------------------------------------------------------------
			//	does not exist on local server, check awarenet.org.za
			//-------------------------------------------------------------------------------------
			
			$raw = implode(@file('http://awarenet.org.za/' . $row['fileName']));
			
			if (strlen($raw) > 0) {
				echo "file " . $row['fileName'] . " exists on awarenet.org.za...<br/>"; flush();
				$check = $kapenta->filePutContents($row['fileName'], $raw, false, false);
				if (true == $check) {
					echo "file saved: " . $row['fileName'] . " <br/>\n"; flush();
				} else {
					echo "file NOT saved: " . $row['fileName'] . " <br/>\n"; flush();
				}
			}

			//echo "<textarea rows='10' cols='80'>$raw</textarea>";

		}

	}

?>
