<?

//--------------------------------------------------------------------------------------------------
//*	utility object, performs miscellaneous tasks
//--------------------------------------------------------------------------------------------------

class KUtils {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $maxDeprecatedNotices = 1024;			//%	prevent excessive memory use [int]

	//----------------------------------------------------------------------------------------------
	//.	make a random number, compatability with older PHP versions
	//----------------------------------------------------------------------------------------------
	//opt: min - bottom of range [float]
	//opt: max - top of range [float]
	//: I don't think this is needed with PHP5
	//returns: a random number (0-1) [float]

	function random($min = 0, $max = 1) {
		srand(make_seed());
		return rand($min, $max);
	}

	//==============================================================================================
	//	INPUT SANITIZATION
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	filter HTML to allowed tags and attributes
	//----------------------------------------------------------------------------------------------
	//arg: html - html to be sanitized [string]
	//returns: sanitized input [string]
	//TODO: more configuration options

	function cleanHtml($html) {
		$mq = mb_strtolower(ini_get('magic_quotes_gpc'));
		if (('on' == $mq) || ('1' == $mq)) { $html = stripslashes($html); }

		//	disallow non-breaking spaces, some users leave thousands of them at the end of blog
		//	posts and forum posts, and it screws up the layout.
		$html = str_replace('&nbsp;', ' ', $html);

		$parser = new KHTMLParser($html);
		$html = $parser->output;

		$html = $this->trimHtml($html);

		return $html;
	}

	//----------------------------------------------------------------------------------------------
	//.	clean html entities in titles and similar strings
	//----------------------------------------------------------------------------------------------
	//arg: txt - text to clean of html tags and entities [string]
	//returns: sanitized input [string]

	function cleanTitle($txt) {
		$txt = $this->stripHtml($txt);
		$txt = htmlentities($txt, ENT_QUOTES, "UTF-8");
		$txt = str_replace("'", '&#39;', $txt);
		$txt = str_replace("\"", '&quot;', $txt);
		return $txt;
	}

	//----------------------------------------------------------------------------------------------
	//.	clean yes/no fields 
	//----------------------------------------------------------------------------------------------
	//arg: yesno - string to match against truthy and falsey values for 'yes' and 'no' [string]
	//returns: 'yes' or 'no' [string]

	function cleanYesNo($yesno) {
		$yesno = trim(mb_strtolower($yesno));
		switch($yesno) {
			case 'false':		$yesno = 'no';		break;
			case 'true':		$yesno = 'yes';		break;
			case '0':			$yesno = 'no';		break;
			case '1':			$yesno = 'yes';		break;
		}

		if ('yes' != $yesno) { $yesno = 'no'; }
		return $yesno;
	}

	//----------------------------------------------------------------------------------------------
	//.	clean a string of crap (to match searches and try stop SQL included into query)
	//----------------------------------------------------------------------------------------------
	//arg: toClean - string [string]
	//returns: safer version of string to prevent JavaScript, HTML and SQL injection [string]
	//: this is a very old function, from first version of Kapenta, deprecated

	function cleanString($toClean) {
		global $session;
		//$session->msgAdmin("deprecated: cleanString()", 'bug');
		$isClean = urldecode($toClean);
		$isClean = str_replace(";", ":", $isClean);
		$isClean = str_replace("'", "`", $isClean);
		$isClean = str_replace("\"", "``", $isClean);
		$isClean = str_replace("<", "<.", $isClean);
		return trim($isClean);
	}

	//----------------------------------------------------------------------------------------------
	//.	Take tags out of HTML
	//----------------------------------------------------------------------------------------------
	//arg: someText - text which may contain HTML tags [string]
	//returns: text without HTML tags [string]

