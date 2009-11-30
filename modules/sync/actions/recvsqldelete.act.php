<?

//-------------------------------------------------------------------------------------------------
//	recieve a SQL update from a peer
//-------------------------------------------------------------------------------------------------

	require_once($installPath . 'modules/sync/models/deleted.mod.php');

	//---------------------------------------------------------------------------------------------
	//	authorize
	//---------------------------------------------------------------------------------------------

	if (syncAuthenticate() == false) { doXmlError('could not authenticate'); }
	logSync("authenticated for table deletion\n");

	//---------------------------------------------------------------------------------------------
	//	get table and uid of record to delete
	//---------------------------------------------------------------------------------------------

	if (array_key_exists('detail', $_POST) == false) { doXmlError('deletion notice not sent'); }

	$delTable = '';
	$delUid = '';
	$xe = new XmlEntity($_POST['detail']);
	foreach($xe->children as $child) {
		if ($child->type == 'table') { $delTable = $child->value; }
		if ($child->type == 'uid') { $delUid = $child->value; }
	}

	if ($delTable == '') { doXmlError('table not specified'); }
	if ($delUid == '') { doXmlError('uid not specified'); }

	//---------------------------------------------------------------------------------------------
	//	delete from database
	//---------------------------------------------------------------------------------------------

	logSync("checking record $delUid from table $delTable <br/>\n");
	if (dbRecordExists($delTable, $delUid) == false) { doXmlError('no such record'); }
	logSync("deleting record $delUid from table $delTable <br/>\n");
	$sql = "delete from " . sqlMarkup($delTable) . " where UID='" . sqlMarkup($delUid) . "'";
	dbQuery($sql);

	//---------------------------------------------------------------------------------------------
	//	add to deleted items
	//---------------------------------------------------------------------------------------------

	$model = new DeletedItem();
	$model->data['refTable'] = $delTable;
	$model->data['refUID'] = $delUID;
	$model->data['timestamp'] = time();
	$model->save();

	//---------------------------------------------------------------------------------------------
	//	pass on to peers
	//---------------------------------------------------------------------------------------------	

	$syncHeaders = syncGetHeaders();
	$source = $syncHeaders['Sync-source'];
	syncBroadcastDbDelete($source, $delTable, $delUid);

	//---------------------------------------------------------------------------------------------
	//	done
	//---------------------------------------------------------------------------------------------	

	echo "<ok/>";

?>
