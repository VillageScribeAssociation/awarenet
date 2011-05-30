<?

	require_once($kapenta->installPath . 'modules/sync/models/server.mod.php');
	require_once($kapenta->installPath . 'modules/sync/models/notice.mod.php');

//-------------------------------------------------------------------------------------------------
//	post a spurious sql update to self
//-------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $page->do403(); }

	$postUrl = $kapenta->serverPath . 'sync/recvsql/';

	$ownData = $sync->getOwnData();

	$server = new Sync_Server($ownData['UID']);

	$syncTime = time();
	$syncTimestamp = 'Sync-timestamp: ' . $syncTime;
	$syncProof = 'Sync-proof: ' . sha1($server->password . $syncTime);
	$syncSource = 'Sync-source: ' . $ownData['serverurl'];
	$postHeaders = array($syncTimestamp, $syncProof, $syncSource);

	$uid = $kapenta->createUID();

	$data = array(	'UID' => $uid,
					'refTable' => 'moose',
					'refUID' => $uid,
					'aliaslc' => 'test' . $uid,
					'alias' => 'TEST' . $uid,
					'editedOn' => $db->datetime(),
					'editedBy' => 'test'
				);

	$xmlData = $sync->base64EncodeSql('recordalias', $data);

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

	$postUrl = $kapenta->serverPath . 'sync/recvsqldelete/';

	$server = new Sync_Server($ownData['UID']);

	$syncTime = time();
	$syncTimestamp = 'Sync-timestamp: ' . $syncTime;
	$syncProof = 'Sync-proof: ' . sha1($server->password . $syncTime);
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
