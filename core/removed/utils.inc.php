<?

//--------------------------------------------------------------------------------------------------
//*	common shared functions
//--------------------------------------------------------------------------------------------------
//+ most of these should be grouped and broken into their own files
//+ TODO: patch up naming of files

//--------------------------------------------------------------------------------------------------
//|	create a unique ID (VERY IMPORTANT FUNCTION) 
//--------------------------------------------------------------------------------------------------
//returns: a band new UID [string]

function createUID() {
	$tempUID = "";
	for ($i = 0; $i < 16; $i++) { $tempUID .= "" . mk_rand(); }
	return substr($tempUID, 0, 18);
}

//--------------------------------------------------------------------------------------------------
//|	clean a string of crap (to match searches and try stop SQL included into query)
//--------------------------------------------------------------------------------------------------
//arg: toClean - string [string]
//returns: slightly safer version of string to prevent JavaScript, HTML and SQL injection [string]
//: this is a very old function, from first version of Kapenta, deprecated

function clean_string($toClean) {
	$isClean = urldecode($toClean);
	$isClean = str_replace(";", ":", $isClean);
	$isClean = str_replace("'", "`", $isClean);
	$isClean = str_replace("\"", "``", $isClean);
	$isClean = str_replace("<", "<.", $isClean);
	return trim($isClean);
}

//--------------------------------------------------------------------------------------------------
//|	make a seed for the random number generator
//--------------------------------------------------------------------------------------------------
//: I don't think this is needed with PHP5
//returns: seed from current time [float]

function make_seed() {
   list($usec, $sec) = explode(' ', microtime());
   return (float) $sec + ((float) $usec * 100000);
}

//--------------------------------------------------------------------------------------------------
//|	make a random number
//--------------------------------------------------------------------------------------------------
//: I don't think this is needed with PHP5
//returns: a random number (0-1) [float]

function mk_rand() {
	srand(make_seed());
	return rand();
}

//--------------------------------------------------------------------------------------------------
//|	make a random number between two values
//--------------------------------------------------------------------------------------------------
//: I don't think this is needed with PHP5
//arg: min - lower bound [float]
//arg: max - upper bound [float]
//returns: a random number [float]

function mk_rand_num($min, $max) {
	srand(make_seed());
	return rand($min, $max);
}

//--------------------------------------------------------------------------------------------------
//|	Take tags out of HTML
//--------------------------------------------------------------------------------------------------
//arg: someText - text which may contain HTML tags [string]
//returns: text without HTML tags [string]

function stripHTML($someText) { return preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $someText); }

//--------------------------------------------------------------------------------------------------
//|	get the current date/time in mySQL format
//--------------------------------------------------------------------------------------------------
//returns: date in format used by MySQL

function mysql_dateTime() { 
	global $session, $db;
	$session->msgAdmin('deprecated: mysql_dateTime() => $db->datetime($date)', 'bug');
	return $db->datetime();
}

//--------------------------------------------------------------------------------------------------
//|	convert some arbitrary date/time to mySQL format
//--------------------------------------------------------------------------------------------------
//arg: date - UNIX timestamp [string]
//returns: date in format used by MySQL

function mk_mysql_dateTime($date) { 
	global $session, $db;
	$session->msgAdmin('deprecated: mk_mysql_dateTime() => $db->datetime($date)', 'bug');
	return $db->dateimte($date);
}

//--------------------------------------------------------------------------------------------------
//|	get a field from $_GET, $_POST, or set it to a default value if it down not exist
//--------------------------------------------------------------------------------------------------
//arg: fieldName - name of a field [string]
//arg: default - what to use if it is not defined [string]
//returns: value of this request field or default [string]
//: very old, deprecated TODO: remove from Kapenta

function requestField($fieldName, $default) {
	$retVal = $default;
	if (array_key_exists($fieldName, $_GET)) { $retVal = sqlMarkup($_GET[$fieldName]); }
	if (array_key_exists($fieldName, $_POST)) { $retVal = sqlMarkup($_POST[$fieldName]); }
	return $retVal;	
}

//--------------------------------------------------------------------------------------------------
//|	strip empty values from an array
//--------------------------------------------------------------------------------------------------
//arg: arr - an array [array]
//returns: array minus empty values [array]
//: this is very old, I think there's a native PHP function to do this.

