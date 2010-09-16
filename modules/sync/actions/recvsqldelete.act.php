<?

	require_once($kapenta->installPath . 'modules/sync/models/deleted.mod.php');

//--------------------------------------------------------------------------------------------------
//	recieve a SQL update from a peer
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	authorize
	//----------------------------------------------------------------------------------------------
	$kapenta->logSync("recieved object deletion request\n");
	if (false == $sync->authenticate()) { $page->doXmlError('could not authenticate'); }
	$kapenta->logSync("authenticated for table deletion\n");

	//----------------------------------------------------------------------------------------------
	//	get table and uid of record to delete
	//----------------------------------------------------------------------------------------------

	if (false == array_key_exists('detail', $_POST)) { $page->doXmlError('deletion notice not sent'); }

	$delTable = '';
	$delUid = '';
	$xe = new XmlEntity($_POST['detail']);
	foreach($xe->children as $child) {		//TODO: switch ()
		if ('table' == $child->type) { $delTable = $child->value; }
		if ('uid' == $child->type) { $delUid = $child->value; }
	}

	if ('' == $delTable) { $page->doXmlError('table not specified'); }
	if ('' == $delUid) { $page->doXmlError('uid not specified'); }

	//----------------------------------------------------------------------------------------------
	//	delete from database
	//----------------------------------------------------------------------------------------------

	$kapenta->logSync("checking record $delUid from table $delTable <br/>\n");
	if (false == $db->objectExists($delTable, $delUid)) { $page->doXmlError('no such record'); }
	$kapenta->logSync("deleting record $delUid from table $delTable <br/>\n");

	$sql = "delete from " . $db->addMarkup($delTable) . " "
		 . "where UID='" . $db->addMarkup($delUid) . "'";

	$db->query($sql);

	//----------------------------------------------------------------------------------------------
	//	add to deleted items and pass on to peers
	//----------------------------------------------------------------------------------------------
	$source = $syncHeaders['Sync-source'];
	$sync->recordDeletion($refTable, $refUID, $source);

	//---------------------------------------------------------------------------------------------
	//	done
	//---------------------------------------------------------------------------------------------	

	echo "<ok/>";

?>
