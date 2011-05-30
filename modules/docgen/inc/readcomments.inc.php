<?

//-------------------------------------------------------------------------------------------------
//*	read comments in source code to extract documentation
//-------------------------------------------------------------------------------------------------
//+	syntax - comment begins with
//+	* file summary
//+	+ explanation (file)
//+	| function summary
//+	: explanation (function)
//+ . class method summary
//+ , explanation (class method)
//+	arg: mandatory argument - description
//+	opt: optional argument - description
//+	returns: return value of the function

function docRead($fileName) {
	global $theme;
	if (false == file_exists($fileName)) { return false; }

	//---------------------------------------------------------------------------------------------

	$file = array();
	$file['summary'] = array();
	$file['desc'] = array();
	$file['functions'] = array();

	$currentFn = array();
	$currentFn['summary'] = array();
	$currentFn['arg'] = array();
	$currentFn['opt'] = array();
	$currentFn['desc'] = array();

	$raw = implode(file($fileName));
	$lines = explode("\n", $raw);

	foreach ($lines as $line) {
		$line = trim($line);
		if ((strlen($line) >= 3) && (substr($line, 0, 2) == '//')) {
			$rest = substr($line, 3);
			switch (substr($line, 0, 3)) {
				case '//*':	$file['summary'][] = $rest; 		break;	// file summary
				case '//+':	$file['desc'][] = $rest; 			break;	// file description
				case '//|':	$currentFn['summary'][] = $rest; 	break;	// function summary
				case '//.':	$currentFn['summary'][] = $rest; 	break;	// function summary
				case '//:':	$currentFn['desc'][] = $rest; 		break;	// function description
				case '//,':	$currentFn['desc'][] = $rest; 		break;	// function description
				case '//a':	$currentFn['arg'][] = 'a' . $rest; 	break;	// mandatory argument
				case '//o':	$currentFn['arg'][] = 'o' . $rest; 	break;	// optional argument
			}

		}

		//-----------------------------------------------------------------------------------------
		//	look for function declarations
		//-----------------------------------------------------------------------------------------
		if (substr($line, 0, 8) == 'function') {

			//-------------------------------------------------------------------------------------
			//	read function definition
			//-------------------------------------------------------------------------------------
			$fnDef = docReadFnDef($line);
			$currentFn['name'] = $fnDef['name'];
	
			//-------------------------------------------------------------------------------------
			//	look for undocumented parameters
			//-------------------------------------------------------------------------------------
			foreach($fnDef['arg'] as $param) {
				$found = false;
				foreach($currentFn['arg'] as $arg) {
					$arg = trim(substr($arg, 4));		//	get rid of the arg: part
					$parts = explode("-", $arg, 2);	//	break at '-'
					if (trim($parts[0]) == $param) { $found = true; }
				}

				if ((false == $found) && ($param != 'args')) { // because of views, etc
					$currentFn['arg'][] = 'arg: ' . $param . " - <span class='ajaxerror'>(undocumented)</span>";
				}
			}

			//-------------------------------------------------------------------------------------
			//	add to file array
			//-------------------------------------------------------------------------------------
			$file['functions'][$fnDef['name']] = $currentFn;

			$currentFn = array();
			$currentFn['summary'] = array();
			$currentFn['arg'] = array();
			$currentFn['opt'] = array();
			$currentFn['desc'] = array();

		}

	}

	return $file;

}

//-------------------------------------------------------------------------------------------------
//|	read function definition (name and arguments)
//-------------------------------------------------------------------------------------------------
//arg: line - line containing function definition [string]
//returns: array containing a description of the function [array]

function docReadFnDef($line) {
	$def = array();

	$line = substr($line, 8);
	$startPos = strpos($line, "(");
	$endPos = strpos($line, "{");
	if (false == $endPos) { $endPos = strlen($line); }

	$def['name'] = trim(substr($line, 0, $startPos));
	$def['paren'] = trim(substr($line, $startPos, $endPos - $startPos));
	$def['arg'] = array();
	$def['opt'] = array();

	// 
	$paren = str_replace('(', '', $def['paren']);
	$paren = str_replace(')', '', $paren);
	$parts = explode(",", $paren);

	foreach($parts as $part) {
		if (strlen(trim($part)) > 0) {			// not empty list of parameters
			$part = trim($part);
			$eqPos = strpos($part, '=');
			if ($eqPos != false) {
				// optional argument
				$part = trim(substr($part, 0, $eqPos));
				$def['arg'][] = str_replace('$', '', $part);
			} else {
				$def['arg'][] = str_replace('$', '', $part);
			}		
		}	
	}

	return $def;
}

//-------------------------------------------------------------------------------------------------
//|	make argument tables for a function
//-------------------------------------------------------------------------------------------------
//arg: fn - function array produced by docRead [array]
//returns: html [string]

function docMakeArgTable($fn) {
	global $req, $theme;

	$html = '';
	$args = array();	
	$args[] = array('Argument', 'Label', 'Required', 'Type');

	$all = array_merge($fn['arg'], $fn['opt']);

	foreach($all as $line) {
			$req = substr($line, 0, 4);
			if ($req == 'arg:') { $req = "yes"; }
			if ($req == 'opt:') { $req = "no"; }

			$line = trim(substr($line, 4));
			$parts = explode("-", $line, 2);
			$type = docGetType($line);
			$args[] = array($parts[0], $parts[1], $req, $type);
	}

	if (count($all) > 0) {
		$html = $theme->arrayToHtmlTable($args, true, true);
	} else {
		$html = "No Parameters.";
	}

	return $html;
}

//-------------------------------------------------------------------------------------------------
//|	look for variable type tags in current line
//-------------------------------------------------------------------------------------------------
//arg: line - a line of PHP code [string]
//returns: list of types [string]

function docGetType($line) {
	$types = array('none', 'bool', 'string', 'array', 'object', 'int', 'float');
	$found = array();
	foreach($types as $type) 
		{ if (strpos($line, '[' . $type . ']') != false) { $found[] = $type; } }

	if (count($found) > 0) { return implode(', ', $found); }
	else { return "<span class='ajaxerror'>unknown</span>"; }
}

//-------------------------------------------------------------------------------------------------
//|	make an example block for a view
//-------------------------------------------------------------------------------------------------
//arg: fn - function array produced by docRead [array]
//returns: html [string]

function docMakeBlockExample($module, $view, $fn) {
	$parts = array();
	$smallSp = "<small><small><small> </small></small></small>";
	foreach($fn['arg'] as $arg) { $parts[] = docGetParamName($arg) . '=param' . $smallSp; }
	foreach($fn['opt'] as $opt) { $parts[] = docGetParamName($opt) . '=optional ' . $smallSp; }
	$html = '[%%delme%%[:' . $module . '::' . $view . '::' . implode('::', $parts) . ':]]';
	$html = str_replace(":::", ":", $html);
	return $html;
}

//-------------------------------------------------------------------------------------------------
//|	get the name of the parameter from an arg or opt descriptopm
//-------------------------------------------------------------------------------------------------
//arg: line - comment dexcribing the argument
//returns: parameter name [string]

function docGetParamName($line) {
	$line = str_replace('//', '', $line);
	$line = str_replace('arg:', '', $line);
	$line = str_replace('opt:', '', $line);
	$parts = explode('-', $line, 2);
	return trim($parts[0]);
}

?>