function arrayCleanEmpty($arr) {
	$retVal = array();
	foreach($arr as $value) {
		if (strlen(trim($value)) > 0) {
			$retVal[] = $value;
		}
	}
	return $retVal;
}

//--------------------------------------------------------------------------------------------------
//|	remove all but alphanumeric characters from a string (allow '-', '_', convert space to _ )
//--------------------------------------------------------------------------------------------------
//arg: txt - text to clean [string]
//returns: only characters 0-9, a-z, A-Z, space, minus, underscore and forwardslash [string]

function mkAlphaNumeric($txt) {
	$txt = trim($txt);
	if ($txt == '') { return ''; }
	$retVal = '';
	$numChars = strlen($txt);
	for($i = 0; $i < $numChars; $i++) {
		$currChar = substr($txt, $i, 1);
		$oCC = ord($currChar);
		
		if (($oCC >= 48) AND ($oCC <= 57)) { $retVal .= $currChar; } 	// 0-9
		if (($oCC >= 97) AND ($oCC <= 122)) { $retVal .= $currChar; } 	// a-z
		if (($oCC >= 65) AND ($oCC <= 90)) { $retVal .= $currChar; } 	// A-Z
		if ($oCC == 32) { $retVal .= '_'; } // space
		if ($oCC == 45) { $retVal .= '-'; } // minus
		if ($oCC == 95) { $retVal .= '_'; } // underscore
		if ($oCC == 47) { $retVal .= '_'; } // forwardslash
	}
	
	return $retVal;
}

//--------------------------------------------------------------------------------------------------
//|	get the last x characters from a string (hopefully multibyte safe)
//--------------------------------------------------------------------------------------------------
//arg: numChars - number of characters to return [int]
//arg: txt - string to trim [string]
//returns: rightmost portion of string, or whole string if length < numChars [string]

function mb_endstr($numChars, $txt) {
	$txt = trim($txt);
	$len = mb_strlen($txt);
	if ($len > $numChars) {
		return mb_substr($txt, $len - $numChars);
	} else { return $txt; }
}

//--------------------------------------------------------------------------------------------------
//|	determines if a file/dir exists and is readable + writeable
//--------------------------------------------------------------------------------------------------
//arg: fileName - absolute[string]
//returns: true if exists, else false [bool]
//: TODO: use centralized directory traversal check

function is_extantrw($fileName) {
	global $kapenta; 
	if ($kapenta->fileExists($fileName)) {
		if (is_readable($fileName) == false) { return false; }
		if (is_writable($fileName) == false) { return false; }
	} else { return false; }
	return true;
}

//--------------------------------------------------------------------------------------------------
//|	remove comments from beginning and end of php file
//--------------------------------------------------------------------------------------------------
//arg: raw - unmodified file contents [string]
//returns: file contents not wrapped unwrapped from php comments [string]

function phpUnComment($raw) {
	$raw = trim($raw);
	if (substr($raw, 0, 5) == '<? /*') { $raw = substr($raw, 5); }
	$len = strlen($raw);
	if (substr($raw, ($len - 5)) == '*/ ?>') { $raw = substr($raw, 0, ($len - 5)); }
	return $raw;
}

//--------------------------------------------------------------------------------------------------
//|	some php versions do not support file_put_contents
//--------------------------------------------------------------------------------------------------
//arg: fileName - absolute fileName [string]
//arg: contents - new body of file [string]
//arg: mode - file mode (w|w+|a|a+) [string]
//returns: true on success, false on failure [bool]
//: plus sign in file mode causes files to be created if the do not exist

function filePutContents($fileName, $contents, $mode) {
	fileMakeSubdirs($fileName);
	$fH = fopen($fileName, $mode);
	if ($fH == false) { return false; }
	fwrite($fH, $contents);
	fclose($fH);
	return true;
}

//--------------------------------------------------------------------------------------------------
//|	ensure that directory exists
//--------------------------------------------------------------------------------------------------
//arg: fileName - containing full path to be confirmed or created [string]

function fileMakeSubdirs($fileName) {
	global $kapenta;
	$fileName = str_replace("//", '/', $fileName);	
	$dirName = str_replace($installPath, '', $fileName);
	$dirName = dirname($dirName);
	$base = $installPath;
	$subDirs = explode('/', $dirName);
	foreach($subDirs as $sdir) {
		$base = $base . $sdir . '/';
		if (false == $kapenta->fileExists($base)) { mkdir($base); }
	}
}

