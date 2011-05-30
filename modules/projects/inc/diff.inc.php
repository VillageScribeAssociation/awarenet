<?

//--------------------------------------------------------------------------------------------------
//	function to use command line diff
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//	diff some html, split into lines first
//--------------------------------------------------------------------------------------------------

function diffHtml($txt1, $txt2) { return diff(diffAddHtmlNl($txt1), diffAddHtmlNl($txt2)); }

//--------------------------------------------------------------------------------------------------
//	add newlines to support diff operation
//--------------------------------------------------------------------------------------------------

function diffAddHtmlNl($html) {
	$html = str_replace("\n", '', $html);
	$html = str_replace("\r", '', $html);

	$replacements = explode("|", "<p>|<ul>|<ol>|<table>|<h1>|<h2>|<h3>|<blockquote>|<br>|<br/>");
	foreach($replacements as $replace) {
		$html = str_replace($replace, "\n" . $replace, $html);
		$replace = str_replace("<", "</", $replace);
		$html = str_replace($replace, $replace. "\n", $html);
	}

	$html = str_replace('<inline>', '', $html);
	$html = str_replace('</inline>', '', $html);

	return $html;
}

//--------------------------------------------------------------------------------------------------
//	diff text
//--------------------------------------------------------------------------------------------------

function diff($txt1, $txt2) {
	global $kapenta;

	//----------------------------------------------------------------------------------------------	
	//	save txt pair to disk
	//----------------------------------------------------------------------------------------------
	$file1 = $kapenta->installPath . 'modules/projects/difftemp/1.txt';
	writefile($file1, $txt1);

	$file2 = $kapenta->installPath . 'modules/projects/difftemp/2.txt';  
	writefile($file2, $txt2);

	//----------------------------------------------------------------------------------------------	
	//	run the diff
	//----------------------------------------------------------------------------------------------
	$shellCmd = "diff $file2 $file1";
	$result = shell_exec($shellCmd);

	//----------------------------------------------------------------------------------------------	
	//	parse the resulting text
	//----------------------------------------------------------------------------------------------
	$ops = diffParseResult($result);
	return $ops;
}

//--------------------------------------------------------------------------------------------------
//	parse diff result, return array of changes
//--------------------------------------------------------------------------------------------------

function diffParseResult($result) {
	$ops = array();
	$op = '';
	$lines = explode("\n", $result);

	foreach($lines as $line) { 
		$isOp = true;
		if (substr($line, 0, 1) == '<') { $isOp = false; }
		if (substr($line, 0, 1) == '>') { $isOp = false; }
		if (substr($line, 0, 1) == '-') { $isOp = false; }
		if (trim($line) == '') { $isOp = false; }

		if (true == $isOp) {
			if (is_array($op) == true) { $ops[] = $op; }
			$op = array();
			$op['raw'] = $line;
			$op['type'] = '';
			$op['desc'] = '';
			$op['from'] = '';
			$op['to'] = '';
		
			if (strpos($line, 'a') != false) { $op['type'] = 'a'; }		// addition
			if (strpos($line, 'd') != false) { $op['type'] = 'd'; }		// deletion
			if (strpos($line, 'c') != false) { $op['type'] = 'c'; }		// deletion

			$line = str_replace(explode('|', 'a|d|c'), explode('|', 'x|x|x'), $line);
			$parts = explode("x", $line);
			$op['from'] = $parts[0];
			$op['to'] = $parts[1];

		} else {
			if ($line == '---') { 
				$op['desc'] .= '<!--fold-->'; 
			} else {
				$op['desc'] .= substr($line, 2) . "\n";
			}
		}

	}
	$ops[] = $op;	// add last operation
	
	return $ops;
}

//--------------------------------------------------------------------------------------------------
//	express a diff op as html
//--------------------------------------------------------------------------------------------------

function diffOpToHtml($op) {
	$html = '';

	switch ($op['type']) {

		case 'a':
				$html .= "<b>Added at line " . $op['from'] . ", becoming line " . $op['to']
					   . ":</b><br/>\n"
					   . "<div class='inlinequote'>" . $op['desc'] . "</div>\n"; break;

		case 'd':
				$html .= "<b>Removed line " . $op['from'] . ":</b><br/>\n"
					   . "<div class='inlinequote'>" . $op['desc'] . "</div>\n"; break;

		case 'c':
				$foldPos = strpos($op['desc'], '<!--fold-->');
				$desc1 = substr($op['desc'], 0, $foldPos);
				$desc2 = substr($op['desc'], $foldPos + 11);

				$html .= "<b>Changed line " . $op['from'] . ":</b><br/>\n"
					   . "<div class='inlinequote'>" . $desc1 . "</div>\n"
					   . "<b>To become line " . $op['to'] . ":</b><br/>\n"
					   . "<div class='inlinequote'>" . $desc2 . "</div>\n"; break;

	}

	return $html;
}

//--------------------------------------------------------------------------------------------------
//	write a file to disk
//--------------------------------------------------------------------------------------------------

function writefile($fileName, $txt) {
	$fH = fopen($fileName, 'w+');
	fwrite($fH, $txt);
	fclose($fH);
}

?>
