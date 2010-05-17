<?

//-------------------------------------------------------------------------------------------------
//	pass a record on to a peer
//-------------------------------------------------------------------------------------------------

	require_once($installPath . 'modules/sync/models/sync.mod.php');
	require_once($installPath . 'modules/sync/models/server.mod.php');

	if ($request['ref'] == '') { doXmlError('item not specified'); }
	if (dbRecordExists('sync', $request['ref']) == false) { doXmlError('no such item'); }

	//---------------------------------------------------------------------------------------------
	//	load the sync item
	//---------------------------------------------------------------------------------------------

	$model = new Sync($request['ref']);
	$server = new Server($model->data['peer']);

	$postUrl = '';
	switch ($model->data['type']) {
		case 'dbUpdate': 	 $postUrl = $server->data['serverurl'] . 'sync/recvsql/';		 break;
		case 'dbDelete': 	 $postUrl = $server->data['serverurl'] . 'sync/recvsqldelete/';	 break;
		case 'fileCreate': 	 $postUrl = $server->data['serverurl'] . 'sync/recvfilecreate/'; break;
		case 'fileDelete': 	 $postUrl = $server->data['serverurl'] . 'sync/recvfiledelete/'; break;
		case 'notification': $postUrl = $server->data['serverurl'] . 'sync/recvnotice/';	 break;
	}

	$model->data['status'] = 'locked';
	$model->data['timestamp'] = time();
	$model->save();

	//---------------------------------------------------------------------------------------------
	//	post it
	//---------------------------------------------------------------------------------------------
	
	$postVars = 'detail=' . urlencode($model->data['data']);
	$result = syncCurlPost($postUrl, $server->data['password'], $postVars);

	//---------------------------------------------------------------------------------------------
	//	
	//---------------------------------------------------------------------------------------------
	
	logSync('send: ' . $server->data['serverurl'] . " result: " . $result . "<br/>\n");
	logSync('send model data: ' . $model->data['data'] . "<br/>\n");
	logSync('send postvars: ' . $postVars . "<br/>\n");

	//---------------------------------------------------------------------------------------------
	//	check peer response
	//---------------------------------------------------------------------------------------------

	if (strpos(' ' . $result, '<ok/>') == false) {
		logSync("send failure:\n$result\n");
		//-----------------------------------------------------------------------------------------
		//	sync failed, retry
		//-----------------------------------------------------------------------------------------
		$model->data['timestamp'] = time();
		$model->data['status'] = 'failed';
		$model->save();

	} else {		
		//-----------------------------------------------------------------------------------------
		//	sync completed successfully, we can delete this record
		//-----------------------------------------------------------------------------------------
		logSync("send success:\n$result\n");
		$model->delete();
	}

?>
