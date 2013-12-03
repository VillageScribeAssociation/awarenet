<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//-------------------------------------------------------------------------------------------------
//*	repair damaged image references
//-------------------------------------------------------------------------------------------------
//TODO: discover if this is still necessary, remove if not

	//---------------------------------------------------------------------------------------------
	//	must be admin
	//---------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	//---------------------------------------------------------------------------------------------
	//	gather some system data
	//---------------------------------------------------------------------------------------------
	$tables = $db->loadTables();

	//---------------------------------------------------------------------------------------------
	//	go through image table looking for dead references
	//---------------------------------------------------------------------------------------------
	$sql = "select UID, refUID, refModule, title from images_image";
	$result = $db->query($sql);
	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);
		$extenuate = false;											// some are just shorter
		if ($row['refUID'] == 'admin') { $extenuate = true; }		// administrator

		if ((strlen($row['refUID']) < 18) && (false == $extenuate)) {
			//-------------------------------------------------------------------------------------
			//	OK, so the reference is probably bad
			//-------------------------------------------------------------------------------------
			echo "image " . $row['title'] . " (" . $row['UID'] . ") is not linked to owner " 
				 . "('" . $row['refUID'] . "...' on " . $row['refModule'] . " module)<br/>\n";

			//-------------------------------------------------------------------------------------
			//	discover if refModule corresponds to a table
			//-------------------------------------------------------------------------------------
			if (true == in_array($row['refModule'], $tables)) {
				//---------------------------------------------------------------------------------
				//	look for a similar UID
				//---------------------------------------------------------------------------------
				$sql = "select UID from " . $row['refModule'] . " "
					 . "where LEFT(UID, " . strlen($row['refUID']) . ") = '" . $row['refUID'] . "'";
			
				$try = $db->query($sql);
				if ($db->numRows($try) > 0) {
					// found it, repair
					$match = $db->fetchAssoc($try);
					echo "found matching UID: " . $match['UID'] . " ~ " . $row['refUID'] . "<br/>\n";

					$sql = "update images_image set refUID='" . $match['UID'] . "' where UID='" . $row['UID'] . "'";
					$db->query($sql);
					echo $sql . "<br/>\n";

				} else {
					// no such record
					echo "no matching record found in table " . $row['refModule'] . "<br/>\n";
				}

				
			} else {
				//---------------------------------------------------------------------------------
				//	unsure which table to search
				//---------------------------------------------------------------------------------
				echo "unsure which table to search for module '" . $row['refModule'] . "'<br/>\n";
			}

		}

		//-----------------------------------------------------------------------------------------
		//	save the image
		//-----------------------------------------------------------------------------------------
		$model = new Images_Image($row['UID']);
		$model->save();

	}

?>
