<?

	require_once($kapenta->installPath . 'modules/aliases/models/alias.mod.php');

//--------------------------------------------------------------------------------------------------
//*	development / maintenance script to search for missing alias records
//--------------------------------------------------------------------------------------------------
//:	This script compares the aliases table with all objects which should have aliases

	if ('admin' != $user->role) { $kapenta->page->do403(); }

	$tables = $kapenta->db->listTables();
	echo $theme->expandBlocks('[[:theme::ifscrollheader:]]');

	foreach($tables as $tableName) {

		$rowCount = 0;
		$errorCount = 0;

		$dbSchema = $kapenta->db->getSchema($tableName);
		if (
			('aliases_alias' != $tableName) && 
			('twitter_tweet' != $tableName) && 
			('tmp_' !== substr($tableName, 0, 4)) &&
			(true == array_key_exists('alias', $dbSchema['fields']))
		) {

			echo "<div class='chatmessageblack'><h2>$tableName</h2></div>\n";

			$result = $kapenta->db->query("select * from " . $tableName);
			while($row = $kapenta->db->fetchAssoc($result)) {
				$rowCount++;
				$item = $kapenta->db->rmArray($row);
			
				if ('' == trim($item['alias'])) {

					$errorCount++;
					echo ''
					 . "<div class='chatmessagered'>"
					 . "Warning: $tableName::" . $item['UID'] . " has empty alias."
					 . "</div>\n";

				} else {

					$found = false;
					$all = $aliases->getAll($dbSchema['module'], $tableName, $item['UID']);

					foreach($all as $candidate) {
						if (strtolower($candidate) == strtolower($item['alias'])) { $found = true; }
					}

					if (true == $found) {

						// all good

					} else {

						$errorCount++;
						echo ''
						 . "<div class='chatmessagered'>"
						 . "Warning: $tableName::" . $item['UID'] . " has mismatched / missing aliases.  "
						 . $item['alias'] . " not found in {" . implode(', ', $all) . "} "
						 . "</div>\n";

						//--------------------------------------------------------------------------
						//	fix it if directed to
						//--------------------------------------------------------------------------

						if (
							(true == array_key_exists('action', $_POST)) &&
							(true == array_key_exists('table', $_POST)) &&
							('fixAliases' == $_POST['action']) && 
							($_POST['table'] == $tableName)
						) {

							echo "<div class='chatmessageblack'>Attempting fix...</div>\n";

							//----------------------------------------------------------------------
							//	when alias on object but none in table, add an alias record
							//----------------------------------------------------------------------

							if ((0 == count($all)) && ('' != trim($item['alias']))) {

								$model = new Aliases_Alias();
								$model->refModule = $dbSchema['module'];
								$model->refModel = $dbSchema['model'];
								$model->refUID = $item['UID'];
								$model->alias = $item['alias'];
								$model->aliaslc = strtolower($item['alias']);
								$report = $model->save();

								if ('' == $report) {

									echo ''
									 . "<div class='chatmessagegreen'>"
									 . "Created alias for " . $tableName . '::' . $item['UID']
									 . " (" . $model->alias . ")"
									 . "</div>";

								} else {

									echo ''
									 . "<div class='chatmessagered'>"
									 . "Could not create alias for "
									 . $tableName . '::' . $item['UID']
									 . " (" . $model->alias . ")"
									 . "</div>";

								}
							}

							//----------------------------------------------------------------------
							//	when alias on object does not match table, update the object
							//----------------------------------------------------------------------
	
						}

					}

					//echo $item['alias'] . "<br/>\n";
				}

			}

			echo "<div class='chatmessageblack'>Checked: $rowCount Errors: $errorCount</div>\n";

			if ($errorCount > 0) {
				echo ''
				 . "<div class='chatmessageblack'>\n"
				 . "<form name='frmFix" . $kapenta->createUID() . "' method='POST'>\n"
				 . "<input type='hidden' name='action' value='fixAliases' />\n"
				 . "<input type='hidden' name='table' value='$tableName' />\n"
				 . "<input type='submit' value='Try to fix these errors &gt;&gt;' />\n"
				 . "</form>\n"
				 . "</div>\n";
			}

		} else {
			//echo "<div class='chatmessageblack'>skipping $tableName (no aliased objects)</div>\n";
		}

	}

	echo $theme->expandBlocks('[[:theme::ifscrollfooter:]]');

?>
