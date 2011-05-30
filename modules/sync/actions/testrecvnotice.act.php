<?

	require_once($kapenta->installPath . 'modules/sync/models/server.mod.php');
	require_once($kapenta->installPath . 'modules/sync/models/notice.mod.php');

//-------------------------------------------------------------------------------------------------
//*	post a spurious notice to self
//-------------------------------------------------------------------------------------------------

	$postUrl = $kapenta->serverPath . 'sync/recvnotice/';

	$ownData = $sync->getOwnData();

	$server = new Sync_Server($ownData['UID']);

	$syncTime = time();
	$syncTimestamp = 'Sync-timestamp: ' . $syncTime;
	$syncProof = 'Sync-proof: ' . sha1($server->password . $syncTime);
	$syncSource = 'Sync-source: ' . $ownData['serverurl'];
	$postHeaders = array($syncTimestamp, $syncProof, $syncSource);

	$channelId = 'admin-syspagelogsimple';
	$event = 'add';
	$entry = $db->datetime() . " - " . $user->username . ' - ' . '/test/spurious/';
	$entry = base64_encode($entry);

	$data = "<notification>\n"
		  . "\t<channelid>$channelId</channelid>\n"
		  . "\t<event>$event</event>\n"
		  . "\t<data>$entry</data>\n"
		  . "</notification>\n";

	$postVars = 'detail=' . urlencode($data);

	$ch = curl_init($postUrl);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postVars);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $postHeaders);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch);

	$result = str_replace('<', '&lt;', $result);
	$result = str_replace('>', '&gt;', $result);

	echo "sql update result: $result <br/>\n";

?>
