<?

	include "../../../shinit.php";
	require_once($kapenta->installPath . 'modules/wiki/models/mwimport.mod.php');

//--------------------------------------------------------------------------------------------------
//*	download outstanding articles from wikihow
//--------------------------------------------------------------------------------------------------

	$continue = true;
	$relayUrl = "http://mothsorchid.com/whrelay.php?p=whp6x&q=";

	while (true == $continue) {
		$conditions = array("content=''");
		$total = $db->countRange('wiki_mwimport');
		$num = $db->countRange('wiki_mwimport', $conditions);
		$range = $db->loadRange('wiki_mwimport', '*', $conditions, 'RAND()', 30000);
		
		foreach($range as $item) {
			$pc = floor((($total - $num) / $total) * 100);
			
			$padId = substr($item['pageid'] . '        ', 0, 8);
			echo "| $num | {$pc}% | $padId | article: {$item['title']}\n";

			$reference = 'api.php?action=query&format=php&prop=revisions&rvprop=content';
			$reference .= '&pageids=' . $item['pageid'];
			//echo 'reference: ' . $reference . "\n";
			
			$raw = implode(file($relayUrl . base64_encode($reference)));
			$parts = explode("\r\n\r\n", $raw, 2);
			//echo $parts[0] . "\n" . str_repeat('-', 79) . "\n\n\n";

			$splitPos = strpos($raw, "\r\n\r\n");
			if ((false != strpos($raw, "200 OK")) && ($splitPos > 0)) {
				//echo "serialize: " . $parts[1] . "\n" . str_repeat('-', 79) . "\n\n\n";
				//$data = unserialize($parts[1]);
				//print_r($data);

				$model = new Wiki_MWImport();
				$model->loadArray($item);
				$model->content = $parts[1];
				$model->save();

			}

			$num--;
	
			$cd = 4;
			while ($cd > 0) { sleep(1); $cd--; }
			//echo "\n";
		}
	
		$continue = false;
	}
	

?>
