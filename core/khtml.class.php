<?

//--------------------------------------------------------------------------------------------------
//*	object for parsing HTML and removing all but a subset of tags
//--------------------------------------------------------------------------------------------------

class KHTMLParser {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------
	var $html;						//_	raw HTML [string]
	var $output = '';				//_ clean html [string]
	var $tagType = '';				//_	type of tag currently being processed [string]
	var $hangingEq = false;			//_	if the last token was an equals sign [bool]
	var $selfClose = false;			//_	if this is a self-closing tag [bool]
	var $tagAtName;					//_	attributes of current tag [array]
	var $tagAtVal;					//_	attribute values of current tag [array]
	var $discard = false;			//_	set when discarding scripts, styleheets, etc [bool]
	var $debug = false;				//_	determines if we're in debug mode or not [bool]
	var $log = '';					//_	debug log [string]

	var $allowTags;					//_	set of permitted tags [array]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: html - html to parse [string]
	//opt: debug - set to true to enable debug mode [string]

	function KHTMLParser($html = '', $debug = false) {
		global $registry;
		$this->html = $html;
		$this->tagAtName = array();
		$this->tagAtVal = array();
		$this->debug = $debug;

		//------------------------------------------------------------------------------------------
		//	set list of allowed HTML tags
		//------------------------------------------------------------------------------------------
		$default = 'a|h1|h2|h3|h4|h5|p|br|b|i|u|ul|ol|li|span|small|table|tbody|th|td|tr|div';
		$allowed = $registry->get('kapenta.htmlparser.allowtags');

		if ('' == $allowed) {
			$registry->set('kapenta.htmlparser.allowtags', $default);
			$allowed = $default;
		}

		$this->allowTags = explode('|', $allowed);

		//------------------------------------------------------------------------------------------
		//	parse
		//------------------------------------------------------------------------------------------
		$this->parseTags();
	}

	//----------------------------------------------------------------------------------------------
	//.	parse raw string for html tags
	//----------------------------------------------------------------------------------------------

