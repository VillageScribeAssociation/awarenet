<?php

	require_once($kapenta->installPath . 'core/dbdriver/sqlite.dbd.php');
	require_once($kapenta->installPath . 'core/dbdriver/sqliteadmin.dbd.php');

	require_once($kapenta->installPath . 'core/dbdriver/dump.dbd.php');
	require_once($kapenta->installPath . 'core/dbdriver/dumpadmin.dbd.php');

	ini_set('memory_limit', '32M');			//	worth a try

//--------------------------------------------------------------------------------------------------
//*	export the current database as SQLLite
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	admins only
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $kapenta->page->do403(); }


	//----------------------------------------------------------------------------------------------
	//	show export page or dump database
	//----------------------------------------------------------------------------------------------

	if ((true == array_key_exists('action', $_POST)) &&	('export' == $_POST['action']) ) {
	
		//------------------------------------------------------------------------------------------
		//	set export options
		//------------------------------------------------------------------------------------------

		$kapenta->db->cacheSize = 1;
	
		$format = "txt";

		if (true == array_key_exists('format', $_POST)) { $format = $_POST['format']; }

		$fileName = ''
		 . $kapenta->installPath . 'data/export/'
		 . $kapenta->datetime() . '-' . basename($kapenta->db->name);

		$fileName = str_replace(':', '-', $fileName);
		$kapenta->fileMakeSubdirs($fileName);
	
		$exp = '';
		$expAdmin = '';

		switch($format) {
			case 'sq3':

				$exp = new KDBDriver_SQLite();
				$exp->name = $fileName;
				$expAdmin = new KDBAdminDriver_SQLite($exp);

				break;		//......................................................................

			case 'txt':
				$exp = new KDBDriver_Dump();
				$exp->name = $fileName;
				$expAdmin = new KDBAdminDriver_Dump($exp);

				break;		//......................................................................

			default:
				echo "Unknown driver: $format<br/>";
				die();
				break;		//......................................................................

		}

		//------------------------------------------------------------------------------------------
		//	dump the current database
		//------------------------------------------------------------------------------------------

		echo $theme->expandBlocks('[[:theme::ifscrollheader:]]', '');

		$tables = array();

		if (true == method_exists($db, 'listTables')) {
			$tables = $kapenta->db->listTables();
		} else {
			$tables = $kapenta->db->loadTables();
		}


		foreach($tables as $tableName) {
	
		if (
			('tmp_' == substr($tableName, 0, 4)) ||
			('p2p_gift' == $tableName) || 
			('live_trigger' == $tableName) ||
			('wiki_mwimport' == $tableName)
		) { continue; }

			//--------------------------------------------------------------------------------------
			//	create the table
			//--------------------------------------------------------------------------------------
			echo ''
			 . "<div class='chatmessageblack'>\n"
			 . "<h2>Creating table " . $tableName . "</h2>\n"
			 . "</div>\n";
			flush();

			$dbSchema = $kapenta->db->getSchema($tableName);
			$expAdmin->createTable($dbSchema);

			//--------------------------------------------------------------------------------------
			//	copy all objects to the name table
			//--------------------------------------------------------------------------------------
			$sql = "SELECT * FROM " . $tableName;
			$result = $kapenta->db->query($sql);

			if (true == method_exists($db, 'transactionStart')) { $kapenta->db->transactionStart(); }

			$rowCount = 100;

			echo "<div class='chatmessagegreen'>\n";

			while ($row = $kapenta->db->fetchAssoc($result)) {
				$item = $kapenta->db->rmArray($row);
				$exp->save($item, $dbSchema, false, false, false);
				echo ".";
				$rowCount--;
				if (0 == $rowCount) { $rowCount = 100; echo "<br/>\n"; flush(); }
			}

			echo "</div>\n";

			echo "<br/>\n";

			if (true == method_exists($db, 'transactionEnd')) { $kapenta->db->transactionEnd(); }

			echo $theme->expandBlocks('[[:theme::ifscrollfooter:]]', '');

		} // end foreach table

	} else {

		//------------------------------------------------------------------------------------------
		//	no action given, display export page
		//------------------------------------------------------------------------------------------

		$kapenta->page->load('modules/admin/actions/exportdb.page.php');
		$kapenta->page->render();

	}

?>
