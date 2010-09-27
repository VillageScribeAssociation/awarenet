<?

//--------------------------------------------------------------------------------------------------
//	all tables to K3 layout migrate tables
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	auth
	//----------------------------------------------------------------------------------------------
	//TODO: this	

	//----------------------------------------------------------------------------------------------
	//	map of old tables names to new ones
	//----------------------------------------------------------------------------------------------

	$tables = array(
		'announcements' => 'Announcements_Announcement',
		'calendar' => 'Calendar_Entry',
		'chat' => 'Chat_Discussion',
		'changes' => 'Revisions_Revision',
		'comments' => 'Comments_Comment',
		'delitems' => 'Revisions_DeletedItem',
		'downloads' => 'Sync_Download',
		'files' => 'Files_File',
		'folders' => 'Files_Folder',
		'forumreplies' => 'Forums_Reply',
		'forums' => 'Forums_Board',
		'forumthreads' => 'Forums_Thread',
		'friendships' => 'Users_Friendship',
		'gallery' => 'Gallery_Gallery',
		'Groups_Membership' => 'Groups_Membership',
		'groups' => 'Groups_Group',
		'images' => 'Images_Image',
		'messages' => 'Messages_Message',
		'migrated' => 'Revisions_Migrate',
		'moblog' => 'Moblog_Post',
		'notices' => 'Users_Notification',
		'projectmembers' => 'Projects_Membership',
		'projectrevisions' => 'Projects_Revision',
		'projects' => 'Projects_Project',
		'recordalias' => 'Aliases_Alias',
		'schools' => 'Schools_School',
		'servers' => 'Sync_Server',
		'static' => 'Home_Static',
		'sync' => 'Sync_Message',
		'userlogin' => 'Users_Session',
		'users' => 'Users_User',
		'wiki' => 'Wiki_Article',
		'wikirevisions' => 'Wiki_Revision'
	);

	//----------------------------------------------------------------------------------------------
	//	the following object types are exported
	//----------------------------------------------------------------------------------------------

	$export = array(
		'announcements' => 'Announcements_Announcement',
		'changes' => 'Revisions_Revision',
		'comments' => 'Comments_Comment',
		'delitems' => 'Revisions_DeletedItem',
		'files' => 'Files_File',
		'friendships' => 'Users_Friendship',
		'images' => 'Images_Image',
		'messages' => 'Messages_Message',
		'migrated' => 'Revisions_Migrate',		
		'notices' => 'Users_Notification',
		'recordalias' => 'Aliases_Alias'
	);

	//----------------------------------------------------------------------------------------------
	//	these fields are renamed
	//----------------------------------------------------------------------------------------------
	
	$rename = array(
		'role' => 'role',
		'refTable' => 'refModel',
		'alias' => 'alias'
	);

	//----------------------------------------------------------------------------------------------
	//	create new tables
	//----------------------------------------------------------------------------------------------

	foreach($tables as $oldName => $newName) {
		$oldSchema = dbGetSchema($oldName);			//	original table schema
		$newSchema = dbGetSchema($oldName);			//	copy on which new table is based
		
		//------------------------------------------------------------------------------------------
		//	construct new table schema
		//------------------------------------------------------------------------------------------

		$newSchema['model'] = $newName;
		$newFields = array();
		$newFields['UID'] = 'VARCHAR(33)';			//	Moving to GUID (32 chars bchex)

		if (true == in_array($newName, $export)) {
			$newFields['refModule'] = 'VARCHAR(20)';	//	20 char limit on module name?
			$newFields['refModel'] = 'VARCHAR(50)';		//	50 char limit on module name?
		}

		foreach($newSchema['fields'] as $fName => $fType) {
			$add = true;
			switch (strtolower($fName)) {
				case 'UID':			$add = false;	break;
				case 'refTable':	$add = false;	break;
				case 'refModule':	$add = false;	break;
				case 'refUID':		$add = false;	break;
				case 'createdBy':	$add = false;	break;
				case 'createdOn':	$add = false;	break;
				case 'editedBy':	$add = false;	break;
				case 'editedOn':	$add = false;	break;
				case 'alias':	$add = false;	break;
				case 'alias':		$add = false;	break;
			}
			if (true == $add) { 
				if (strtolower($fType) == 'varchar(30)') { $fType = 'VARCHAR(33)'; }
				if (strtolower($fType) == 'text') { $fType = 'MEDIUMTEXT'; }
				foreach($rename as $oldName => $newName) {
					if ($fName == $oldName) { $fName = $newName; }
				}
				$newFields[$fName] = $fType;
			}
		} // end foreach field

		$newFields['createdOn'] = 'DATETIME';		//	
		$newFields['createdBy'] = 'VARCHAR(33)';	//	change UID size
		$newFields['editedOn'] = 'DATETIME';		//	
		$newFields['editedBy'] = 'VARCHAR(33)';		//	change UID size

		if (true == array_key_exists('alias', $oldSchema['fields'])) {
			$newFields['alias'] = 'VARCHAR(100)';	//	maximum size of an alias
		}

		if (true == array_key_exists('alias', $oldSchema['fields'])) {
			$newFields['alias'] = 'VARCHAR(100)';	//	maximum size of an alias
		}

		$newSchema['fields'] = $newFields;

		//------------------------------------------------------------------------------------------
		//	create the new table
		//------------------------------------------------------------------------------------------

		if (true == $db->tableExists($newSchema['model'])) {
			$sql = "drop table " . $newSchema['model'];
			$db->query($sql);
		}

		$sql = dbCreateTableSql($newSchema);
		$db->query($sql);
		echo "<h2>Creating Table {$newSchema['model']}</h2>\n";
		echo "</b>" . $sql . "</b><br/><br/>\n";
		
		//------------------------------------------------------------------------------------------
		//	copy all records from the old table
		//------------------------------------------------------------------------------------------
		$sql = "select * from " . $oldSchema['model'];
		$result = $db->query($sql);
		while ($row = $db->fetchAssoc($result)) {
			$row = $db->rmArray($row);
			
			// make blank record of new type
			$newRecord = array();
			foreach($newSchema['fields'] as $fName => $fType) {	$newRecord[$fName] = ''; }

			// fill new record from row
			foreach($row as $fieldName => $fieldValue) {
				foreach($rename as $oldName => $newName) 
					{ if ($fieldName == $oldName) { $fieldName = $newName; } }

				if (array_key_exists($fieldName, $newRecord)) 
					{ $newRecord[$fieldName] = $fieldValue;	}

			}

			// save it
			if ('Sync' != substr($newSchema['model'], 0, 4)) { 
				echo "<small>Adding record {$newRecord['UID']} to {$newSchema['model']}<br/></small>\n";
				flush();
				$db->save($newRecord, $newSchema, true); 
			}
			

		} // end while

	} // end foreach $tables

?>
