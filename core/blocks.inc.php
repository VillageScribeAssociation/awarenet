<?

//--------------------------------------------------------------------------------------------------
//*	functions for loading, executing and displaying blocks (transcluded sections)
//--------------------------------------------------------------------------------------------------

//+	Modules, themes and the kapenta core express a blocks API for dynamicaly transcluding data as a 
//+	page is requested.  Generally, evey section on a page will be expressed as a block.  They have 
//+	the following format:
//+
//+	[[::blockapi::method::argument::]]
//+
//+	For example:
//+
//+	[[::blog::show::23498723484::]]		// generate HTML of blog post UID:23498723484
//+	[[::blog::navlist::10::0::]]		// list the 10 latest blog posts, formatted for nav bar
//+
//+	BlockAPI may be a module, 'theme' for the current theme's api (menus, pagination, etc) and the 
//+	method is a hook defined on that API, with any number of arguments.  Blocks may return HTML or 
//+	text including other blocks which are processed recursively.  One must be careful when 
//+	designing pages not to allow an infinite loop to occur: [[block A]] loads [[block B]] which 
//+	loads [[block A]] again
//+
//+	Access control is provided by the block API itself consulting the users permissions.  Blocks 
//+	may also be used in determining a users level of access by returning data to the permissions 
//+	manager.
//+
//+	Blocks may be cached for the public user to allow faster loading of pages and increase overall 
//+	efficiency.  Some blocks, such as search results, should not be cached.

//--------------------------------------------------------------------------------------------------
//|	load a block template file
//--------------------------------------------------------------------------------------------------
//arg: fileName - relative to installPath [string]

function loadBlock($fileName) {
	global $installPath;
	global $user;
	global $request;
	
	if (file_exists($installPath . $fileName)) {
	  	$raw = implode(file($fileName));
	  	$raw = phpUnComment($raw);

		// special admin option
		if (($user->data['ofGroup'] == 'admin') AND (substr($fileName, 0, 8) == 'modules/')) {
		  if ($request['module'] != 'blocks') {
			$parts = explode('/', $fileName);
			//$raw .= "<small><a href='/blocks/edit/module_" . $parts[1] . '/'
			//     . $parts[2] . "'>[edit block]</a></small>";
		  }
		}

	  	return $raw;

	} else { return false; }
}

//--------------------------------------------------------------------------------------------------
//|	save a block, deprecated TODO: replace with filePutContents
//--------------------------------------------------------------------------------------------------
//arg: fileName - absolute fileName [string]
//arg: raw - file contents [string]

function saveBlock($fileName, $raw) {
	$fh = fopen($fileName, 'w+');
	if ($fh != false) {
		fwrite($fh, "<? /*\n" . trim($raw) . "\n*/ ?>");
		fclose($fh);
	} else { return false; }
}

//--------------------------------------------------------------------------------------------------
//|	expand all blocks within a string
//--------------------------------------------------------------------------------------------------
//arg: txt - text containing block tags to be expanded [string]
//arg: calledBy - newline delimited list of parents, set to empty string [string]
//: calledBy is used to prevent infinite recursion, newline delimited list of parents

function expandBlocks($txt, $calledBy) {

	//----------------------------------------------------------------------------------------------
	//	filter out any calling blocks - prevent infinite recursion
	//----------------------------------------------------------------------------------------------
	$ban = explode("\n", $calledBy);
	foreach($ban as $killThis) {
	  if (strlen($killThis) > 3) {
		$txt = str_replace($killThis, '', $txt);
	  }
	}

	//----------------------------------------------------------------------------------------------
	//	replace each block with result from the appropriate blocks API
	//----------------------------------------------------------------------------------------------
	$blocks = findUniqueBlocks($txt);
	foreach ($blocks as $block) {

		//------------------------------------------------------------------------------------------
		// 	load the appropriate block API and execute the hook
		//------------------------------------------------------------------------------------------
		$ba = blockToArray($block);
		$bHTML = runBlock($ba);

		//------------------------------------------------------------------------------------------
		// 	recurse, expand any blocks that were created by the hook
		//------------------------------------------------------------------------------------------
		$bHTML = expandBlocks($bHTML, $calledBy . $block. "\n");
		$txt = str_replace($block, $bHTML, $txt);

	}

	return $txt;
}

//--------------------------------------------------------------------------------------------------
//|	read block to extract api, method and arguments
//--------------------------------------------------------------------------------------------------
//arg: block - a block tag [string]