	function parseTags() {

		//------------------------------------------------------------------------------------------
		//	working variables
		//------------------------------------------------------------------------------------------
		$thisChar = '';						//%	current char being examined [string]
		$nextChar = '';						//%	next char to be examined, if any [string]
		$thisHtmlCharNo = 0;				//%	char position we're scanning from [int]
		$buffer = '';						//%	piece of the document being worked on [string]
		$mode = 'outside';					//%	state of state machine [string]
		$len = strlen($this->html);			//%	length of input HTML [int]

		//------------------------------------------------------------------------------------------
		//	consider each character in source HTML
		//------------------------------------------------------------------------------------------
		for ($thisHtmlCharNo = 0; $thisHtmlCharNo < $len; $thisHtmlCharNo++) {
			$thisChar = substr($this->html, $thisHtmlCharNo, 1);			// current char
			$nextChar = '';
			if (($thisHtmlCharNo + 1) < $len) { 				
				$nextChar = substr($this->html, $thisHtmlCharNo + 1, 1);	// next char, if any
			}

			//--------------------------------------------------------------------------------------
			// change states (only pay attention to opening of tags if not already in one)
			//--------------------------------------------------------------------------------------
			switch ($mode) {
				case 'outside':
					//------------------------------------------------------------------------------
					//	not inside an html tag change state when we encounter '<'
					//------------------------------------------------------------------------------
					if ('<' == $thisChar) {							// start of a tag
						$this->throwToken($buffer, 'outside');		// throw anything in buffer
						$buffer = '';								// clear the buffer
						$mode = 'tag';								// change mode
						$thisHtmlCharNo--;							// reprocess this in tag mode

					} else {
						switch ($thisChar) {						// disallow quotes
							case "'":	$thisChar = "&apos;";	break;
							case "\"":	$thisChar = "&quot;";	break;
						}
						$buffer .= $thisChar;				// add current char to buffer
					}
					break;	// .....................................................................

				case 'tag':
					//------------------------------------------------------------------------------
					//	inside an html tag, look for whitespace, tokens, strings and '>'
					//------------------------------------------------------------------------------
					switch($thisChar) {
						case ' ':	$this->throwToken(' ', 'ws');		break;	// throw whitespace
						case "\t":	$this->throwToken("\t", 'ws');		break;	// ...
						case "\n":	$this->throwToken("\n", 'ws');		break;
						case "\r":	$this->throwToken("\r", 'ws');		break;
						case '<':	$this->throwToken("<", 'start');	break;	// throw control
						case '=':	$this->throwToken("=", 'equals');	break;	// chars

						case '/':
							if ('>' == $nextChar) {					// end of self closing tag
								$this->throwToken($thisChar . $nextChar, 'endsc');
								$thisHtmlCharNo++;					// skip the next char									
								$mode = 'outside';					// done with this tag

							} else {								// start of token
								$mode = 'token';					// change to token mode
								$thisHtmlCharNo--;					// reprocess this in token mode
							}
							break;

						case '>':									// end of this tag
							$this->throwToken(">", 'end');			// throw it
							$mode = 'outside';						// change mode
							break;							

						case "'":
							$mode = 'sq';							// change mode
							$thisHtmlCharNo--;						// reprocess this in sq mode
							break;

						case "\"":
							$mode = 'dq';							// change mode
							$thisHtmlCharNo--;						// reprocess this in dq mode
							break;	

						default:
							$mode = 'token';						// change to token mode
							$thisHtmlCharNo--;						// reprocess this in token mode
							break;

					}
					break;	// .....................................................................

				case 'sq':
					//------------------------------------------------------------------------------
					//	inside a single quoted string
					//------------------------------------------------------------------------------
					$buffer .= $thisChar;							// add current char to buffer
					if (("'" == $thisChar) && ("'" != $buffer)) {	// end of sq string
						$this->throwToken($buffer, 'sq');			// throw it
						$buffer = '';								// clear the buffer
						$mode = 'tag';								// and change mode back to tag
					}
					break;	// .....................................................................

				case 'dq':
					//------------------------------------------------------------------------------
					//	inside a double quoted string
					//------------------------------------------------------------------------------
					$buffer .= $thisChar;							// add current char to buffer
					if (("\"" == $thisChar) && ("\"" != $buffer)) {	// end of dq string
						$this->throwToken($buffer, 'dq');			// throw it
						$buffer = '';								// clear the buffer
						$mode = 'tag';								// and change mode back to tag
					}
					break;	// .....................................................................

				case 'token':
					//------------------------------------------------------------------------------
					//	inside a tag name, attrib name or unquoted value
					//------------------------------------------------------------------------------
					$endOfToken = false;
					switch ($thisChar) {
						case '=':	$endOfToken = true; break;
						case ' ':	$endOfToken = true; break;
						case "\t":	$endOfToken = true; break;
						case "\n":	$endOfToken = true; break;
						case "\r":	$endOfToken = true; break;
						case ">":	$endOfToken = true; break;
						case '/':	if ('>' == $nextChar) { $endOfToken = true; }	break;
					}

					if (true == $endOfToken) {
						$this->throwToken($buffer, 'token');		// throw it
						$buffer = '';								// clear the buffer
						$mode = 'tag';								// go back to tag mode
						$thisHtmlCharNo--;							// reprocess this in tag mode

					} else { $buffer .= $thisChar; }				// still within token

					break;	// .....................................................................

			} // end switch

		} // end for each char

		//------------------------------------------------------------------------------------------
		//	throw whatever is left in the buffer, assume it's outside
		//------------------------------------------------------------------------------------------
		$this->throwToken($buffer, 'outside');

	} // end this.parseTags

