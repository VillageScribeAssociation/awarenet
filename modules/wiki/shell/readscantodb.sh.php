<?

	include "../../../shinit.php";
	require_once($kapenta->installPath . 'modules/wiki/models/mwimport.mod.php');

//--------------------------------------------------------------------------------------------------
//*	read scanned article list into database
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	go through all mwi50 files in the dump directory
	//----------------------------------------------------------------------------------------------
	$scanDir = dir("../../../data/mwimport/scan/");
	$max = 100;
	
	$totalCount = 0;
	$newCount = 0;

	while (true) {
		$entry = $scanDir->read();
		if (false == $entry) { break; }
		if ('mwi50' == substr($entry, 0, 5)) {

			$stat = "[" . $totalCount . "/" . $newCount . "] ";

			//--------------------------------------------------------------------------------------
			//	read this file and look for new articles
			//--------------------------------------------------------------------------------------
			$mwiFile = $scanDir->path . '/' . $entry;
			echo "Processing $mwiFile ...\n";
			$xml = implode(file($mwiFile));
			$data = wiki_shell_expandAllPages($xml);
		
			foreach($data['allpages'] as $article) {
				echo $stat 
				  . "page id: " . $article['id']
				  . " namespace: " . $article['ns']
				  . " title: " . $article['title'] . "\n";

				$totalCount++;

				//----------------------------------------------------------------------------------
				//	discover if this article exists in the database
				//----------------------------------------------------------------------------------
				$conditions = array("pageid='" . $kapenta->db->addMarkup($article['id']) . "'");
				$num = $kapenta->db->countRange('wiki_mwimport', $conditions);

				if (0 == $num) {
					$newCount++;
					echo $stat . "New to database, saving...\n"; flush();
					$model = new Wiki_MWImport();
					$model->UID = $kapenta->createUID();
					$model->title = $article['title'];
					$model->pageid = $article['id'];
					$model->status = 'new';
					$model->save();

				} else {
					echo $stat . "Exists in database, TODO update...\n"; flush();
				}

			}

			echo str_repeat('-', 79) . "\n\n";

		}

		//$max--;
		//if (0 == $max) { break; }
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
