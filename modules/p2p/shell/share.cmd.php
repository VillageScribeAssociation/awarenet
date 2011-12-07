<?

	require_once($kapenta->installPath . 'modules/live/inc/cmdaliases.class.php');
	require_once($kapenta->installPath . 'modules/p2p/models/downloads.set.php');
	require_once($kapenta->installPath . 'modules/p2p/models/offers.set.php');

//--------------------------------------------------------------------------------------------------
//|	web shell command to display and modify the gifts table (file and object shares)
//--------------------------------------------------------------------------------------------------

function p2p_WebShell_share($args) {
	global $kapenta;
	global $user;
	global $shell;
	global $db;
	global $theme;

	$mode = 'show';			//%	operation [string]
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists(0, $args)) {
		// show help?
	} else {
		switch($args[0]) {
			case '-h':		$mode = 'help';		break;
			case '-r':		$mode = 'reset';	break;
			case '--list':	$mode = 'list';		break;
			case '--help':	$mode = 'help';		break;
			case '--reset':	$mode = 'reset';	break;
		}
	}

	if ('admin' != $user->role) { $mode = 'noauth'; }

	//----------------------------------------------------------------------------------------------
	//	execute
	//----------------------------------------------------------------------------------------------
	
	switch($mode) {

		case 'show':
			//--------------------------------------------------------------------------------------
			//	show share status for a single database object and any file it maintains
			//--------------------------------------------------------------------------------------
			if (false == array_key_exists(1, $args)) { return "Table name not given.<br/>"; }
			if (false == array_key_exists(2, $args)) { return "UID not given.<br/>"; }
			$tableName = $args[1];
			$UID = $args[2];
			
			if (false == $db->tableExists($tableName)) { return 'Unknown table.<br/>'; }
			if (false == $db->objectExists($tableName, $UID)) { return 'Object not found.<br/>'; }

			$dbSchema = $db->getSchema($tableName);
			$isShared = $db->isShared($tableName, $UID);
			if (true == $isShared) { $html .= "Database reports that object is shared.<br/>"; }
			else { $html .= "Database reports that object is NOT SHARED.<br/>"; }

			if (true == array_key_exists('fileName', $dbSchema['fields'])) {
				$objAry = $db->load($UID, $dbSchema);
				$html .= "Has fileName: " . $objAry['fileName'] . "<br/>";
			}

			$peers = $db->loadRange('p2p_peer', '*');

			$table = array();
			$table[] = array('Peer', 'Gift', 'Type', 'Status', 'File');
			foreach($peers as $peer) {
				$conditions = array();
				$conditions[] = "refModel='" . $db->addMarkup($tableName) . "'";
				$conditions[] = "refUID='" . $db->addMarkup($UID) . "'";
				$conditions[] = "peer='" . $db->addMarkup($peer['UID']) . "'";
				$range = $db->loadRange('p2p_gift', '*', $conditions);

				foreach ($range as $item) {
					$table[] = array(
						$peer['name'],
						$item['UID'],
						$item['type'], 
						$item['status'],
						$item['fileName']
					);
				}
			}

			$html .= $theme->arrayToHtmlTable($table, true, true, true);

			break;	//..............................................................................

		case 'help':
			//--------------------------------------------------------------------------------------
			//	display the man page for this command
			//--------------------------------------------------------------------------------------
			$html = p2p_WebShell_downloads_help();
			break;	//..............................................................................

		case 'reset':
			//--------------------------------------------------------------------------------------
			//	re-share this item/table with all peers
			//--------------------------------------------------------------------------------------
			if (false == array_key_exists(1, $args)) { return "Table name not given.<br/>"; }
			if (false == array_key_exists(2, $args)) { $args[2] = ''; }

			$tableName = $args[1];						//%	table to reset from [string]
			$UID = $args[2];							//% single object to reset [string]

			if (false == $db->tableExists($tableName)) { return 'Unknown table.<br/>'; }

			if (('' != $UID) && (false == $db->objectExists($tableName, $UID))) { 
				return "Object not found.";
			}

			$peers = $db->loadRange('p2p_peer', '*');		//%	array serialized peers [array]
			$dbSchema = $db->getSchema($tableName);			//%	table schema [array]

			if ('' == $UID) {
				$html .= "Resetting share status of objects in table: $tableName <br/>";
			} else {
				$html .= "Resetting share status of single object: $tableName :: $UID <br/>";
			}

			//--------------------------------------------------------------------------------------
			//	load object or table from database
			//--------------------------------------------------------------------------------------
			$sql = "SELECT * FROM " . $tableName;
			if ('' != $UID) { $sql .= " WHERE UID='" . $db->addMarkup($UID) . "'"; }
			$result = $db->query($sql);

			while ($row = $db->fetchAssoc($result)) {
				$item = $db->rmArray($row);
				$isShared = $db->isShared($tableName, $item['UID']);	//%	share status [bool]
				$hasFile = array_key_exists('fileName', $dbSchema['fields']);	//% [bool]

				if (true == $isShared) { $html .= "Database reports that object is shared.<br/>"; }
				else { $html .= "Database reports that object is NOT SHARED.<br/>"; }

				if (true == $hasFile) { $html .= "Object may have an attached file.<br/>"; }

				if (true == $isShared) {
					//------------------------------------------------------------------------------
					// item is shared, (re)add for all peers
					//------------------------------------------------------------------------------
					$set = new P2P_Offers();
					foreach($peers as $peer) {
						$html .= "Sharing $tableName :: $UID  with " . $peer['name'] . "<br/>\n";
						$set->peerUID = $peer['UID'];
						$upd = $set->updateObject($tableName, $item['UID'], $item);
						if (true == $upd) {
							$html .= ''
							 . "Updated object share $tableName " . $row['UID'] . " "
							 . "on " . $peer['name'] . ".<br/>";
						}


						if (true == $hasFile) {
							$upd = $set->updateFile($tableName, $item['UID'], $item['fileName']);
							if (true == $upd) {
								$html .= ''
								 . "Updated file share " . $row['fileName']
								 . "( " . $tableName . " " . $row['UID'] . ")"
								 . " on " . $peer['name'] . ".<br/>";
							}
						}

					}

				} else {
					//------------------------------------------------------------------------------
					// item is not shared, delete any gift records
					//------------------------------------------------------------------------------
					$conditions = array();
					$conditions[] = "refModel='" . $db->addMarkup($tableName). "'";
					$conditions[] = "refUID='" . $db->addMarkup($UID). "'";
					$range = $db->loadRange('p2p_gift', '*', $conditions);
					
				}
			}

			break;	//..............................................................................

		case 'noauth':
			$html = "Admin permissions required to perform this operation.";
			break;	//..............................................................................

	}

	return $html;
}

//--------------------------------------------------------------------------------------------------
//|	manpage for the admin.bash command
//--------------------------------------------------------------------------------------------------
//opt: short - display command summary [bool]

function p2p_WebShell_share_help($short = false) {
	if (true == $short) { return "Execute a command at the OS shell."; }

	$html = "
	<b>usage: p2p.share [-h|-s|-r]</b><br/>
	Display or reset the share status of a database object.<br/>
	<br/>
	<b>[--help|-h]</b><br/>
	Displays this manpage.<br/>
	<br/>
	<b>[--show|-s] <i>model_name 123UID456</i></b><br/>
	Displays share status of an object.<br/>
	<br/>
	<b>[--reset|-r] <i>model_name [123UID456]</i></b><br/>
	Displays this manpage.<br/>
	<br/>
	";

	return $html;
}


?>
