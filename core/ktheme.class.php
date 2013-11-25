<?

//--------------------------------------------------------------------------------------------------
//*	object to allow access to the default theme
//--------------------------------------------------------------------------------------------------

class KTheme {

	//----------------------------------------------------------------------------------------------
	//	member variables
	//----------------------------------------------------------------------------------------------

	var $name;					// name of current theme [string]
	var $loaded = false;		// is set to true if a theme is loaded [bool]

	var $style;					// array of style variables, colors, etc [array]
	var $styleLoaded = false;	// is set to true when stylesheet is loaded [bool]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: theme - name of current theme [string]

	function KTheme($theme = '') {
		global $kapenta;
		$this->style = array();
		if ('' != $theme) { $this->load($theme); }
		else { $this->load($kapenta->defaultTheme); }
	}

	//----------------------------------------------------------------------------------------------
	//.	load a theme
	//----------------------------------------------------------------------------------------------

	function load($theme) {
		$this->name = $theme;
		//$this->readStyle();
		$this->loaded = true;
	}

	//==============================================================================================
	//	BLOCKS
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	load a block template file
	//----------------------------------------------------------------------------------------------
	//arg: fileName - relative to installPath [string]
	//returns: block template, or null string on failure [string]

	function loadBlock($fileName) {
		global $kapenta;
		//global $user;
		//global $session;

		//------------------------------------------------------------------------------------------
		//	check for block customized to this device profile
		//------------------------------------------------------------------------------------------
		$profile = $kapenta->session->get('deviceprofile');
		if ('desktop' != $profile) {
			$altBlock = str_replace('.block.php', '.' . $profile . '.block.php', $fileName);
			if (true == $kapenta->fs->exists($altBlock)) { $fileName = $altBlock; }			
		}

		//------------------------------------------------------------------------------------------
		//	get the file contents
		//------------------------------------------------------------------------------------------
	
		if ($kapenta->fs->exists($fileName)) {
		  	$raw = $kapenta->fs->get($fileName, false, true);
	
			//--------------------------------------------------------------------------------------
			// special admin option TODO: make this a setting
			//--------------------------------------------------------------------------------------
			/*
			if (('admin' == $user->role) AND (mb_substr($fileName, 0, 8) == 'modules/')) {
			  if ($request['module'] != 'blocks') {
				$parts = explode('/', $fileName);
				$raw .= "<small><a href='/blocks/edit/module_" . $parts[1] . '/'
				     . $parts[2] . "'>[edit block]</a></small>";
			  }
			}
			*/

		  	return $raw;

		} else { 
			$msg = 'Could not load requested block file:<br/>' . $fileName;
			$kapenta->session->msgAdmin($msg, 'bad');
			return ''; 
		}
	}


	//----------------------------------------------------------------------------------------------
	//.	substitute an array of values for labels in text
	//----------------------------------------------------------------------------------------------
	//arg: labels - array of variable names (keys) and values to replace them with [array]
	//returns: txt with labels replaced [string]

	function replaceLabels($labels, $txt) {
		global $kapenta;

		$labels['serverPath'] = $kapenta->serverPath;
		$labels['websiteName'] = $kapenta->websiteName;

		if (false == is_array($labels)) { return $txt; }	// no. because no.

		foreach ($labels as $label => $val) { 
			if (false == is_array($val)) { $txt = str_replace('%%' . $label . '%%', $val, $txt); }
		}

		return $txt;
	}

	//--------------------------------------------------------------------------------------------------
	//.	save a block, deprecated
	//--------------------------------------------------------------------------------------------------
	//arg: fileName - relative to installPath [string]
	//arg: raw - file contents [string]
	//returns: true on success, false on failure [bool]

	function saveBlock($fileName, $raw) {
		global $kapenta;
		$result = $kapenta->fs->put($fileName, $raw, false, true);
		return $result;
	}

	//----------------------------------------------------------------------------------------------
	//.	remove blocks from a string
	//----------------------------------------------------------------------------------------------
	//arg: txt - text or HTML which may contain blocks [string]
	//returns: txt without blocks [string]
	//: useful for summaries, text snippets, etc

	function stripBlocks($txt) {
		$txt = str_replace('<', '{{-less-than-}}', $txt);
		$txt = str_replace('>', '{{-greater-than-}}', $txt);
		$txt = str_replace('[[:', "<blocktag '", $txt);
		$txt = str_replace(':]]', "'>", $txt);
		$txt = strip_tags($txt);
		$txt = str_replace('{{-less-than-}}', '<', $txt);
		$txt = str_replace('{{-greater-than-}}', '>', $txt);
		return $txt;
	} 

