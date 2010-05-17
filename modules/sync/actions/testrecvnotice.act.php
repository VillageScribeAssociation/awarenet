<?

//-------------------------------------------------------------------------------------------------
//	post a spurious notice to self
//-------------------------------------------------------------------------------------------------

	require_once($installPath . 'modules/sync/models/server.mod.php');
	require_once($installPath . 'modules/sync/models/sync.mod.php');

	$postUrl = $serverPath . 'sync/recvnotice/';

	$ownData = syncGetOwnData();

	$server = new Server($ownData['UID']);

	$syncTime = time();
	$syncTimestamp = 'Sync-timestamp: ' . $syncTime;
	$syncProof = 'Sync-proof: ' . sha1($server->data['password'] . $syncTime);
	$syncSource = 'Sync-source: ' . $ownData['serverurl'];
	$postHeaders = array($syncTimestamp, $syncProof, $syncSource);

	$channelId = 'admin-syspagelogsimple';
	$event = 'add';
	$entry = mysql_datetime() . " - " . $user->data['username'] . ' - ' . '/test/spurious/';
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
