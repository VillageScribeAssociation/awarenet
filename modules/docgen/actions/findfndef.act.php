<?

//-------------------------------------------------------------------------------------------------
//*	find function definitions, mark up source
//-------------------------------------------------------------------------------------------------

	require_once($kapenta->installPath . 'modules/docgen/inc/tokenizer.inc.php');
	require_once($kapenta->installPath . 'modules/docgen/inc/lexer.inc.php');
	//require_once($kapenta->installPath . 'modules/docgen/models/source.class.php');

	//---------------------------------------------------------------------------------------------
	//	load and parse source
	//---------------------------------------------------------------------------------------------

	$testFile = $kapenta->installPath. 'modules/docgen/test.txt';
	$source = implode(file($testFile));

	$cells = dgTokenizeSource($source);

	echo count($cells) . " cells<br/>\n";
	$cells = dgSetSourceColor($cells);

	//---------------------------------------------------------------------------------------------
	//	make array of tokens
	//---------------------------------------------------------------------------------------------

	$ta = array();
	dgThrowTokens($cells, 'catchToken');

	function catchToken($cells) { 
		global $ta;

		$lit = dgCellsToString($cells);
		
		$t = $cells[0]['type'];
		if (($t == 'n') || ($t == 'w')) { return; }

		if (($t == 'c') && (strpos($lit, '----') != false)) { $t = 'h'; }
		if (($t == 't') && ($lit == 'function')) { $t = 'f'; }
		
		$newToken = array();
		$newToken['cells'] = $cells;
		$newToken['type'] = $t;
		$newToken['lit'] = $lit;

		$ta[] = $newToken;
	}

	//---------------------------------------------------------------------------------------------
	//	scan backward for function definitions
	//---------------------------------------------------------------------------------------------

	$fnName = '';

	for ($i = count($ta); $i <= 0; $i--) {
		$token = $ta[$i];

		if (($token['type'] == 't') && ($ta[($i - 1)]['type'] == 'f')) { 
			$fnName = $token['lit'];
			echo "function: $fnName <br/>\n";
		}

		echo "token: (" . $token['type'] . ") " . $token['lit'] . "<br/>\n";
	}

	//---------------------------------------------------------------------------------------------
	//	scan forward for file synopsis and description
	//---------------------------------------------------------------------------------------------

	$hcount = 0;	
	$fileSynopsis = '';
	$fileDesc = '';
	$other = false;
	$allowed = array('u', 'n', 'c', 'l', 'e', 'w', 'h', '');

	foreach($ta as $token) {
		//

		if (in_array($token['type'], $allowed) == false) { 
			if (false == $other) { echo "othertoken: " . $token['type'] . " <br/>\n"; }
			$other = true; 
		}

		if (false == $other) {
			//echo "token: (" . $token['type'] . ") " . $token['lit'] . "<br/>\n";
			if ($token['type'] == 'h') { 
				$hcount++; 
				echo "hcount: $hcount <br/>\n";
			}
			if ($token['type'] == 'c') {
				if ($hcount == 1) { $fileSynopsis .= $token['lit'] . "\n"; }
				if ($hcount == 2) { $fileDesc .= $token['lit'] . "\n"; }		
			}
		}
	}

	echo "file synopsis: $fileSynopsis <br/>\nfile description: $fileDesc <br/>\n";

	//---------------------------------------------------------------------------------------------
	//	print all source
	//---------------------------------------------------------------------------------------------

	$html = dgCellsToHtml($cells);
	echo $html;

?>
