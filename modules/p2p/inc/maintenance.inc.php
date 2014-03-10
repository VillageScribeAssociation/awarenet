<?

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');
	require_once($kapenta->installPath . 'modules/p2p/models/downloads.set.php');

//-------------------------------------------------------------------------------------------------
//*	maintain the p2p module
//-------------------------------------------------------------------------------------------------

function p2p_maintenance() {
	global $kapenta;
	global $kapenta;
	global $theme;

	//---------------------------------------------------------------------------------------------
	//	check active downloads for all peers
	//---------------------------------------------------------------------------------------------
	$report = "<h2>Checking active downloads...</h2>";

	$recordCount = 0;
	$errorCount = 0;
	$fixCount = 0;
	$errors = array();
	$errors[] = array('UID', 'Title', 'error');
	$recordCount++;

	$sql = "select * from p2p_peer";
	$result = $kapenta->db->query($sql);

	while ($row = $kapenta->db->fetchAssoc($result)) {
		$recordCount++;
		$row = $kapenta->db->rmArray($row);
		$peer = new P2P_Peer();
		$peer->loadArray($row);

		$downloads = new P2P_Downloads($peer->UID);
		if (true == $downloads->loaded) { 
			foreach($downloads->members as $fileName) {
				if (true == $kapenta->fs->exists($fileName)) {
					$downloads->remove($fileName);
					$downloads->save();
				} 

				if (true == $downloads->hasManifest($fileName)) {
					$klf = new KLargeFile($fileName);
					if (true == $klf->checkCompletion()) {
						$errors[] = array($row['UID'], $fileName, 'stuck download');
						$errorCount++;

						$check = ($klf->stitchTogether() && $klf->delete());
						if (true == $check) { $fixCount++; }
					}
				}
			}

		}

	}

	//---------------------------------------------------------------------------------------------
	//	compile report
	//---------------------------------------------------------------------------------------------

	if (count($errors) > 1) { $report .= $theme->arrayToHtmlTable($errors, true, true); }

	$report .= "<b>Records Checked:</b> $recordCount<br/>\n";
	$report .= "<b>Errors Found:</b> $errorCount<br/>\n";
	if ($errorCount > 0) {
		$report .= "<b>Errors Fixed:</b> $fixCount<br/>\n";
	}


	//---------------------------------------------------------------------------------------------
	//	check that all p2p_gifts refer to objects we actually have
	//---------------------------------------------------------------------------------------------
	$report = "<h2>Checking gifts table...</h2>";

	$recordCount = 0;
	$errorCount = 0;
	$fixCount = 0;
	$errors = array();
	$errors[] = array('UID', 'refModel', 'refUID', 'error');

	$dbSchema = $kapenta->db->getSchema('p2p_gift');

	$sql = "select * from p2p_gift";
	$result = $kapenta->db->query($sql);
	while ($row = $kapenta->db->fetchAssoc($result)) {
		$recordCount++;
		$item = $kapenta->db->rmArray($row);

		//------------------------------------------------------------------------------------------
		//	check references to files, delete offer if bad
		//------------------------------------------------------------------------------------------
		if ('file' == $item['type']) {
			if (false == $kapenta->fs->exists($item['fileName'])) { 
				$errors[] = array(
					$item['UID'],
					$item['refModel'],
					$item['refUID'],
					'Missing file: ' . $item['fileName']
				);
				$errorCount++;
				$fixCount++;

				$sql = "delete from p2p_gift where UID='" . $kapenta->db->addMarkup($item['UID']) . "'";
				$kapenta->db->query($sql);
			}
		}

		//------------------------------------------------------------------------------------------
		//	check references to objects, delete offer if bad
		//------------------------------------------------------------------------------------------
		if ('object' == $item['type']) {
			if (false == $kapenta->db->objectExists($item['refModel'], $item['refUID'])) { 
				$errors[] = array(
					$item['UID'],
					$item['refModel'],
					$item['refUID'],
					'Missing object: ' . $item['refModel'] . '::' . $item['refUID']
				);
				$errorCount++;
				$fixCount++;

				$sql = "delete from p2p_gift where UID='" . $kapenta->db->addMarkup($item['UID']) . "'";
				$kapenta->db->query($sql);
			}
		}

		//------------------------------------------------------------------------------------------
		//	check 'updated' field, delete if bad
		//------------------------------------------------------------------------------------------
		if (('' == $item['updated']) || ('0000-00-00 00:00:00' == $item['updated'])) {
			$errors[] = array(
				$item['UID'],
				$item['refModel'],
				$item['refUID'],
				'Missing update time: ' . $item['refModel'] . '::' . $item['refUID']
			);
			$errorCount++;
			$fixCount++;

			$sql = "delete from p2p_gift where UID='" . $kapenta->db->addMarkup($item['UID']) . "'";
			$kapenta->db->query($sql);
		}

	}

	//---------------------------------------------------------------------------------------------
	//	compile report
	//---------------------------------------------------------------------------------------------

	if (count($errors) > 1) { $report .= $theme->arrayToHtmlTable($errors, true, true); }

	$report .= "<b>Records Checked:</b> $recordCount<br/>\n";
	$report .= "<b>Errors Found:</b> $errorCount<br/>\n";
	if ($errorCount > 0) {
		$report .= "<b>Errors Fixed:</b> $fixCount<br/>\n";
	}

	return $report;
}

?>
