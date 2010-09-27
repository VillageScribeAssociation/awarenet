<?

//-------------------------------------------------------------------------------------------------
//	search the sync logs for orphan images
//-------------------------------------------------------------------------------------------------

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

$countImages = 0;
$knownImages = array();

	//if ('admin' != $user->role) { $page->do403(); }

	//---------------------------------------------------------------------------------------------
	//	make list of all sync logs
	//---------------------------------------------------------------------------------------------

	$cmd = "ls " . $installPath . "data/log/*c.log.php";
	$result = shell_exec($cmd);
	$lines = explode("\n", $result);
	
	foreach ($lines as $line) {
		if (strlen($line) > 4) {
			//echo "<h2>" . $line . "</h2>\n";

			$raw = implode(file($line));
			findImages($raw);
		}
	}

//-------------------------------------------------------------------------------------------------
//	find image xml
//-------------------------------------------------------------------------------------------------

function findImages($text) {
	global $countImages;

	$continue = true;
	$at = 0;

	while (true == $continue) {

		$startPos = strpos($text, "<table>images", $at);
		if (false != $startPos) { 
			//echo "image found <br/>\n"; 

			$at = $startPos + 1;	
			$endPos = strpos($text, "</fields>", $at);

			//echo "startPos: $startPos <br/>\n";

			if (false != $endPos) {
				$endPos += 9;
				$xml = substr($text, $startPos, ($endPos - $startPos));
				if (strlen($xml) < 4000) {
					$xml = "<update>\n" . $xml . "\n" . "</update>";
					processImage($xml);
				}
	
				$at = $endPos + 1;

			} else { $continue = false; }


		} else { $continue = false; }

	} // end while

}

function processImage($xml) {
	global $db;

	//echo "<textarea rows=10 cols=80>$xml</textarea>\n<br/>";
	global $knownImages;

	$data = $sync->base64DecodeSql($xml);

	if (false == $db->objectExists('images', $data['fields']['UID'])) {

		//echo "<textarea rows=10 cols=80>";
		//print_r($data);
		//echo "</textarea>\n<br/>";


		//echo "<img src='http://awarenet.org.za/" . $data['fields']['fileName'] . "' />";

		if (in_array($data['fields']['UID'], $knownImages) == false) {

			//echo "wget http://awarenet.org.za/" . $data['fields']['fileName'] . "<br/>\n";
			$knownImages[] = $data['fields']['UID'];

			if (strlen($data['fields']['fileName']) > 3) {

				$model = new Images_Image();
				$model->loadArray($data['fields']);
				$model->save();

				echo "saved " . $model->alias . " <br/>\n";

			} 

		}

	}

}

?>