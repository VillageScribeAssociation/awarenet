<?

//-------------------------------------------------------------------------------------------------
//*	recieve notice from peer that a file has been deleted
//-------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------
	//	authorize
	//---------------------------------------------------------------------------------------------

	if (false == $sync->authenticate()) { $page->doXmlError('could not authenticate'); }

	//---------------------------------------------------------------------------------------------
	//	get fileName (relative URL) and delete it if it exists
	//---------------------------------------------------------------------------------------------

	if (false == array_key_exists('detail', $_POST)) { $page->doXmlError('notice not sent'); }

	$fileName = $_POST['detail'];
	$fileName = str_replace("/.", 'XXXX', $fileName);	// prevent directory traversal

	// peers may only advise on deletion of files in /data/
	if (false == strpos(' ' . $fileName, 'data')) { $page->doXmlError('permission denied'); }

	if (true == file_exists($kapenta->installPath . $fileName)) { 
		unlink($kapenta->installPath . $fileName); 
	}
	
	//---------------------------------------------------------------------------------------------
	//	add to deleted items
	//---------------------------------------------------------------------------------------------

	$model = new DeletedItem();
	$model->refTable = 'localfile';
	$model->refUID = $fileName;
	$model->timestamp = time();
	$model->save();

	//---------------------------------------------------------------------------------------------
	//	pass on to peers
	//---------------------------------------------------------------------------------------------	

	$syncHeaders = syncGetHeaders();
	$source = $syncHeaders['Sync-source'];
	$sync->broadcastDbDelete($source, $delTable, $delUid)

	//---------------------------------------------------------------------------------------------
	//	done
	//---------------------------------------------------------------------------------------------	

	echo "<ok/>";

?>