	//----------------------------------------------------------------------------------------------
	//.	create a clean, short summary of a section of (x)HTML
	//----------------------------------------------------------------------------------------------
	//arg: html - source document to be summarized [string]
	//opt: length - maximum length, default is 300 [int]
	//returns: plaintext [string]

	function makeSummary($html, $length = 300) {
		global $utils;
		$length = (int)$length;

		$html = str_replace(array('<br/>', '<br>', '</p>'), array(' ', ' ', ' '), $html);

		$html = $this->stripBlocks($html);								// remove blocks
		$html = preg_replace("'<[\/\!]*?[^<>]*?>'si", "", $html);		// remove HTML

		$srch = array('<', '>', '&nbsp;');
		$repl = array('&lt;', '&gt;', ' ');

		$html = mb_substr($html, 0, $length) . '...';						// cut down to length
		return $html;
	}

	//----------------------------------------------------------------------------------------------
	//.	execute a block
	//----------------------------------------------------------------------------------------------
	//arg: ba - block tag data [array]
	//returns: block content, usually html, xml or text [string]
	//: this is quite an old function, from before views were separated into their own files

	function runBlock($ba) {
		global $kapenta;

		if (
			(false == array_key_exists('api', $ba)) ||
			(false == array_key_exists('method', $ba))
		) {
			if (false == array_key_exists('rawblock', $ba)) { $ba['rawblock'] = ''; }
			$kapenta->session->msgAdmin("No api or method given: " . $ba['rawblock']);
			return '';
		}

		$apiFile = $this->getBlockApiFile($ba['api'], $ba['method']);
		$fnName = $ba['api'] . '_' . $ba['method'];

		if ($kapenta->fs->exists($apiFile)) {
			require_once($kapenta->installPath . $apiFile);
			if (function_exists($fnName)) {
				//----------------------------------------------------------------------------------
				//	view loaded, run it
				//----------------------------------------------------------------------------------
				$startTime = $kapenta->time();
				$html = call_user_func($fnName, ($ba['args']));
				$endTime = $kapenta->time();

				$diff = ($endTime - $startTime);
				if ($diff > 1) {
					$logMsg = $diff . ' - ' . $ba['args']['rawblock'];
					$kapenta->logEvent('slow-views', 'theme', 'block', $logMsg);
				}

				return $html;

			} else { 
				$msg = "called function $fnName does not exist in $apiFile";
				$kapenta->logErr('blocks', 'runBlock', $msg); 
				$kkapenta->session->msgAdmin($msg);

			}
		} else {
			$msg = "api file does not exist: " . $apiFile;
			$kapenta->logErr('blocks', 'runBlock', $msg);
			//$session->msgAdmin("api file does not exist: " . $apiFile);

		}

		return '';
	}

	//----------------------------------------------------------------------------------------------
	//.	a get block API's filename
	//----------------------------------------------------------------------------------------------
	//arg: module - module name [string]
	//arg: fn - view name [string]
	//returns: filename relative to installPath [string]
	//: this is quite an old function, from before views were separated into their own files

	function getBlockApiFile($module, $fn) {
		global $kapenta;
		$file = 'modules/' . $module . '/views/' . $fn . '.fn.php';
		if ('theme' == $module) 
			{ $file = 'themes/' . $kapenta->defaultTheme . '/views/' . $fn . '.fn.php'; }

		return $file;
	}

	//----------------------------------------------------------------------------------------------
	//.	extract all blocks from a piece of text and return an array
	//----------------------------------------------------------------------------------------------
	//arg: txt - text or HTML which may contain block tags [string]
	//returns: array of blocks [array:string]

	function findUniqueBlocks($txt) {
		$blocks = array();							//% return value [array:string]

		$txt = str_replace("\r", '', $txt);			//	strip newlines
		$txt = str_replace("\n", '', $txt);
	
		$txt = str_replace('[[:', "\n[[:", $txt);	//	place blocks on their own line
		$txt = str_replace(':]]', ":]]\n", $txt);

		$lines = explode("\n", $txt);				//%	[array:string]
		foreach($lines as $line) {					//	for each line which might be a block
		  $line = trim($line);
		  if (mb_strlen($line) > 8) {
			//--------------------------------------------------------------------------------------
			//	if this line begins with [[:: and ends with ::]]
			//--------------------------------------------------------------------------------------
			if ((mb_substr($line, 0, 3) == '[[:') AND (mb_substr(strrev($line), 0, 3) == ']]:')) 
				{ $blocks[] = $line; }

		  }
		}
		
		$blocks = array_unique($blocks);		// prevent looking up the same thing twice
		return $blocks;
	}

