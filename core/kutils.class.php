<?

//--------------------------------------------------------------------------------------------------
//*	utility object, performs miscellaneous tasks
//--------------------------------------------------------------------------------------------------

class KUtils {

	//----------------------------------------------------------------------------------------------
	//.	make a random number
	//----------------------------------------------------------------------------------------------
	//opt: min - bottom of range [float]
	//opt: max - top of range [float]
	//: I don't think this is needed with PHP5
	//returns: a random number (0-1) [float]

	function random($min = 0, $max = 1) {
		srand(make_seed());
		return rand($min, $max);
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
	//.	remove all but alphanumeric characters from a string (allow specified others)
	//----------------------------------------------------------------------------------------------
	//arg: txt - text to clean [string]
	//opt: allow - string of chars to allow [
	//returns: only characters 0-9, a-z, A-Z
	
	function makeAlphaNumeric($txt, $allow = '') {
		$clean = '';
		$numChars = strlen($txt);											// loop invariant
		$numAllow = strlen($allow);
		for($i = 0; $i < $numChars; $i++) {
			$currChar = substr($txt, $i, 1);
			$oCC = ord($currChar);
		
			if (($oCC >= 48) AND ($oCC <= 57)) { $clean .= $currChar; } 	// 0-9
			if (($oCC >= 97) AND ($oCC <= 122)) { $clean .= $currChar; } 	// a-z
			if (($oCC >= 65) AND ($oCC <= 90)) { $clean .= $currChar; } 	// A-Z

			if ($numAllow > 0) {
				for($j = 0; $j < $numAllow; $j++) {						// specified by caller
					$allowChar = substr($allow, $j, 1);
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
		$txt = str_replace('&', '&amp;', $txt);
		$txt = str_replace('<', '&lt;', $txt);
		$txt = str_replace('>', '&gt;', $txt);
		return $txt;
	}

	//----------------------------------------------------------------------------------------------
	// 	convert to base64 (TODO: make this more efficient)
	//----------------------------------------------------------------------------------------------
	//arg: txt - text which may contain escaped entities [string]
	//returns: same string, with entities replaced [string]

	function removeHtmlEntities($txt) {
		$txt = str_replace('&lt;', '<', $txt);
		$txt = str_replace('&gt;', '>', $txt);
		$txt = str_replace('&amp;', '&', $txt);
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
		$break = "\"\n" . str_repeat(' ', strlen($varName) + 5) . "+ \"";	// indent pattern
		$b64 = str_replace("\n", $break, $b64);								// apply pattern
		$b64 = "var $varName = \"" . $b64 . "\";\n";						// add js varname
		if (true == $scriptTags) 
			{ $b64 = "<script language='Javascript'>\n" . $b64 . "</script>\n"; }
		return $b64;														// done
	}

	//--------------------------------------------------------------------------------------------------
	//|	HTTP GET a URL using cURL, respecting kapenta settings for proxy, etc
	//--------------------------------------------------------------------------------------------------
	//: TODO - attempt other download methods (wget, file wrapper, etc) if curl not present
	//: TODO - implement password for systems which use older HTTP authentication methods
	//: TODO - handle HTTP error codes
	//: TODO - make POST version of this method
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
	
		if (function_exists('curl_init') == false) { return false; }	// is cURL installed?

		//temporary DNS failure
		$url = str_replace('http://www.kapenta.co.uk', 'http://89.145.97.133', $url);
		$url = str_replace('http://kapenta.co.uk', 'http://89.145.97.133', $url);

		//------------------------------------------------------------------------------------------
		//	create baisc cURL HTTP GET request
		//------------------------------------------------------------------------------------------
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		if ('' != $hostInterface) { curl_setopt($ch, CURLOPT_INTERFACE, $hostInterface); }

		//------------------------------------------------------------------------------------------
		//	use HTTP proxy if enabled
		//------------------------------------------------------------------------------------------
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

		if (strpos($url, '89.145.97.133') != false) { 
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Host: www.kapenta.co.uk'));
		}

		//------------------------------------------------------------------------------------------
		//	return result
		//------------------------------------------------------------------------------------------
		$result = curl_exec($ch);
		return $result;
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

}

?>
