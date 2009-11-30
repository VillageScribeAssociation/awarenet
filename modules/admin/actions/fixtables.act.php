<?

//-------------------------------------------------------------------------------------------------
//	temporary script to ensure all tables which should have editedOn and editedBy fields do
//-------------------------------------------------------------------------------------------------

	if ($user->data['ofGroup'] != 'admin') { do403(); }

	//---------------------------------------------------------------------------------------------
	//	specify which tables not to add fields to
	//---------------------------------------------------------------------------------------------

	$html = '';
	$exempt = array('changes', 'chat', 'migrated', 'pagechannels', 'pageclients', 'sync', 'servers', 'static');

	//---------------------------------------------------------------------------------------------
	//	list tables, update and print them
	//---------------------------------------------------------------------------------------------

	$tables = dbListTables();
	foreach($tables as $tableName) {
		$dbSchema = dbGetSchema($tableName);

		if (($dbSchema == false) || (in_array($tableName, $exempt))) { 
			$html .= "<h2>$tableName (not described)</h2>\n"; 

		} else {
			$html .= dbSchemaToHtml($dbSchema);
			
			if (array_key_exists('editedOn', $dbSchema['fields']) == false) {
				$html .= "<b>This table does not have an editedOn field.</b><br/>\n";
				$sql = 'ALTER TABLE ' . $dbSchema['table'] . ' ADD editedOn DATETIME AFTER ' . getAfterField($dbSchema);
				$html .= "sql: $sql <br/>\n";
				dbQuery($sql);

			}

			if (array_key_exists('editedBy', $dbSchema['fields']) == false) {
				$html .= "<b>This table does not have an editedBy field.</b><br/>\n";
				$sql = 'ALTER TABLE ' . $dbSchema['table'] . ' ADD editedBy VARCHAR(30) AFTER ' . getAfterField($dbSchema);
				$html .= "sql: $sql <br/>\n";
				dbQuery($sql);
			}

		}
	}

	//---------------------------------------------------------------------------------------------
	//	find unfilled fields
	//---------------------------------------------------------------------------------------------

	$tables = dbListTables();
	foreach($tables as $tableName) {
		$dbSchema = dbGetSchema($tableName);

		if (($dbSchema == false) || (in_array($tableName, $exempt))) { 
			$html .= "<h2>$tableName (not described)</h2>\n"; 

		} else {
	
			$sql = "select * from " . $tableName;
			$result = dbQuery($sql);
			while ($row = dbFetchAssoc($result)) {
				foreach($dbSchema['fields'] as $fieldName => $type) {
					if (($fieldName == 'editedBy') && ($row['editedBy'] == '')) {
						$sql = "update $tableName "
							 . "set editedBy='" . $user->data['UID'] . "' "
							 . "where UID='" . $row['UID'] . "'";
						dbQuery($sql);
						$html .= "updated $tableName -> " . $row['UID'] . " -> editedBy<br/> \n";
					}

					if (($fieldName == 'editedOn') && ($row['editedOn'] == '')) {
						$sql = "update $tableName "
							 . "set editedOn='" . mysql_datetime() . "' "
							 . "where UID='" . $row['UID'] . "'";
						dbQuery($sql);
						$html .= "updated $tableName -> " . $row['UID']. " -> editedOn<br/> \n";
					}
				}			
			}

		}
	}

	//---------------------------------------------------------------------------------------------
	//	render the page
	//---------------------------------------------------------------------------------------------

	$page->load($installPath . 'modules/admin/actions/fixtables.page.php');
	$page->blockArgs['tablesdisplay'] = $html;
	//$page->data['template'] = str_replace('%%tablesdisplay%%', $html, $page->data['template']);
	$page->render();

	function getAfterField($dbSchema) {
		// before recordalias, so after the field before recordalias
		$after = 'UID';
		foreach($dbSchema['fields'] as $field => $type) {
			if ($field == 'recordAlias') { return $after; }
			$after = $field;
		}
		return $after;
	}

?>
