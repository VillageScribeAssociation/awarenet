<?

//--------------------------------------------------------------------------------------------------
//*	functions used for routing
//--------------------------------------------------------------------------------------------------

//+	Example requests:
//+
//+	http://somewhere.com/module/action/reference/argument1_value/argument2_value
//+	|xx ignored xxxxxxxx| 
//+
//+	http://somewhere.com/module/action/argument_value/reference
//+	|xx ignored xxxxxxxx| 
//+
//+	The defining property of arguments is that they contain an underscore
//+	Everything except argument values are converted to lower case (INCLUDING RECORDALIASES)
//+	An argument value can contain underscores

//--------------------------------------------------------------------------------------------------
//|	list modules (installed or otherwise)
//--------------------------------------------------------------------------------------------------
//:	Note that static data should be used on production sites for a slight performance increase.
//returns: array of module names [array]

function listModules() {
	global $installPath;
	$modList = array();

	$d = dir($installPath . 'modules/');
	while (false !== ($entry = $d->read())) {
	  if (($entry != '.') AND ($entry != '..') AND ($entry != '.svn')) {
		$entry = strtolower($entry);
		$modList[] = $entry;
	  }
	}
	
	sort($modList);	
	return $modList;
}

//--------------------------------------------------------------------------------------------------
//|	list files with a given extension 
//--------------------------------------------------------------------------------------------------
//arg: path - full path of directory to search
//arg: ext - file extension, eg, usually .act.php, .view.php, .block.php
//returns: array of file names (base, not full path) [array]

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
//|	list all actions a module can perform (~urls)
//--------------------------------------------------------------------------------------------------
//arg: module - name of a kapenta module [string]
//returns: array of action file names (base, not full path) [array]

function listActions($module)
	{ return listFiles('%%installPath%%modules/' . $module . '/actions/', '.act.php'); }

//--------------------------------------------------------------------------------------------------
//|	list all models a module contains
//--------------------------------------------------------------------------------------------------
//arg: module - name of a kapenta module [string]
//returns: array of model names (base, not full path) [array]

function listModels($module)
	{ return listFiles('%%installPath%%modules/' . $module . '/models/', '.mod.php'); }

//--------------------------------------------------------------------------------------------------
//|	list all block templates in a module
//--------------------------------------------------------------------------------------------------
//arg: module - name of a kapenta module [string]
//returns: array of block template file names (base, not full path) [array]

function listBlocks($module) 
	{ return listFiles('%%installPath%%modules/' . $module . '/views/', '.block.php'); }

//--------------------------------------------------------------------------------------------------
//|	list all views a module exposes
//--------------------------------------------------------------------------------------------------
//arg: module - name of a kapenta module [string]
//returns: array of view function file names (base, not full path) [array]

function listViews($module) 
	{ return listFiles('%%installPath%%modules/' . $module . '/views/', '.fn.php'); }

//--------------------------------------------------------------------------------------------------
//|	list all pages on a module
//--------------------------------------------------------------------------------------------------
//arg: module - name of a kapenta module [string]
//returns: array of page template file names (base, not full path) [array]

function listPages($module) 
	{ return listFiles('%%installPath%%modules/' . $module . '/actions/', '.page.php'); }

//--------------------------------------------------------------------------------------------------
//|	list all includes prodiced by a module
//--------------------------------------------------------------------------------------------------
//arg: module - name of a kapenta module [string]
//returns: array of page template file names (base, not full path) [array]

function listIncs($module) 
	{ return listFiles('%%installPath%%modules/' . $module . '/inc/', '.inc.php'); }


//--------------------------------------------------------------------------------------------------
//|	list all page templates in a teme
//--------------------------------------------------------------------------------------------------
//arg: theme - name of a kapenta theme [string]
//returns: array of template files (base, not full path) [array]

function listTemplates($theme)	
	{ return listFiles('%%installPath%%themes/' . $theme . '/', '.template.php'); }

//--------------------------------------------------------------------------------------------------
//|	get information from the server about the request the browser is making
//--------------------------------------------------------------------------------------------------
//returns: array representing request made by browser, server metadata and kapenta metadata
//: TODO - better handling of URLS outside of document root

function getRequestParams() {
        global $defaultModule;
        $request = array();

        $request['raw'] = $_SERVER['REQUEST_URI'];

        if (substr($request['raw'], 0, 9) == 'awarenet/')
                { $request['raw'] = '/' . substr($request['raw'], 9); }

        if (substr($request['raw'], 0, 10) == '/awarenet/')
                { $request['raw'] = '/' . substr($request['raw'], 10); }

        //echo $request['raw'] . "<br/>\n"; flush();

        $request['parts'] = explode('/', substr($request['raw'], 1));
        $request['module'] = $defaultModule;
        $request['action'] = 'default';
        $request['ref'] = '';

        $request = getRequestArguments($request);
        $request = splitRequestURI($request);

        return $request;
}

//--------------------------------------------------------------------------------------------------
//|	get arguments from request URL (refences to records, variables, switches, etc)
//--------------------------------------------------------------------------------------------------
//:	arguments have the form /var_value/ where value may contain underscores, variable names cannot.
//arg: request - the request array as created by getRequestParams() [array]
//returns: request array with action arguments added [array]

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
//|	break a browser request into controller, method, reference and param parts
//--------------------------------------------------------------------------------------------------
//arg: request - browser request information [array]
//returns: request array with module, action and ref added  [array]

//:	no information is OK, *wrong* information makes 404
//:
//:	(1) is the first part the name of a module?
//:		(a) yes, set param['module'] 
//:
//:		(b) no, is the first part an action on the default module?
//:			(i) yes, set param['module']
//:			(ii) no, is the first part a valid reference


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
			// assuming default module, default action, request must be a reference
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
//|	diagnostic TODO - use arrayToHtmlTable
//--------------------------------------------------------------------------------------------------
//returns: html table [string]

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
