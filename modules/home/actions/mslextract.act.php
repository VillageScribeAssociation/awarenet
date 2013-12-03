<?

//--------------------------------------------------------------------------------------------------
//*	temporary action to extract metadata from cached MSL pages 
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	admins only
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	process html files
	//----------------------------------------------------------------------------------------------
	$tmpDir = 'data/exampapers/tmp/';
	$files = $kapenta->fileList($tmpDir, '.html');

	$links = array();

	foreach($files as $file) {
		echo "<h2>$file</h2>\n";
		$raw = $kapenta->fs->get($file);
		echo "length: " . strlen($raw) . "<br/>\n";
		$raw = str_replace("<a", "\n<a", $raw);
		$raw = str_replace("</a>", "</a>\n", $raw);
		$lines = explode("\n", $raw);
	
		foreach($lines as $line) {
			if (("<a" == substr(trim($line), 0, 2)) && (false != strpos($line, "http://www.education.gpg.gov.za"))) {
				$line = str_replace("</a>", '', $line);
				$line = str_replace("<a href=", '', $line);
				$line = str_replace("target=\"_new\"", "", $line);
				$line = str_replace("\"", "", $line);
				$line = str_replace(">", "|*|", $line);

				$parts = explode("|*|", $line);
				if (2 == count($parts)) { 
					echo "url: " . $parts[0] . " title: " . $parts[1] . "<br/>\n"; 
					$links[trim(strtolower($parts[0]))] = trim($parts[1]);
				}

			}
		}		

	}

	//----------------------------------------------------------------------------------------------
	//	get list of word documents
	//----------------------------------------------------------------------------------------------
	$wikiCode = '';
	$docs = $kapenta->fileList('data/exampapers/', '.doc');

	foreach($docs as $doc) {
		$equiv = str_replace('data/exampapers/', '', $doc);
		$equiv = "http://www.education.gpg.gov.za/matricinfo/past%20papers/" . str_replace(' ', '%20', $equiv);
		echo "doc: $doc ==> $equiv <br/>";

		if (true == array_key_exists(strtolower($equiv), $links)) { 
			$title = $links[strtolower($equiv)];
			echo "***found: " . $title . "<br/>\n";
			$wikiCode .= "[[" . $kapenta->serverPath . "$doc|$title]]<br/>\n";
		}
	}

	echo "<br/><br/>$wikiCode";

?>
