<?

//--------------------------------------------------------------------------------------------------
//*	find references to tables with mixed case names and correct
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	admins only
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	echo "<html><body><h1>Fixing database table references " . $kapenta->db->datetime() . "</h1><small><pre>\n";

	$tables = array();
	$tables[] = 'Abuse_Report';
	$tables[] = 'Aliases_Alias';
	$tables[] = 'Announcements_Announcement';
	$tables[] = 'Badges_Badge';
	$tables[] = 'Badges_Userindex';
	$tables[] = 'Calendar_Entry';
	$tables[] = 'Chat_Discussion';
	$tables[] = 'Comments_Comment';
	$tables[] = 'Contact_Detail';
	$tables[] = 'Files_File';
	$tables[] = 'Files_Folder';
	$tables[] = 'Forums_Board';
	$tables[] = 'Forums_Reply';
	$tables[] = 'Forums_Thread';
	$tables[] = 'Gallery_Gallery';
	$tables[] = 'Groups_Group';
	$tables[] = 'Groups_Membership';
	$tables[] = 'Home_Static';
	$tables[] = 'Images_Image';
	$tables[] = 'Live_Chat';
	$tables[] = 'Live_Mailbox';
	$tables[] = 'Live_Trigger';
	$tables[] = 'Messages_Message';
	$tables[] = 'Moblog_Post';
	$tables[] = 'Notifications_Notification';
	$tables[] = 'Notifications_Userindex';
	$tables[] = 'Projects_Membership';
	$tables[] = 'Projects_Project';
	$tables[] = 'Projects_Revision';
	$tables[] = 'Revisions_Deleted';
	$tables[] = 'Revisions_Deleteditem';
	$tables[] = 'Revisions_Migrate';
	$tables[] = 'Revisions_Revision';
	$tables[] = 'Schools_School';
	$tables[] = 'Sync_Download';
	$tables[] = 'Sync_Message';
	$tables[] = 'Sync_Notice';
	$tables[] = 'Sync_Server';
	$tables[] = 'Sync_Server';
	$tables[] = 'Tags_Index';
	$tables[] = 'Tags_Tag';
	$tables[] = 'Users_Friendship';
	$tables[] = 'Users_Login';
	$tables[] = 'Users_Notification';
	$tables[] = 'Users_Role';
	$tables[] = 'Users_Session';
	$tables[] = 'Users_User';
	$tables[] = 'Videos_Gallery';
	$tables[] = 'Videos_Video';
	$tables[] = 'Wiki_Article';
	$tables[] = 'Wiki_Category';
	$tables[] = 'Wiki_Mwimport';
	$tables[] = 'Wiki_Revision';

	/*
	$tables = $kapenta->db->loadTables();
	foreach($tables as $table) {
		if (false != strpos($table, '_')) {
			$pieces = explode('_', $table);
			$mn = strtoupper(substr($pieces[0], 0, 1)) . substr($pieces[0], 1);
			$on = strtoupper(substr($pieces[1], 0, 1)) . substr($pieces[1], 1);

			echo "\t\$tables[] = '" . $mn . '_' . $on . "';\n";
		}
	}
	*/

	//----------------------------------------------------------------------------------------------
	//	get all table schemas
	//----------------------------------------------------------------------------------------------

	$extant = $kapenta->db->loadTables();

	foreach($extant as $tableName) {
		$schema = $kapenta->db->getSchema($tableName);
		foreach($schema['fields'] as $fieldName => $fieldType) {
			if (('refModel' == $fieldName) || ('refTable' == $fieldName)) {
				echo "table: $tableName field: $fieldName\n";
				
				//----------------------------------------------------------------------------------
				//	found table with open reference to other objects, looking for bad refs
				//----------------------------------------------------------------------------------
				$sql = "select UID, $fieldName from $tableName";
				echo $sql . "\n";

				$result = $kapenta->db->query($sql);
				while($row = $kapenta->db->fetchAssoc($result)) {
					if (($row[$fieldName] != strtolower($row[$fieldName])) && ('' != $tableName)) {
						$sql = "update $tableName "
							 . "set $fieldName='" . strtolower($row[$fieldName]) . "' "
							 . "where UID='" . $kapenta->db->addMarkup($row['UID']) . "';";
						echo $sql . "\n";
						$kapenta->db->query($sql);
						echo $row['UID'] . "--> " . $row[$fieldName] . " (fixed)\n"; flush();
					}
				}				

				echo "\n";
			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//	check all aliases
	//----------------------------------------------------------------------------------------------
	/*

	$dba = new KDBAdminDriver();

	$sql = "select * from aliases_alias";
	$result = $kapenta->db->query($sql);
	while ($row = $kapenta->db->fetchAssoc($result)) {

		if (false != strpos($row['refModel'], '_')) { $row['refModel'] = 'wiki_mwimport'; }

		$row = $kapenta->db->rmArray($row);
		if ('wiki_mwimport' == $row['refModel']) {
			echo "bad alias: "
				. $row['refModule'] . ' - '
				. $row['refModel'] . ' - '
				. $row['refUID'] . ' - '
				. $row['alias'] . "\n";

			$correct = $dba->findByUID($row['refUID']);
			if (false != $correct) {
				echo "correct refModel: $correct \n"; flush();
				$sql = "update aliases_alias "
					 . "set refModel='" . $kapenta->db->addMarkup($correct) . "' where UID='" . $kapenta->db->addMarkup($row['UID']) . "';";

				echo "$sql \n";
				$kapenta->db->query($sql);
			}

		}

		if ('new-image-' == substr($row['alias'], 0, 10)) {
			$correct = $dba->findByUID($row['refUID']);
			if (false == $correct) {
				echo "dud object: "
					. $row['refModule'] . ' - '
					. $row['refModel'] . ' - '
					. $row['refUID'] . ' - '
					. $row['alias'] . "\n";

				$sql = "delete from aliases_alias where UID='" . $row['UID'] . "';";
				$kapenta->db->query($sql);

			}
		}

	}

	*/

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------

	$changeCount = 0;	


	echo "change count: $changeCount \n";

	echo "</pre></small></body></html>\n";


?>