	//----------------------------------------------------------------------------------------------
	//.	catch thrown tokens and evaluate
	//----------------------------------------------------------------------------------------------
	//arg: tkVal - token value / plaintext [string]
	//arg: tkType - token type [string]

	function throwToken ($tkVal, $tkType) {
		$this->htmlLog("token: " . $tkVal . " type: " . $tkType);
		switch($tkType) {
			case 'outside':												// not an html tag part
				if (false == $this->discard) { $this->output .= $tkVal; }
				break;	// .....................................................................

			case 'start':
				$this->tagType = '';									// clear all working vars
				$this->hangingEq = false;		
				$this->selfClose = false;
				$this->tagAtName = array();	
				$this->tagAtVal = array();
				break;

			case 'equals':												// separates k,v pairs
				$this->hangingEq = true;
				break;	// .....................................................................

			case 'token':
				if ('' == $this->tagType) { $this->tagType = $tkVal; }	// this is the tag name
				else {													// this is attrib or value
					if (true == $this->hangingEq) {				
						$tagAtIdx = (count($this->tagAtVal) - 1);		// last to be added
						$this->tagAtVal[$tagAtIdx] = $tkVal;			// this is an attrib value 
						$this->hangingEq = false;						// no longer hanging

					} else {
						$tagAtIdx = count($this->tagAtVal);
						$tkVal = strtolower($tkVal);					// lowercase is tidier
						$this->tagAtName[$tagAtIdx] = $tkVal;			// this is an attrib name
						$this->tagAtVal[$tagAtIdx] = '';				// set blank value
					}
				}
				break;	// .........................................................................

			case 'sq':													// single quoted string
				if (true == $this->hangingEq) {
					$tagAtIdx = (count($this->tagAtVal) - 1);			// last to be added
					$this->tagAtVal[$tagAtIdx] = $tkVal;				// this is an attrib value 
					$this->hangingEq = false;							// no longer hanging
				}	
				break;	// .........................................................................

			case 'dq':													// single quoted string
				if (true == $this->hangingEq) {	
					$tagAtIdx = (count($this->tagAtVal) - 1);			// last to be added
					$this->tagAtVal[$tagAtIdx] = $tkVal;				// this is an attrib value 
					$this->hangingEq = false;							// no longer hanging
				}	
				break;	// .........................................................................

			case 'endsc':
				$this->selfClose = true;
				$this->addTag();
				break;	// .........................................................................

			case 'end':
				$this->addTag();
				break;	// .........................................................................

		} // end switch
	} // end this.throwToken

	//----------------------------------------------------------------------------------------------
	//.	finished with current tag, redact and add it to output
	//----------------------------------------------------------------------------------------------

	function addTag() {
		$allowed = false;							//%	if this tag is allowed [bool]
		$tnLower = strtolower($this->tagType);		//%	for comparison below [string]
		$tagStr = '<' . $this->tagType . ' ';		//%	redacted/rebuilt HTML tag [string]

		if (('script' == $tnLower) && (false == $this->selfClose)) { $this->discard = true; }
		if ('/script' == $tnLower) { $this->discard = false; }
		if (('style' == $tnLower) && (false == $this->selfClose)) { $this->discard = true; }
		if ('/style' == $tnLower) { $this->discard = false; }
		if (('xml' == $tnLower) && (false == $this->selfClose)) { $this->discard = true; }
		if ('/xml' == $tnLower) { $this->discard = false; }

		if (true == in_array(str_replace('/', '', $tnLower), $this->allowTags)) { $allowed = true; }
		if (false == $allowed) { 
			$this->htmlLog("Not adding tag: $tnLower (disallowed type)");
			return false; 
		}

		for ($i = 0; $i < count($this->tagAtName); $i++) { 
			$atName = $this->tagAtName[$i];			//%	attribute name [string]
			$atVal = $this->tagAtVal[$i];			//%	attribute value [string]

			if (true == $this->allowAttrib($tnLower, $atName)) {	// If this attrib is allowed
				$tagStr = $tagStr . $atName;						// add attribute name.
				if ('' != $atVal) {									// If there is a value
					if ('style' == $atName) {						// and this is a 'style' attrib
						$cStyle = $this->cleanStyle($atVal);		// clean the value
						$tagStr = $tagStr .'='. $cleanStyle .' ';	// before adding it.

					} else { $tagStr = $tagStr .'='. $atVal .' '; }	// not 'style', just add it

				} else { $tagStr = $tagStr . ' '; }					// no value, leave a space

			}	
		}
		
		if (true == $this->selfClose) {	$tagStr .= '/>'; }			// img, br, etc
		else { $tagStr = str_replace(' >', '>', $tagStr . '>'); }	// all other tags

		$this->output .= $tagStr;									// we're done, add to output
		$this->log .= "Adding tag: " . htmlentities($tagStr) . "<br/>\n";
	} // end this.addTag