	function stripHTML($someText) { 
		return preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $someText);
	}

	//----------------------------------------------------------------------------------------------
	//.	remove leading and trailing whitespace, line breaks and paragraphs from HTML
	//----------------------------------------------------------------------------------------------
	//arg: html - cleaned HTML [string]

	function trimHtml($html) {
		$continue = true;
		$remove = array('<br/>', '<br>', '<p></p>', '&nbsp;');		

		while (true == $continue) {
			$continue = false;
			$html = trim($html);
			foreach($remove as $find) {
				if (mb_substr($html, 0, mb_strlen($find)) == $find) {
					$html = mb_substr($html, mb_strlen($find));
					$continue = true;
				}

				if (mb_substr($html, (0 - mb_strlen($find))) == $find) {
					$html = mb_substr($html, 0, (mb_strlen($html) - mb_strlen($find)));
					$continue = true;
				}
			}
		}

		return $html;
	}

	//==============================================================================================
	//	TEXT FORMATTING
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	remove all but alphanumeric characters from a string (allow specified others)
	//----------------------------------------------------------------------------------------------
	//arg: txt - text to clean [string]
	//opt: allow - string of chars to allow [
	//returns: only characters 0-9, a-z, A-Z
	
	function makeAlphaNumeric($txt, $allow = '') {
		$clean = '';
		$numChars = mb_strlen($txt);											// loop invariant
		$numAllow = mb_strlen($allow);
		for($i = 0; $i < $numChars; $i++) {
			$currChar = mb_substr($txt, $i, 1);
			$oCC = ord($currChar);
		
			if (($oCC >= 48) AND ($oCC <= 57)) { $clean .= $currChar; } 	// 0-9
			if (($oCC >= 97) AND ($oCC <= 122)) { $clean .= $currChar; } 	// a-z
			if (($oCC >= 65) AND ($oCC <= 90)) { $clean .= $currChar; } 	// A-Z

			if ($numAllow > 0) {
				for($j = 0; $j < $numAllow; $j++) {						// specified by caller
					$allowChar = mb_substr($allow, $j, 1);
					if ($currChar == $allowChar) { $clean .= $allowChar; }
				}
			}
		}
	
		return $clean;														// done
	}

	//----------------------------------------------------------------------------------------------
	//.	convert a 2d array into a simple XML document
	//----------------------------------------------------------------------------------------------
	//arg: rootType - name of root entity [string]
	//arg: members - array of type => value [array]
	//opt: docType - add XML declaration [bool]
	//returns: XML document [string]

	function arrayToXml2d($rootType, $members, $docType = false) {
		$xml = "<" . $rootType. ">\n";
		foreach($members as $type => $value) { $xml .= "\t<$type>$value</$type>\n"; }
		$xml .= "</" . $rootType . ">\n";
		if (true == $docType) { $xml = "<?xml version=\"1.0\" ?>\n\n" . $xml; }
		return $xml;
	}

	//----------------------------------------------------------------------------------------------
	//.	remove all but alphanumeric characters from a string (allow specified others)
	//----------------------------------------------------------------------------------------------
	//arg: txt - plain text [string]
	//arg: html - html equivalent [string]

	function txtToHtml($txt) {
		$html = str_replace('<', '&lt;', $txt);
		$html = str_replace('>', '&gt;', $html);
		$html = str_replace("\r", '', $html);
		$html = str_replace("\n", "<br/>\n", $html);
		return $html;
	}

	//----------------------------------------------------------------------------------------------
	// 	convert to base64 (TODO: make this more efficient)
	//----------------------------------------------------------------------------------------------
	//arg: txt - text which may contain escaped entities [string]
	//returns: same string, with entities replaced [string]

	function addHtmlEntities($txt) {
		//$txt = str_replace('&', '&amp;', $txt);
		//$txt = str_replace('<', '&lt;', $txt);
		//$txt = str_replace('>', '&gt;', $txt);

		//TODO: redo this

		return $txt;
	}

	//----------------------------------------------------------------------------------------------
	// 	convert to base64 (TODO: make this more efficient)
	//----------------------------------------------------------------------------------------------
	//arg: txt - text which may contain escaped entities [string]
	//returns: same string, with entities replaced [string]

	function removeHtmlEntities($txt) {
		//$txt = str_replace('&lt;', '<', $txt);
		//$txt = str_replace('&gt;', '>', $txt);
		//$txt = str_replace('&amp;', '&', $txt);

		//TODO: redo this

		return $txt;
	}

	//----------------------------------------------------------------------------------------------
	// 	convert to base64 and word wrap 
	//----------------------------------------------------------------------------------------------
	//arg: txt - text to convert to base 64 [string]
	//opt: width - max line width - default is 80 [int]
	//returns: wrapped base 64  [string]

	function b64wrap($txt, $width = 80) {
		$txt = base64_encode($txt);
		$txt = wordwrap($txt, $width, "\n", true);
		return $txt;
	}

	//----------------------------------------------------------------------------------------------
	// 	convert to base64 (TODO: make this more efficient)
	//----------------------------------------------------------------------------------------------
	//arg: varName - JavaScript variable name [string]
	//arg: text - value of javascript variable [string]
	//opt: scriptTags - wrap in html script tags if true [bool]
	//returns: code for javascript variable assignment [string]

	function base64EncodeJs($varName, $text, $scriptTags = true) {
		$b64 = base64_encode($text);										// encode
		$b64 = wordwrap($b64, 80, "\n", true);								// break into lines, 80c
		$break = "\"\n" . str_repeat(' ', mb_strlen($varName) + 5) . "+ \"";	// indent pattern
		$b64 = str_replace("\n", $break, $b64);								// apply pattern
		$b64 = "var $varName = \"" . $b64 . "\";\n";						// add js varname
		if (true == $scriptTags) 
			{ $b64 = "<script language='Javascript'>\n" . $b64 . "</script>\n"; }
		return $b64;														// done
	}

	//----------------------------------------------------------------------------------------------
	//.	get a mb_substring delimited by start and end strings
	//----------------------------------------------------------------------------------------------
	//arg: start - string begins with [string]
	//arg: end - string ends with [string]

	function strdelim($str, $start, $end) {
		$str = ' ' . $str;
		$mb_substr = '';
		$startPos = mb_strpos($str, $start);
		if (false != $startPos) {
			$startPos = $startPos + mb_strlen($start);
			$endPos = mb_strpos($str, $end, $startPos);
			if (false != $endPos) { $mb_substr = mb_substr($str, $startPos, $endPos - $startPos); }
		}
		return $mb_substr;
	}

	//----------------------------------------------------------------------------------------------
	//.	mark up html for injection via javascript (deprecated)
	//----------------------------------------------------------------------------------------------
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

	//----------------------------------------------------------------------------------------------
	//.	convert file size in bytes to approximate summary size
	//----------------------------------------------------------------------------------------------
	//arg: bytes - size of something in bytes [int]

	function printFileSize($bytes) {
		$txt = (string)$bytes . ' bytes';

		$magnitude = 1024;
		if ($bytes > $magnitude) { $txt = (string)floor(($bytes / $magnitude)) . 'kb'; }

		$magnitude *= 1024;
		if ($bytes > $magnitude) { $txt = (string)floor(($bytes / $magnitude)) . 'mb'; }

		$magnitude *= 1024;
		if ($bytes > $magnitude) { $txt = (string)floor(($bytes / $magnitude)) . 'gb'; }

		return $txt;
	}

	//==============================================================================================
	//	network IO
	//==============================================================================================

	//--------------------------------------------------------------------------------------------------
	//|	HTTP GET a URL using cURL, respecting kapenta settings for proxy, etc
	//--------------------------------------------------------------------------------------------------
	//: TODO - attempt other download methods (wget, file wrapper, etc) if curl not present
	//: TODO - implement password for systems which use older HTTP authentication methods
	//: TODO - handle HTTP error codes
	//arg: url - a URL [string]
	//opt: password - reserved for HTTP/1.x credentials, not implemented [string]
	//opt: headers - set to true to return HTTP headers [string]
	//opt: cookie - cookie string to use for this request [string]
	//returns: result of HTTP GET request, false if no cURL [string]

	function curlGet($url, $password = '', $headers = false, $cookie = '') {
		global $kapenta;
	
		if (false == function_exists('curl_init')) { return false; }	// is cURL installed?

		//------------------------------------------------------------------------------------------
		//	create baisc cURL HTTP GET request
		//------------------------------------------------------------------------------------------
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$interface = $kapenta->hostInterface;
		if ('' != $interface) { curl_setopt($ch, CURLOPT_INTERFACE, $interface); }
		if (true == $headers) { curl_setopt($ch, CURLOPT_HEADER, true); }
		if ('' != $cookie) { curl_setopt($ch, CURLOPT_COOKIE, $cookie); }

		//------------------------------------------------------------------------------------------
		//	use HTTP proxy if enabled
		//------------------------------------------------------------------------------------------
		if ('yes' == $kapenta->proxyEnabled) {
			$credentials = $kapenta->proxyUser . ':' . $kapenta->proxyPass;
			curl_setopt($ch, CURLOPT_PROXY, $kapenta->proxyAddress);
			curl_setopt($ch, CURLOPT_PROXYPORT, $kapenta->proxyPort);
			curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
			if (trim($credentials) != ':') {
				curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
				curl_setopt($ch, CURLOPT_PROXYUSERPWD, $credentials);
			}
		}

		//------------------------------------------------------------------------------------------
		//	return result
		//------------------------------------------------------------------------------------------
		$result = curl_exec($ch);
		return $result;
	}

	//--------------------------------------------------------------------------------------------------
	//|	HTTP GET a URL using cURL, respecting kapenta settings for proxy, etc
	//--------------------------------------------------------------------------------------------------
	//: TODO - attempt other download methods (wget, file wrapper, etc) if curl not present
	//: TODO - implement password for systems which use older HTTP authentication methods
	//: TODO - handle HTTP error codes
	//arg: url - a URL [string]
	//arg: postvars - array of key => value pairs [array]
	//opt: headers - set to true to return HTTP headers [string]
	//opt: cookie - cookie string to use for this request [string]
	//returns: result of HTTP GET request, false if no cURL [string]

	function curlPost($url, $postvars, $headers = false, $cookie = '', $headerArr = NULL) {
		global $kapenta;
	
		if (false == function_exists('curl_init')) { 
			$kapenta->session->msgAdmin('Could not initialize cURL');
			return false; 
		}	// is cURL installed?

		//------------------------------------------------------------------------------------------
		//	create baisc cURL HTTP POST request
		//------------------------------------------------------------------------------------------
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
		
		$interface = $kapenta->hostInterface;
		if ('' != $interface) { curl_setopt($ch, CURLOPT_INTERFACE, $interface); }
		if (true == $headers) { curl_setopt($ch, CURLOPT_HEADER, true); }
		if ('' != $cookie) { curl_setopt($ch, CURLOPT_COOKIE, $cookie); }

		//------------------------------------------------------------------------------------------
		//	use HTTP proxy if enabled
		//------------------------------------------------------------------------------------------
		if ('yes' == $kapenta->proxyEnabled) {
			$kapenta->session->msgAdmin('Using proxy: ' . $kapenta->proxyAddress);
			$credentials = $kapenta->proxyUser . ':' . $kapenta->proxyPass;
			curl_setopt($ch, CURLOPT_PROXY, $kapenta->proxyAddress);
			curl_setopt($ch, CURLOPT_PROXYPORT, $kapenta->proxyPort);
			curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
			if (trim($credentials) != ':') {
				curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
				curl_setopt($ch, CURLOPT_PROXYUSERPWD, $credentials);
			}
		}

		//------------------------------------------------------------------------------------------
		//	add to request header
		//------------------------------------------------------------------------------------------
		if (NULL !== $headerArr) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArr);
		}

		//------------------------------------------------------------------------------------------
		//	return result
		//------------------------------------------------------------------------------------------
		//echo "Making curl request: $url<br/>";
		$result = curl_exec($ch);
		//echo "Result: " . strlen($result) . "bytes<br/>";
		return $result;
	}

	//----------------------------------------------------------------------------------------------
	//	note when a method has been deprecated
	//----------------------------------------------------------------------------------------------

	function noteDeprecated($component, $method) {
		global $session;

		$this->maxDeprecatedNotices--;
		if (0 <= $this->maxDeprecatedNotices) { return; }

		$session->msgAdmin('Deprecated: ' . $component . '::' . $method, 'bad');
		//echo 'Deprecated: ' . $component . '::' . $method . "<br/>\n";
		//echo "<small>";
		//debug_print_backtrace();
		//echo "</small><br/>\n";
	}

}

?>
