<?

	require_once($kapenta->installPath . 'modules/sync/models/notice.mod.php');
	require_once($kapenta->installPath . 'modules/sync/models/server.mod.php');

//-------------------------------------------------------------------------------------------------
//*	pass a new/changed object on to a peer
//-------------------------------------------------------------------------------------------------

	$kapenta->logSync("**** /SYNC/SEND/ ****.<br/>\n");	

	if ('' == $req->ref) {
		$kapenta->logSync("/SYNC/SEND/ **** item not specified.<br/>\n");	
		$page->doXmlError('item not specified'); 
	}
	if (false == $db->objectExists('sync_notice', $req->ref)) {
		$kapenta->logSync("/SYNC/SEND/ **** no such item.<br/>\n");	
		$page->doXmlError('no such item');
	}

	//---------------------------------------------------------------------------------------------
	//	load the sync item
	//---------------------------------------------------------------------------------------------
	$model = new Sync_Notice($req->ref);

	if (false == $model->loaded) {
		$kapenta->logSync("No such sync notice: " . htmlentities($req->ref) . "<br/>\n");
		$page->doXmlError('no such item');
	}

	if ($model->failures >= 3) { $page->doXmlError('failed three times'); }

	if (strpos($model->ndata, '<table></table>') != false) { 
		$kapenta->logSync("Bad data - table not specified.<br/>\n");
		$page->doXmlError('invalid update');
	}

	$server = new Sync_Server($model->peer);
	if (false == $server->loaded) {
		$kapenta->logSync("No such sync server: " . htmlentities($model->peer) . "<br/>\n");
		$page->doXmlError('no such server');
	}

	$postUrl = '';
	switch ($model->type) {
		case 'dbUpdate': 	 $postUrl = $server->serverurl . 'sync/recvsql/';		 break;
		case 'dbDelete': 	 $postUrl = $server->serverurl . 'sync/recvsqldelete/';	 break;
		case 'fileCreate': 	 $postUrl = $server->serverurl . 'sync/recvfilecreate/'; break;
		case 'fileDelete': 	 $postUrl = $server->serverurl . 'sync/recvfiledelete/'; break;
		case 'notification': $postUrl = $server->serverurl . 'sync/recvnotice/';	 break;
	}

	$model->status = 'locked';
	$model->timestamp = time();
	$model->save();

	//---------------------------------------------------------------------------------------------
	//	post it
	//---------------------------------------------------------------------------------------------
	
	$postVars = 'detail=' . urlencode($model->ndata);
	$result = $sync->curlPost($postUrl, $server->password, $postVars);

	//---------------------------------------------------------------------------------------------
	//	
	//---------------------------------------------------------------------------------------------
	
	$kapenta->logSync('send: ' . $postUrl . " result: " . $result . "<br/>\n");
	$kapenta->logSync('send model data: ' . $model->ndata . "<br/>\n");
	$kapenta->logSync('send postvars: ' . $postVars . "<br/>\n");

	//---------------------------------------------------------------------------------------------
	//	check peer response
	//---------------------------------------------------------------------------------------------

	if (false == strpos(' ' . $result, '<ok/>')) {
		$kapenta->logSync("send failure:\n$result\n");
		//-----------------------------------------------------------------------------------------
		//	sync failed, retry
		//-----------------------------------------------------------------------------------------
		$model->timestamp = time();
		$model->status = 'failed';
		$model->failures = $model->failures++;
		$model->save();

	} else {		
		//-----------------------------------------------------------------------------------------
		//	sync completed successfully, we can delete this Sync_Notice
		//-----------------------------------------------------------------------------------------
		$kapenta->logSync("send success:\n$result\n");
		$check = $model->delete();
		if (false == $check) { $kapenta->logSync("Could not delete Notice: ". $model->UID ."\n"); }
		else { $kapenta->logSync("Deleted notice: ". $model->UID ." (send complete)\n"); }
	}

?>