	//----------------------------------------------------------------------------------------------
	//.	discover if an attribute is allowed
	//----------------------------------------------------------------------------------------------
	//arg: tagType - eg, 'img', 'table', 'html' [string]
	//arg: tagType - eg, 'img', 'table', 'html' [string]
	//returns: true is it's allowed, false if not [bool]

	function allowAttrib($tagType, $attribute) {
		$tagType = strtolower($tagType);
		$attribute = strtolower($attribute);
		if ('class' == $attribute) { return true; }	// any tag may have any class

		//--------------------------------------------------------------------------------------
		//	some tags may have specific attributes, eg: a -> href, img -> src
		//--------------------------------------------------------------------------------------
		switch ($tagType) {
			case 'span':
				switch ($attribute) {
					case 'style': 	return true;	break;
				}
				break; // ......................................................................

			case 'a':
				switch ($attribute) {
					case 'href': 	return true;	break;
				}
				break; // ......................................................................

			case 'img':
				switch ($attribute) {
					case 'src': 	return true;	break;
					case 'border': 	return true;	break;
					case 'alt': 	return true;	break;
					case 'style': 	return true;	break;
				}
				break; // ......................................................................

		}

		return false;
	} // end this.allowAttrib

	//---------------------------------------------------------------------------------------------
	//.	classes which are allowed
	//---------------------------------------------------------------------------------------------
	//arg: styleDef - CSS style definition [string]

	function cleanStyle($styleDef) {
		$newStyleDef = '';
		$styleIsAllowed = false;

		$styleDef = str_replace("'", "", $styleDef);
		$styleDef = str_replace("\"", "", $styleDef);
		$styleDef = str_replace("&quot;", "'", $styleDef);
		$styleDef = trim($styleDef);

		$styleLines = explode(";", $styleDef);
		for ($thisStyleLineNo = 0; $thisStyleLineNo < count($styleLines); $thisStyleLineNo++) {
			$thisStyleLine = $styleLines[$thisStyleLineNo];			
			$parts = explode(":", $thisStyleLine);
			$styleIsAllowed = false;
			$styleParamName = $parts[0];
			$styleParamName = strtolower($styleParamName);

			switch($styleParamName) {
				case 'color': 		$styleIsAllowed = true; 	break;
				case 'font-size':	$styleIsAllowed = true; 	break;
			}

			if (true == $styleIsAllowed) {
				$this->htmlLog("styleLine: " . $thisStyleLine . " (OK)");	
				$newStyleDef = $newStyleDef . $thisStyleLine . "; ";
			} else {
				$this->htmlLog("styleLine: " . $thisStyleLine . " (not allowed)");			
			}
		}

		return "\"" . $newStyleDef . "\"";
	}

	//----------------------------------------------------------------------------------------------
	//.	debugging log
	//----------------------------------------------------------------------------------------------
	//arg: msg - line to log [string]

	function htmlLog($msg) {
		if (true == $this->debug) {	$this->log .= $msg . "<br/>\n"; }
	}

}

?>