function blockToArray($block) {
	global $page;
	$ba = array();

	$block = str_replace("[[:", '', $block);
	$block = str_replace(":]]", '', $block);
	$parts = explode('::', $block);
	if (count($parts) >= 2) {

		//------------------------------------------------------------------------------------------
		//	get the api and method
		//------------------------------------------------------------------------------------------
		$ba['api'] = array_shift($parts);
		$ba['method'] = array_shift($parts);
		$ba['args'] = array();

		//------------------------------------------------------------------------------------------
		//	add page arguments
		//------------------------------------------------------------------------------------------
		foreach($page->blockArgs as $argName => $argValue) {
			$ba['args'][$argName] = $argValue;
		}

		//------------------------------------------------------------------------------------------
		//	get any explicit arguments (overrwrites page args)
		//------------------------------------------------------------------------------------------

		foreach($parts as $part) {
			$eqPos = strpos($part, '=');
			if ($eqPos == false) {
				$ba['args'][$part] = true;
			} else {
				$argName = substr($part, 0, $eqPos);
				$argValue = substr($part, ($eqPos + 1));
				$ba['args'][$argName] = $argValue;
			}
		}

	} else { return false; }

	return $ba;
}

//--------------------------------------------------------------------------------------------------
//|	extract all blocks from a piece of text and return an array
//--------------------------------------------------------------------------------------------------
//arg: txt - text or HTML which may contain block tags [string]

function findUniqueBlocks($txt) {
	$blocks = array();

	$txt = str_replace("\r", '', $txt);		// strip newlines
	$txt = str_replace("\n", '', $txt);

	$txt = str_replace('[[:', "\n[[:", $txt);	// place blocks on their own line
	$txt = str_replace(':]]', ":]]\n", $txt);

	$lines = explode("\n", $txt);			// for each line which might be a block
	foreach($lines as $line) {
	  $line = trim($line);
	  if (strlen($line) > 8) {
		//------------------------------------------------------------------------------------------
		//	if this line begins with [[:: and ends with ::]]
		//------------------------------------------------------------------------------------------
		if ((substr($line, 0, 3) == '[[:') AND (substr(strrev($line), 0, 3) == ']]:')) {
			$blocks[] = $line;
		}

	  }
	}
		
	$blocks = array_unique($blocks);		// prevent looking up the same thing twice
	return $blocks;
}

//--------------------------------------------------------------------------------------------------
//|	a get block API's filename
//--------------------------------------------------------------------------------------------------
//arg: module - module name [string]
//arg: fn - view name [string]

function getBlockApiFile($module, $fn) {
	global $defaultTheme;
	global $installPath;

	$fileName = 'modules/' . $module . '/views/' . $fn . '.fn.php';
	if ($module == 'theme') { $fileName = 'themes/' . $defaultTheme . '/theme.api.php'; }
	
	return $installPath . $fileName;
}

//--------------------------------------------------------------------------------------------------
//|	execute a block
//--------------------------------------------------------------------------------------------------
//arg: ba - block tag data [array]
//: this is quite an old function, from before views were separated into their own files

function runBlock($ba) {
	$apiFile = getBlockApiFile($ba['api'], $ba['method']);
	$fnName = $ba['api'] . '_' . $ba['method'];

	if (file_exists($apiFile)) {
		require_once($apiFile);
		if (function_exists($fnName)) {
			return call_user_func($fnName, ($ba['args']));

		} else { 
			logErr('blocks', 'runBlock', "called function $fnName does not exist in $apiFile"); 

		}
	} else { logErr('blocks', 'runBlock', "api file does not exist: " . $apiFile); 	}	
	return '';
}

//--------------------------------------------------------------------------------------------------
//|	remove blocks from a string (TODO: use a regex)
//--------------------------------------------------------------------------------------------------
//arg: txt - text or HTML which may contain blocks [string]
//: useful for summaries, text snippets, etc

function strip_blocks($txt) {
	$txt = str_replace('<', '{{-less-than-}}', $txt);
	$txt = str_replace('>', '{{-greater-than-}}', $txt);
	$txt = str_replace('[[:', "<blocktag '", $txt);
	$txt = str_replace(':]]', "'>", $txt);
	$txt = strip_tags($txt);
	$txt = str_replace('{{-less-than-}}', '<', $txt);
	$txt = str_replace('{{-greater-than-}}', '>', $txt);
	return $txt;
} 

//--------------------------------------------------------------------------------------------------
//|	substitute an array of values for labels in text
//--------------------------------------------------------------------------------------------------
//arg: labels - array of variable names (keys) and values to replace them with [array]

function replaceLabels($labels, $txt) {
	global $serverPath;
	global $websiteName;

	$labels['serverPath'] = $serverPath;
	$labels['websiteName'] = $websiteName;

	foreach($labels as $label => $val) {
		$txt = str_replace('%%' . $label . '%%', $val, $txt);
	}
	return $txt;
}

?>