//--------------------------------------------------------------------------------------------------
//|	HTTP GET a URL using cURL, respecting kapenta settings for proxy, etc
//--------------------------------------------------------------------------------------------------
//: TODO - attempt other download methods (wget, file wrapper, etc) if curl not present
//: TODO - implement password for systems which use older HTTP authentication methods
//: TODO - handle HTTP error codes
//arg: url - a URL [string]
//opt: password - reserved for HTTP/1.x credentials, not implemented [string]
//returns: result of HTTP GET request, false if no cURL [string]

function curlGet($url, $password = '') {
	global $hostInterface;
	global $proxyEnabled;
	global $proxyAddress;
	global $proxyUser;
	global $proxyPass;
	global $proxyPort;
	
	if (false == function_exists('curl_init')) { return false; }	// is cURL installed?

	//---------------------------------------------------------------------------------------------
	//	create baisc cURL HTTP GET request
	//---------------------------------------------------------------------------------------------
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	if ('' != $hostInterface) { curl_setopt($ch, CURLOPT_INTERFACE, $hostInterface); }

	//---------------------------------------------------------------------------------------------
	//	use HTTP proxy if enabled
	//---------------------------------------------------------------------------------------------
	if ($proxyEnabled == 'yes') {
		$credentials = $proxyUser . ':' . $proxyPass;
		curl_setopt($ch, CURLOPT_PROXY, $proxyAddress);
		curl_setopt($ch, CURLOPT_PROXYPORT, $proxyPort);
		curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
		if (trim($credentials) != ':') {
			curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_PROXYUSERPWD, $credentials);
		}
	}

	//---------------------------------------------------------------------------------------------
	//	return result
	//---------------------------------------------------------------------------------------------
	$result = curl_exec($ch);
	return $result;
}

//--------------------------------------------------------------------------------------------------
//|	render a 2d array as a table
//--------------------------------------------------------------------------------------------------
//arg: ary - 2d array of table rows and columns [array]
//opt: wireframe - use kapenta's default wireframe table style [bool]
//opt: firstrowtitle - if column titles in first row to be highlighted [bool]
//returns: HTML table [string]
//: TODO: consider moving this to theme

