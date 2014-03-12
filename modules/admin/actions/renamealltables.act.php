<?

//--------------------------------------------------------------------------------------------------
//*	upgrade script to rename database tables to lowercase (windows compatable)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	admins only
	//----------------------------------------------------------------------------------------------
	//if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	do it
	//----------------------------------------------------------------------------------------------
	$tables = $kapenta->db->loadTables();

	foreach($tables as $table) {
		$sql = "RENAME TABLE $table TO " . strtolower($table);
		echo $sql . "<br/>\n";
		$kapenta->db->query($sql);

		//if ('tmp_' == substr($table, 0, 4)) {
		//	$sql = "drop table $table;";
		//	echo $sql . "<br/>\n";
		//}

		if (false != strpos($table, '_')) {
			$parts = explode('_', $table);
			$parts[0] = strtoupper(substr($parts[0], 0, 1)) . substr($parts[0], 1);
			$parts[1] = strtoupper(substr($parts[1], 0, 1)) . substr($parts[1], 1);
			$oldName = $parts[0] . '_' . $parts[1];
			echo "'$oldName'|'$table'<br/>\n";
		}
	}

?>
