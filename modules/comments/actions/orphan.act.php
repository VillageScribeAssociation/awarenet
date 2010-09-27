<?

//-------------------------------------------------------------------------------------------------
//	search the sync logs for orphan Comments
//-------------------------------------------------------------------------------------------------

	require_once($kapenta->installPath . 'modules/comments/models/comments.mod.php');

	if ('admin' != $user->role) { $page->do403(); }

	$knownComments = array();

	//---------------------------------------------------------------------------------------------
	//	make list of all sync logs
	//---------------------------------------------------------------------------------------------

	$cmd = "ls " . $installPath . "data/log/*c.log.php";
	$result = shell_exec($cmd);
	$lines = explode("\n", $result);
	
	foreach ($lines as $line) {
		if (strlen($line) > 4) {
			echo "<h2>" . $line . "</h2>\n"; flush();
			commentStreamSync($line);		
		}
	}

//--------------------------------------------------------------------------------------------------
//	process a sync log one item at a tiem looking for comments
//--------------------------------------------------------------------------------------------------

function commentStreamSync($fileName) {
	$marker = '*******************************************************';
	$buffer = '';

	$fH = fopen($fileName, 'r');

	while (!feof($fH)) {
		$buffer .= fread($fH, 1024);	// read another KB into the buffer

		$markerPos = strpos($buffer, $marker);		
		while ($markerPos != false) {
			$chunk = substr($buffer, 0, $markerPos + strlen($marker));
		
			if (strpos($chunk, '</update>') != false) {

				$startPos = strpos($chunk, "<update>");
				if ($startPos > 0) { $chunk = substr($chunk, $startPos); }

				$endPos = strpos($chunk, "</update>");
				if ($endPos > 0) { $chunk = substr($chunk, 0, $endPos + 9); }

				if (strpos($chunk, "<table>comments</table>") != false) {
					//echo "throwing chunk:<br/>\n";
					//echo "<textarea rows='10' cols='80'>$chunk</textarea><br/>\n";
					processComment($chunk);
				}

			}

			$buffer = substr($buffer, $markerPos + strlen($marker));
			$markerPos = strpos($buffer, $marker);		
		}

	}

	fclose($fH);
}


//-------------------------------------------------------------------------------------------------
//	find Comment xml
//-------------------------------------------------------------------------------------------------

function findComments($text) {
	global $countComments;

	$continue = true;
	$at = 0;

	while (true == $continue) {

		$startPos = strpos($text, "<table>comments", $at);
		if (false != $startPos) { 
			//echo "Comment found <br/>\n"; 

			$at = $startPos + 1;	
			$endPos = strpos($text, "</fields>", $at);

			//echo "startPos: $startPos <br/>\n";

			if (false != $endPos) {
				$endPos += 9;
				$xml = substr($text, $startPos, ($endPos - $startPos));
				if (strlen($xml) < 4000) {
					$xml = "<update>\n" . $xml . "\n" . "</update>";
					processComment($xml);
				}
	
				$at = $endPos + 1;

			} else { $continue = false; }


		} else { $continue = false; }

	} // end while

}

function processComment($xml) {
	global $db;

	global $knownComments;

	//$xml = str_replace("<update>", '', $xml);
	//$xml = str_replace("</update>", '', $xml);

	$print = str_replace("\n", "<br/>\n", $xml);
	$print = str_replace("<", "&lt;", $print);
	$print = str_replace(">", "&gt;", $print);
	//echo $print . "<br/>\n";

	$data = $sync->base64DecodeSql($xml);

	echo "found comment: " . $data['fields']['UID'] . "<br/>\n";

	if (false == $db->objectExists('comments', $data['fields']['UID'])) {

		//echo "<img src='http://awarenet.org.za/" . $data['fields']['fileName'] . "' />";

		if (in_array($data['fields']['UID'], $knownComments) == false) {

			//echo "wget http://awarenet.org.za/" . $data['fields']['fileName'] . "<br/>\n";
			$knownComments[] = $data['fields']['UID'];

			$model = new Comments_Comment();
			$model->loadArray($data['fields']);
			$model->save();

			echo "saved " . $model->UID . " <br/>\n";

			if (true == $sync->recordDeleted('comments', $model->UID)) {
				$sql = "delete from delitems where refUID='" . $model->UID . "'";
				$db->query($sql);
				echo "removed from delitems (" . $model->UID . ")...<br/>\n";
			}

		} else { echo "comment is already known to this script.<br/>\n"; flush(); }

	} else { echo "comment is already in database.<br/>\n"; flush(); }

}

?>