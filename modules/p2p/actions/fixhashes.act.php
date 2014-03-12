<?php

//-------------------------------------------------------------------------------------------------
//*	go through database and look for missing file hashes
//-------------------------------------------------------------------------------------------------

	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }

	$tables = $kapenta->db->listTables();

	echo $theme->expandBlocks('[[:theme::ifscrollheader:]]');

	//---------------------------------------------------------------------------------------------
	//	go through each table which has 'hash' and 'fileName' fields
	//---------------------------------------------------------------------------------------------

	foreach($tables as $table) {
		$dbSchema = $kapenta->db->getSchema($table);
		if (
			(true == array_key_exists('hash', $dbSchema['fields'])) &&		//	has hash field
			(true == array_key_exists('fileName', $dbSchema['fields'])) &&	//	has fileName field
			('tmp_' !== substr($table, 0, 4))								//	not a temp table
		) {

			echo "<div class='chatmessageblack'>\n<h2>Checking: $table</h2>\n</div>";
			flush();

			$sql = "select * from " . $table . " where hash='';";
			$result = $kapenta->db->query($sql);
			while ($row = $kapenta->db->fetchAssoc($result)) {
				$item = $kapenta->db->rmArray($row);
				$hasFile = $kapenta->fs->exists($item['fileName']);

				if (true == $hasFile) {

					$hash = $kapenta->fileSha1($item['fileName']);

					echo ''
					 . "<div class='chatmessagered'>\n"
					 . "[i] Missing hash: " . $table ."::" . $item['UID'] . "<br/>\n"
					 . "[*] corrected: $hash <br/>\n"
					 . "</div>\n";
					flush();

				}
			}
		}
	}

	//---------------------------------------------------------------------------------------------
	//	done
	//---------------------------------------------------------------------------------------------

	echo $theme->expandBlocks('[[:theme::ifscrollheader:]]');
?>