function arrayToHtmlTable($ary, $wireframe = false, $firstrowtitle = false) {
	if (false == $wireframe) { 
		$html = "<table noborder width='100%'>";
		foreach($ary as $row) {
			$html .= "\t<tr>\n";
			foreach($row as $col) {	$html .= "\t\t<td>" . $col . "</td>\n"; }	
			$html .= "\t</tr>\n"; 
		}
		$html .= "</table>";

	} else {
		$html = "<table class='wireframe' width='100%'>";
		foreach($ary as $row) {
			$tdClass = 'wireframe';
			if (true == $firstrowtitle) { $firstrowtitle = false; $tdClass = 'title'; }
			$html .= "\t<tr>\n";
			foreach($row as $col) {	$html .= "\t\t<td class='". $tdClass ."'>". $col ."</td>\n"; }	
			$html .= "\t</tr>\n"; 
		}
		$html .= "</table>";
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------
//|	mark up html for injection via javascript
//--------------------------------------------------------------------------------------------------
//arg: txt - text to be marked up [string]
//returns: escaped txt [string]
//: this is deprecated in favor of base64 encoding

function jsMarkup($txt) {
	$txt = str_replace("'", '--squote--', $txt);
	$txt = str_replace("\"", '--dquote--', $txt);
	$txt = str_replace("\n", '--newline--', $txt);
	$txt = str_replace("\r", '', $txt);
	$txt = str_replace("[[:", '[[%%delme%%:', $txt);
	return $txt;
}

//--------------------------------------------------------------------------------------------------
//|	convert to base64 (TODO: make this more efficient)
//--------------------------------------------------------------------------------------------------
//arg: varName - JavaScript variable name [string]
//arg: text - value of javascript variable [string]
//opt: scriptTags - wrap in html script tags if true [bool]
//returns: code for javascript variable assignment [string]

function base64EncodeJs($varName, $text, $scriptTags = true) {
	$b64 = base64_encode($text);										// encode
	$b64 = wordwrap($b64, 80, "\n", true);								// break into 80 char lines
	$break = "\"\n" . str_repeat(' ', strlen($varName) + 5) . "+ \"";	// newline/indent pattern
	$b64 = str_replace("\n", $break, $b64);								// apply pattern to lines
	$b64 = "var $varName = \"" . $b64 . "\";\n";						// add js varname
	if (true == $scriptTags) { $b64 = "<script language='Javascript'>\n" . $b64 . "</script>\n"; }
	return $b64;														// done
}

//--------------------------------------------------------------------------------------------------
// 	start a process in background (*nix only)
//--------------------------------------------------------------------------------------------------
//:	source: http://nsaunders.wordpress.com/2007/01/12/running-a-background-process-in-php/
//:	TODO: find equivalent for windows
//: will probably not work in safe mode, forking in PHP is too large a topic to be covered here

//arg: Command - shell command [string]
//arg: Priority - runlevel [int]
//returns: ID of new process (PID) [int]

function procExecBackground($Command, $Priority = 0) {
	$PID = false;
	if ($Priority) { 
		// consider removing this
		$PID = exec("$Command > > /dev/null 2>&1 &"); 
	} else { 
		$PID = exec("$Command > /dev/null 2>&1 &");
	}
	return $PID;
}

//--------------------------------------------------------------------------------------------------
//|	discover if a process is running
//--------------------------------------------------------------------------------------------------
//:	source: http://nsaunders.wordpress.com/2007/01/12/running-a-background-process-in-php/
//: *Nix only, might work on windows with Cygwin or GNU tools, probably won't work in safe mode
//arg: PID - process ID [int] [string]
//returns: true if running, otherwise false [bool]

function procIsRunning($PID) {
	exec("ps $PID", $ProcessState);
	return(count($ProcessState) >= 2);
}

//--------------------------------------------------------------------------------------------------
// 	remove javascript from html (prevent XSS worms, etc)
//--------------------------------------------------------------------------------------------------
//:	source: http://us3.php.net/manual/en/function.strip-tags.php
//: TODO: review this 
//arg: Source - HTML to strip of Javascript [string]
//opt: aAllowedTags - tags which may contain javascript (?) [array]
//opt: aDisabledAttributes - attributes which tags may not have (?) [array]
//returns: sanitized (X)HTML [string]

function stripJavascript( $sSource, $aAllowedTags = array(), $aDisabledAttributes = array() ) {
	//---------------------------------------------------------------------------------------------
	//	if no attributes to diable have been specified, use these:
	//---------------------------------------------------------------------------------------------
	if (count($aDisabledAttributes) == 0) {	
		$aDisabledAttributes = array(	
								'onabort', 'onactivate', 'onafterprint', 'onafterupdate', 
								'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 
								'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 
								'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 
								'onbounce', 'oncellchange', 'onchange', 'onclick', 
								'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 
								'ondataavaible', 'ondatasetchanged', 'ondatasetcomplete', 
								'ondblclick', 'ondeactivate', 'ondrag', 'ondragdrop', 
								'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 
								'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 
								'onfilterupdate', 'onfinish', 'onfocus', 'onfocusin', 
								'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 
								'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 
								'onmouseenter', 'onmouseleave', 'onmousemove', 'onmoveout', 
								'onmouseover', 'onmouseup', 'onmousewheel', 'onmove',
								'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange',
								'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 
								'onresizestart', 'onrowexit', 'onrowsdelete', 
								'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 
								'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
	}

	if (empty($aDisabledAttributes)) return strip_tags($sSource, implode('', $aAllowedTags));

	$pattern = '/<(.*?)>/ie';

	$replacement = 	"'<' . preg_replace(array('/javascript:[^\"\']*/i', '/("
					. implode('|', $aDisabledAttributes)
					. ")[ \\t\\n]*=[ \\t\\n]*[\"\'][^\"\']*[\"\']/i', '/\s+/'), "
					. "array('', '', ' '), stripslashes('\\1')) . '>'";

	return preg_replace($pattern, $replacement, $sSource);
}

//--------------------------------------------------------------------------------------------------
// 	convert plain text to html (quite basic for now)
//--------------------------------------------------------------------------------------------------

function txtToHtml($txt) {
	$html = str_replace('<', '&lt;', $txt);
	$html = str_replace('>', '&gt;', $html);
	$html = str_replace("\r", '', $html);
	$html = str_replace("\n", "<br/>\n", $html);
	return $html;
}

?>
