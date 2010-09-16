<?

	require_once($kapenta->installPath . 'modules/sync/models/notice.mod.php');
	require_once($kapenta->installPath . 'modules/sync/models/server.mod.php');

//-------------------------------------------------------------------------------------------------
//	pass a record on to a peer
//-------------------------------------------------------------------------------------------------

	if ('' == $req->ref) { $page->doXmlError('item not specified'); }
	if (false == $db->objectExists('Sync_Notice', $req->ref)) { $page->doXmlError('no such item'); }

	//---------------------------------------------------------------------------------------------
	//	load the sync item
	//---------------------------------------------------------------------------------------------
	$model = new Sync_Notice($req->ref);
	if (false == $model->loaded) { $page->doXmlError('no such item'); }
	$server = new Sync_Server($model->peer);

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
	
	$postVars = 'detail=' . urlencode($model->data);
	$result = syncCurlPost($postUrl, $server->password, $postVars);

	//---------------------------------------------------------------------------------------------
	//	
	//---------------------------------------------------------------------------------------------
	
	$kapenta->logSync('send: ' . $server->serverurl . " result: " . $result . "<br/>\n");
	$kapenta->logSync('send model data: ' . $model->data . "<br/>\n");
	$kapenta->logSync('send postvars: ' . $postVars . "<br/>\n");

	//---------------------------------------------------------------------------------------------
	//	check peer response
	//---------------------------------------------------------------------------------------------

	if (strpos(' ' . $result, '<ok/>') == false) {
		$kapenta->logSync("send failure:\n$result\n");
		//-----------------------------------------------------------------------------------------
		//	sync failed, retry
		//-----------------------------------------------------------------------------------------
		$model->timestamp = time();
		$model->status = 'failed';
		$model->save();

	} else {		
		//-----------------------------------------------------------------------------------------
		//	sync completed successfully, we can delete this record
		//-----------------------------------------------------------------------------------------
		$kapenta->logSync("send success:\n$result\n");
		$model->delete();
	}

?>
