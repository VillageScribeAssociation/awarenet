<?

	include "../../../setup.php";

//--------------------------------------------------------------------------------------------------
//*	page to scan wikihow through mothsorchid.com proxy
//--------------------------------------------------------------------------------------------------

	$scanDir = "../../../data/mwimport/scan/";

	$step = 50;
	$startTitle = '';
	$startTitle = 'Differentiate Math Functions';		//temp
	$max = 500000;
	$continue = true;

	$totalPages = 0;

	while (true == $continue) {
		//------------------------------------------------------------------------------------------
		//	get and parse next page of results
		//------------------------------------------------------------------------------------------
		//$reference = "Create-a-Text-Input-Using-HTML";	//test data
		$reference = "api.php?action=query&list=allpages&format=xml"
				. "&apfrom=" . urlencode($startTitle)
				. "&aplimit=" . $step;
	
		$relayUrl = "http://mothsorchid.com/whrelay.php?p=whp6x&q=" . base64_encode($reference);

		$raw = implode(file($relayUrl));
		$data = wiki_shell_expandAllPages($raw);

		//------------------------------------------------------------------------------------------
		//	print to console
		//------------------------------------------------------------------------------------------
		echo ">> " . strtoupper($reference) . "\n";

		foreach($data['allpages'] as $page) {
			echo "page id: " . $page['id']
			  . " namespace: " . $page['ns']
			  . " title: " . $page['title'] . "\n";
		}
		echo "next apfrom: " . $data['apfrom'] . "\n";
		$startTitle = $data['apfrom'];
		if ('' == $data['apfrom']) { $continue = false; }

		//echo str_repeat('-', 79) . "\n";
		//echo "raw data:" . $raw . "\n";
		echo str_repeat('-', 79) . "\n";

		//------------------------------------------------------------------------------------------
		//	save raw listing
		//------------------------------------------------------------------------------------------
		$fileName = $scanDir . "mwi50_" . $kapenta->time() . ".raw";
		$fH = fopen($fileName, 'w+');
		fwrite($fH, $raw);
		fclose($fH);

		$max--;
		if (0 == $max) { $continue = false; }

		$totalPages += count($data['allpages']);
		$cd = 15;
		echo $totalPages . " scanned (wait $cd) ";
		while ($cd > 0) { sleep(1); $cd--; echo "."; }
		echo "\n\n";

	}


//--------------------------------------------------------------------------------------------------
//|	utility functions
//--------------------------------------------------------------------------------------------------

	function wiki_shell_expandAllPages($xml) {
		$result = array('apfrom' => '', 'allpages' => array());

		$xml = str_replace(">", ">\n", $xml);
		$lines = explode("\n", $xml);
		//foreach($lines as $line) {	echo htmlentities($line) . "<br/>\n"; }

		foreach($lines as $line) {
			if ('<allpages apfrom' == substr($line, 0, 16)) { 
				$parts = explode("\"", $line);
				$result['apfrom'] = $parts[1];
			}

			if ('<p pageid' == substr($line, 0, 9)) { 
				$parts = explode("\"", $line);
				$result['allpages'][] = array(
					'id' => $parts[1],
					'ns' => $parts[3],
					'title' => $parts[5]
				);
			}
		}

		return $result;
	}

?>
