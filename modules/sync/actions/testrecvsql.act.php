<?

//-------------------------------------------------------------------------------------------------
//	post a spurious sql update to self
//-------------------------------------------------------------------------------------------------

	require_once($installPath . 'modules/sync/models/servers.mod.php');
	require_once($installPath . 'modules/sync/models/sync.mod.php');

	$postUrl = $serverPath . 'sync/recvsql/';

	$ownData = syncGetOwnData();

	$server = new Server($ownData['UID']);

	$syncTime = time();
	$syncTimestamp = 'Sync-timestamp: ' . $syncTime;
	$syncProof = 'Sync-proof: ' . sha1($server->data['password'] . $syncTime);
	$syncSource = 'Sync-source: ' . $ownData['serverurl'];
	$postHeaders = array($syncTimestamp, $syncProof, $syncSource);

	$uid = createUID();

	$data = array(	'UID' => $uid,
					'refTable' => 'moose',
					'refUID' => $uid,
					'aliaslc' => 'test' . $uid,
					'alias' => 'TEST' . $uid,
					'editedOn' => mysql_datetime(),
					'editedBy' => 'test'
				);

	$xmlData = syncBase64EncodeSql('recordalias', $data);

	$postVars = 'detail=' . urlencode($xmlData);

	$ch = curl_init($postUrl);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postVars);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $postHeaders);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch);

	$result = str_replace('<', '&lt;', $result);
	$result = str_replace('>', '&gt;', $result);

	echo "sql update result: $result <br/>\n";

//-------------------------------------------------------------------------------------------------
//	and delete it again
//-------------------------------------------------------------------------------------------------

	$postUrl = $serverPath . 'sync/recvsqldelete/';

	$server = new Server($ownData['UID']);

	$syncTime = time();
	$syncTimestamp = 'Sync-timestamp: ' . $syncTime;
	$syncProof = 'Sync-proof: ' . sha1($server->data['password'] . $syncTime);
	$syncSource = 'Sync-source: ' . $ownData['serverurl'];
	$postHeaders = array($syncTimestamp, $syncProof, $syncSource);


	$xmlData = "<deletion><table>recordalias</table><uid>$uid</uid></deletion>";

	$postVars = 'detail=' . urlencode($xmlData);

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
