<?php

	require_once('../../../shinit.php');
	require_once($kapenta->installPath . 'core/dbdriver/sqlite.dbd.php');
	require_once($kapenta->installPath . 'core/dbdriver/sqliteadmin.dbd.php');
	require_once($kapenta->installPath . 'core/dbdriver/dump.dbd.php');
	require_once($kapenta->installPath . 'core/dbdriver/dumpadmin.dbd.php');

//--------------------------------------------------------------------------------------------------
//*	administrative shell action to export the database
//--------------------------------------------------------------------------------------------------

	$format = '';

	if ((is_array($argv)) && (1 == count($argv))) {
		echo "Please specify 'txt' or 'sq3' export format.\n";
		die();
	}

	if ('txt' == $argv[1]) { $format = 'txt'; }
	if ('sq3' == $argv[1]) { $format = 'sq3'; }

	if ('' == $format) {
		echo "Please specify 'txt' or 'sq3' export format.\n";
		die();
	}

	echo "Exporting database: " . $format . "\n";
	echo "Soure database type: " . $db->type . "\n";

	//----------------------------------------------------------------------------------------------
	//	set export options
	//----------------------------------------------------------------------------------------------
	
	$fileName = ''
	 . $kapenta->installPath . 'data/export/'
	 . $kapenta->datetime() . '-' . basename($db->name);

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
				echo "Unknown driver: $format\n";
				die();
				break;		//......................................................................

	}

	echo "File: $fileName \n";

	//----------------------------------------------------------------------------------------------
	//	dump the current database
	//----------------------------------------------------------------------------------------------

	$tables = $db->listTables();
	print_r($tables);

	foreach($tables as $tableName) {

		if (
			('tmp_' == substr($tableName, 0, 4)) ||
			('p2p_gift' == $tableName) || 
			('live_trigger' == $tableName) ||
			('wiki_mwimport' == $tableName)
		) { continue; }

		//------------------------------------------------------------------------------------------
		//	create the table
		//------------------------------------------------------------------------------------------
		echo "Creating table " . $tableName . "\n\n";

		$dbSchema = $db->getSchema($tableName);
		$expAdmin->createTable($dbSchema);

		//------------------------------------------------------------------------------------------
		//	copy all objects to the name table
		//------------------------------------------------------------------------------------------
		$sql = "SELECT * FROM " . $tableName;
		$result = $db->query($sql);

		$exp->transactionStart();
		$rowCount = 80;

		while ($row = $db->fetchAssoc($result)) {
			$item = $db->rmArray($row);
			$exp->save($item, $dbSchema, false, false, false);
			echo ".";
			$rowCount--;
			if (0 == $rowCount) { $rowCount = 80; echo "\n"; flush(); }
		}

		echo "\n";

		$exp->transactionEnd();

	} // end foreach table


?>
