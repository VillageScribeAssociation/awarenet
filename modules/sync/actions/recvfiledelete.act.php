<?

//-------------------------------------------------------------------------------------------------
//	recieve notice from peer that a file has been deleted
//-------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------
	//	authorize
	//---------------------------------------------------------------------------------------------

	if (syncAuthenticate() == false) { doXmlError('could not authenticate'); }

	//---------------------------------------------------------------------------------------------
	//	get fileName (relative URL) and delete it if it exists
	//---------------------------------------------------------------------------------------------

	if (array_key_exists('detail', $_POST) == false) { doXmlError('notice not sent'); }

	$fileName = $_POST['detail'];
	$fileName = str_replace("/.", 'XXXX', $fileName);	// prevent directory traversal

	// peers may only advise on deletion of files in /data/
	if (strpos(' ' . $fileName, 'data') == false) { doXmlError('permission denied'); }

	if (file_exists($installPath . $fileName) == true) { unlink($installPath . $fileName); }
	
	//---------------------------------------------------------------------------------------------
	//	add to deleted items
	//---------------------------------------------------------------------------------------------

	$model = new DeletedItem();
	$model->data['refTable'] = 'localfile';
	$model->data['refUID'] = $fileName;
	$model->data['timestamp'] = time();
	$model->save();

	//---------------------------------------------------------------------------------------------
	//	pass on to peers
	//---------------------------------------------------------------------------------------------	

	$syncHeaders = syncGetHeaders();
	$source = $syncHeaders['Sync-source'];
	syncBroadcastDbDelete($source, $delTable, $delUid)

	//---------------------------------------------------------------------------------------------
	//	done
	//---------------------------------------------------------------------------------------------	

	echo "<ok/>";

?>