	//----------------------------------------------------------------------------------------------
	//.	read block to extract api, method and arguments
	//----------------------------------------------------------------------------------------------
	//arg: block - a block tag [string]
	//returns: block components [array]
	//; TODO: overhaul this

	function blockToArray($block) {
		global $page, $session;
		$ba = array();

		$original = trim($block);
		$block = str_replace("[[:", '', $block);
		$block = str_replace(":]]", '', $block);
		$parts = explode('::', $block);
		if (count($parts) >= 2) {

			//--------------------------------------------------------------------------------------
			//	get the api and method
			//--------------------------------------------------------------------------------------
			$ba['api'] = array_shift($parts);
			$ba['method'] = array_shift($parts);
			$ba['args'] = array();

			//--------------------------------------------------------------------------------------
			//	add page arguments
			//--------------------------------------------------------------------------------------
			if (false == is_array($page->blockArgs)) {
				$page->blockArgs = array();
				$session->msgAdmin("\$page->blockArgs not an array.", 'bug');
			} else {
				foreach($page->blockArgs as $argName => $argValue) {
					$ba['args'][$argName] = $argValue;
				}
			}

			//--------------------------------------------------------------------------------------
			//	get any explicit arguments (overrwrites page args)
			//--------------------------------------------------------------------------------------

			foreach($parts as $part) {
				$eqPos = mb_strpos($part, '=');
				if (false == $eqPos) {
					$ba['args'][$part] = true;
				} else {
					$argName = mb_substr($part, 0, $eqPos);
					$argValue = mb_substr($part, ($eqPos + 1));
					$ba['args'][$argName] = $argValue;
				}
			}

		} else { return false; }

		return $ba;
	}

	//----------------------------------------------------------------------------------------------
	//.	expand all blocks within a string
	//----------------------------------------------------------------------------------------------
	//: calledBy is used to prevent infinite recursion, newline delimited list of parents
	//arg: txt - text containing block tags to be expanded [string]
	//opt: calledBy - newline delimited list of parents, set to empty string [string]
	//opt: area - area of the page this block is on, default is 'content' [string]
	//returns: txt with blocks recusively expanded [string]

	function expandBlocks($txt, $area = 'content') {
		global $page;
		global $session;

		$continue = true;

		if ('mobile' == $session->get('mobile')) {
			switch($area) {
				case 'indent':		$area = 'mobileindent'; 	break;
				case 'content':		$area = 'mobile'; 			break;
				case 'nav1':		$area = 'mobile'; 			break;
				case 'nav2':		$area = 'mobile'; 			break;
			}
		}

		foreach($page->blockArgs as $find => $replace) {
			$txt = str_replace('%%' . $find . '%%', $replace, $txt);
		}

		while (true == $continue) {
			$startPos = mb_strpos($txt, '[[:');
			$endPos = mb_strpos($txt, ':]]', (int)$startPos);

			if ((false === $startPos) || (false === $endPos)) {
				$continue = false;
			} else {

				$endPos = $endPos + 3;
				$block = mb_substr($txt, $startPos, $endPos - $startPos);

				$ba = $this->blockToArray($block);
	
				$ba['args']['area'] = $area;					// spacial magic block argument
				$ba['args']['rawblock'] = $block;				// ... ditto ...

				$result = $this->runBlock($ba);

				$txt = mb_substr($txt, 0, $startPos) . $result . mb_substr($txt, $endPos);
			}
		}

		return $txt;
	}

	/*
	function expandBlocks($txt, $area = 'content') {
		//------------------------------------------------------------------------------------------
		//	filter out any calling blocks - prevent infinite recursion
		//------------------------------------------------------------------------------------------
		$ban = explode("\n", $calledBy);
		foreach($ban as $killThis) 
			{ if (mb_strlen($killThis) > 3) { $txt = str_replace($killThis, '', $txt); } }

		//------------------------------------------------------------------------------------------
		//	replace each block with result from the appropriate blocks API
		//------------------------------------------------------------------------------------------
		$blocks = $this->findUniqueBlocks($txt);
		foreach ($blocks as $block) {

			//--------------------------------------------------------------------------------------
			// 	load the appropriate block API and execute the hook
			//--------------------------------------------------------------------------------------
			$ba = $this->blockToArray($block);
			$bHTML = $this->runBlock($ba);

			//--------------------------------------------------------------------------------------
			// 	recurse, expand any blocks that were created by the hook
			//--------------------------------------------------------------------------------------
			$bHTML = $this->expandBlocks($bHTML, $calledBy . $block . "\n");
			$txt = str_replace($block, $bHTML, $txt);

		}

		return $txt;
	}
	*/

