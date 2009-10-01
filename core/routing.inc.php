<?

//--------------------------------------------------------------------------------------------------
//	functions used for routing
//--------------------------------------------------------------------------------------------------

//	Example requests:
//
//	http://somewhere.com/module/action/reference/argument1_value/argument2_value
//	|-- ignored --------| 
//
//	http://somewhere.com/module/action/argument_value/reference
//	|-- ignored --------| 
//
//	The format of arguments is that they contain an underscore
//	Everything except argument values are converted to lower case
//	An argument value can contain underscores
//

//--------------------------------------------------------------------------------------------------
//	list modules (installed or otherwise)
//	Note that static data should be used on production sites for a slight performance increase.
//--------------------------------------------------------------------------------------------------

function listModules() {
	global $installPath;
	$modList = array();

	$d = dir($installPath . 'modules/');
	while (false !== ($entry = $d->read())) {
	  if (($entry != '.') AND ($entry != '..')) {
		$entry = strtolower($entry);
		$modList[] = $entry;
	  }
	}
	
	sort($modList);	
	return $modList;
}

//--------------------------------------------------------------------------------------------------
//	list files of a particular extension (usually .act.php, .view.php, .block.php)
//--------------------------------------------------------------------------------------------------

function listFiles($path, $ext) {
	global $installPath;
	$fileList = array();

	$path = str_replace('%%installPath%%', $installPath, $path);
	$extLen = strlen($ext);
	$d = dir($path);

	while (false !== ($entry = $d->read())) {
	  	$entryLen = strlen($entry);
	  	if ( ($entryLen > ($extLen + 1)) AND
		     (substr($entry, $entryLen - $extLen) == $ext)) 
			{ $fileList[] = strtolower($entry); }
	}

	sort($fileList);
	return $fileList;
}

//--------------------------------------------------------------------------------------------------
//	wrapper functions for listFiles
//--------------------------------------------------------------------------------------------------

function listActions($module)	
	{ return listFiles('%%installPath%%modules/' . $module . '/actions/', '.act.php'); }

function listBlocks($module) 	
	{ return listFiles('%%installPath%%modules/' . $module . '/views/', '.block.php'); }

function listPages($module) 	
	{ return listFiles('%%installPath%%modules/' . $module . '/actions/', '.page.php'); }

function listTemplates($theme)	
	{ return listFiles('%%installPath%%themes/' . $theme . '/', '.template.php'); }

//--------------------------------------------------------------------------------------------------
//	get information from the server about the request the browser is making
//--------------------------------------------------------------------------------------------------

function getRequestParams() {
	global $defaultModule;
	$request = array();

	$request['raw'] = str_replace("/awarenet2/", "/", $_SERVER['REQUEST_URI']);	
	$request['parts'] = explode('/', substr($request['raw'], 1));
	$request['module'] = $defaultModule;
	$request['action'] = 'default';
	$request['ref'] = '';

	$request = getRequestArguments($request);
	$request = splitRequestURI($request);

	return $request;
}

//--------------------------------------------------------------------------------------------------
//	get arguments from request URL (refences to records, variables, switches, etc)
//	arguments have the form /var_value/ where value may contain underscores, variable names cannot.
//--------------------------------------------------------------------------------------------------

function getRequestArguments($request) {
	$request['args'] = array();
	$request['mvc'] = array();

	foreach($request['parts'] as $part) {
	  if (strlen($part) > 0) {			// non empty
	    if (strpos($part, '_') !== false) { 	// contains an underscore
	
		$bits = explode('_', $part);
		$varName = strtolower($bits[0]);
		unset($bits[0]);
		$request['args'][$varName] = implode('_', $bits);

	    } else { $request['mvc'][] = strtolower($part); }	// no underscore (module or action)
	  }	
	}

	return $request;
}

//--------------------------------------------------------------------------------------------------
//	break a browser request into controller, method, reference and param parts
//--------------------------------------------------------------------------------------------------

// no information is OK, *wrong* information makes 404
//
// (1) is the first part the name of a module?
//     (a) yes, set param['module'] 
//
//     (b) no, is the first part an action on the default module?
//     	   (i) yes, set param['module']
//         (ii) no, is the first part a valid reference

function splitRequestURI($request) {
	global $defaultModule;

	$modList = listModules();
	//echo routingMapToHtml();
	$parts = $request['mvc'];

	if (count($parts) > 0) { 
	 if (in_array($parts[0], $modList)) {
		//------------------------------------------------------------------------------------------
		// module is explicitly named, check next part against actions of that module
		//------------------------------------------------------------------------------------------
		$request['module'] = $parts[0];
		$actList = listActions($parts[0]);

		if (in_array($parts[1] . '.act.php', $actList)) {
			//--------------------------------------------------------------------------------------
			// action is explicitly named, check if there is a recordAlias or UID
			//--------------------------------------------------------------------------------------
			$request['action'] = $parts[1];

			if (count($parts) > 2) {
				//----------------------------------------------------------------------------------
				// reference is given (presumed recordAlias, UID, etc)
				//----------------------------------------------------------------------------------
				$request['ref'] = $parts[2]; 
			}

		} else {
			//--------------------------------------------------------------------------------------
			// action is not explicitly named, use default
			//--------------------------------------------------------------------------------------
			$request['ref'] = $parts[1];
		}

	  } else {
		//------------------------------------------------------------------------------------------
		// module is not explicitly named, check against actions on default module
		//------------------------------------------------------------------------------------------
		$actList = listActions($defaultModule);

		if (array_key_exists($parts[0], $actList)) {
			//--------------------------------------------------------------------------------------
			// method is explicitly named, any remaining part must be a reference to something
			//--------------------------------------------------------------------------------------
			$request['action'] = $parts[0];

			if (count($parts) > 1) {
				//----------------------------------------------------------------------------------
				// reference is given
				//----------------------------------------------------------------------------------
				$request['ref'] = $parts[1];
			}

		} else {
			//--------------------------------------------------------------------------------------
			// adduming default module, default action, request must be a reference
			//--------------------------------------------------------------------------------------
			$request['ref'] = $parts[0];

		}		
	  }
		
	} else {
		//------------------------------------------------------------------------------------------
		// nothing in URL
		//------------------------------------------------------------------------------------------
		
	}
	return $request;
}

//--------------------------------------------------------------------------------------------------
//	diagnostic
//--------------------------------------------------------------------------------------------------

function routingMapToHtml() {
	$routeMap = "<table noborder>\n";
	$routeMap .= "<tr><td>module</td><td>action</td><td>view</td><td>block</td></tr>";

	$modList = listModules();
	foreach($modList as $module) {
		$routeMap .= "\t<tr>\n";
		$routeMap .= "\t\t<td>$module</td>\n";
		$routeMap .= "\t\t<td>" . implode("<br/>\n", listActions($module)) . "</td>\n";
		$routeMap .= "\t\t<td>" . implode("<br/>\n", listViews($module)) . "</td>\n";
		$routeMap .= "\t\t<td>" . implode("<br/>\n", listBlocks($module)) . "</td>\n";
		$routeMap .= "\t</tr>";
	}

	$routeMap .= "</table>";
	return $routeMap;
}

?>