	//==============================================================================================
	//	UTILITY
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	render a 2d array as a table
	//----------------------------------------------------------------------------------------------
	//arg: ary - 2d array of table rows and columns [array]
	//opt: wireframe - use kapenta's default wireframe table style [bool]
	//opt: firstrowtitle - if column titles in first row to be highlighted [bool]
	//returns: HTML table, false on failure [string][bool]
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

	//----------------------------------------------------------------------------------------------
	//.	wrap a snippet of HTML in a content title box / section heading
	//----------------------------------------------------------------------------------------------
	//arg: html - html snippet to wrap in a block [string]
	//arg: title - title of box [string]
	//arg: divId - id of the div to wrap this in [string]
	//opt: toggle - (hide|show|off), default is off [string]

	function tb($html, $title, $divId, $toggle = 'off') {
		$tg = '::toggle=' . $divId;;
		$style = '';

		if ('hide' == $toggle) {
			$tg = '::hidden=yes::toggle=' . $divId;
			$style = " style='visibility: hidden; display: none;'";
		}

		//	 . "::divid=" . $divId . "tb"

		$html = ''
		 . "<div class='block'>\n"
		 . "[[:theme::titlebox"
			 . "::label=$title" . $tg . ":]]\n"
		 . "<div id='$divId'$style>\n"
		 . $html
		 . "</div>\n"
		 . "<div class='foot'></div>\n"
		 . "</div>\n"
		 . "<br/>\n";

		return $html;
	}

	//----------------------------------------------------------------------------------------------
	//.	wrap a snippet of HTML in a nav title box / section heading
	//----------------------------------------------------------------------------------------------
	//arg: html - html snippet to wrap in a block [string]
	//arg: title - title of box [string]
	//arg: divId - id of the div to wrap this in [string]
	//opt: toggle - (hide|show|off), default is off [string]

	function ntb($html, $title, $divId, $toggle = 'off') {

		$style = '';

		$tg = '';
		if ('show' == $toggle) { $tg = '::toggle=' . $divId; }
		if ('hide' == $toggle) {
			$tg = '::hidden=yes::toggle=' . $divId;
			$style = "style='display: none;'";
		}

		$html = ''
		 . "<div class='block'>\n"
		 . "[[:theme::navtitlebox::label=$title" . $tg . ":]]\n"
		 . "<div id='$divId' $style>\n"
		 . $html
		 . "</div>\n"
		 . "<div class='foot'></div>\n"
		 . "</div>\n"
		 . "<br/>\n";

		return $html;
	}

	//==============================================================================================
	//	TAGS
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	convert a set of tags from array to HTML representation
	//----------------------------------------------------------------------------------------------
	//arg: tags - 2d array of name, weight, link [array]
	//note: we don't show tags with weight/magnitude less than 1

	function makeTagCloud($tags) {
		$html = '';		//%	return value
		$max = 1; 
		$min = 1;	

		foreach($tags as $tag) { if ($tag['weight'] > $max) { $max = $tag['weight']; } }

		foreach($tags as $tag) {
			$size = floor((5 / $max) * $tag['weight']);
			$html .= ''
			 . "<a href=\"" . $tag['link'] . "\" style='color: #444444;'>"
			 . "<font size='" . $size . "'>" . $tag['name'] . "</font>"
			 . "</a>\n";
		}

		return $html;
	}

	//==============================================================================================
	//	STYLES
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	load theme style variables into an array (usually attached to page object)
	//----------------------------------------------------------------------------------------------
	//arg: theme - theme name [string]
	//returns: theme stylesheet as associative array, or false on failure [array][bool]

	function readStyle() {
		$xmlFile = 'themes/' . $this->name . '/style.xml.php';
		$xmlDoc = new KXmlDocument($xmlFile, true);
		if (false == $xmlDoc->loaded) { return false; }
		$ary = $xmlDoc->getChildren2d();
		$this->style = $ary;
		$this->styleLoaded = true;
		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//.	save an array of style variables
	//----------------------------------------------------------------------------------------------
	//arg: theme - name of theme [string]
	//arg: style - theme stylesheet; associative array [array]
	//returns: true on success, else false [bool]
	//: TODO: use filePutContents

	function writeStyle() {
		global $kapenta, $utils;
		if (false == $kapenta->themeExists($this->name)) { return false; }

		$xml = $utils->arrayToXml2d('theme', $style, true);					// construct XML
		return $kapenta->filePutContents($fileName, $xml, false, true);		// save to file
	}

}

?>
