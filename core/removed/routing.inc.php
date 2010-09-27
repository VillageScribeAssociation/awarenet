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
	global $kapenta, $session;
	$session->msg('deprecated: listModules => $kapenta->listModules()', 'bug');
	$modList = $kapenta->listModules();
	return $modList;
}

//--------------------------------------------------------------------------------------------------
//|	list files with a given extension 
//--------------------------------------------------------------------------------------------------
//arg: path - full path of directory to search
//arg: ext - file extension, eg, usually .act.php, .view.php, .block.php
//returns: array of file names (base, not full path) [array]

function listFiles($path, $ext) {
	global $kapenta, $session;
	$session->msg('deprecated: listFiles => $kapenta->listFiles()', 'bug');
	$fileList = $kapenta->listFiles($path, $ext);
	return $fileList;
}

//--------------------------------------------------------------------------------------------------
//|	list all actions a module can perform (~urls)
//--------------------------------------------------------------------------------------------------
//arg: module - name of a kapenta module [string]
//returns: array of action file names (base, not full path) [array]

function listActions($module) { 
	global $kapenta, $session;
	$session->msg('deprecated: listActions => $kapenta->listActions()', 'bug');
	$actList = $kapenta->listActions($module);
	return $actList;
}

//--------------------------------------------------------------------------------------------------
//|	list all models a module contains
//--------------------------------------------------------------------------------------------------
//arg: module - name of a kapenta module [string]
//returns: array of model names (base, not full path) [array]

function listModels($module) {
	global $kapenta, $session;
	$session->msg('deprecated: listModels => $kapenta->listModels()', 'bug');
	$modList = $kapenta->listModels($module);
	return $modList;
}

//--------------------------------------------------------------------------------------------------
//|	list all block templates in a module
//--------------------------------------------------------------------------------------------------
//arg: module - name of a kapenta module [string]
//returns: array of block template file names (base, not full path) [array]

function listBlocks($module) { 
	global $kapenta, $session;
	$session->msg('deprecated: listBlocks => $kapenta->listBlocks()', 'bug');
	$blockList = $kapenta->listBlocks($module);
	return $blockList;
}

//--------------------------------------------------------------------------------------------------
//|	list all views a module exposes
//--------------------------------------------------------------------------------------------------
//arg: module - name of a kapenta module [string]
//returns: array of view function file names (base, not full path) [array]

function listViews($module) { 
	global $kapenta, $session;
	$session->msg('deprecated: listViews => $kapenta->listVeiws()', 'bug');
	$viewList = $kapenta->listViews($module);
	return $viewList;
}

//--------------------------------------------------------------------------------------------------
//|	list all pages on a module
//--------------------------------------------------------------------------------------------------
//arg: module - name of a kapenta module [string]
//returns: array of page template file names (base, not full path) [array]

function listPages($module) {
	global $kapenta, $session;
	$session->msg('deprecated: listPages => $kapenta->listPages()', 'bug');
	$pageList = $kapenta->listPages($module);
	return $pageList;
}

//--------------------------------------------------------------------------------------------------
//|	list all includes prodiced by a module
//--------------------------------------------------------------------------------------------------
//arg: module - name of a kapenta module [string]
//returns: array of page template file names (base, not full path) [array]
/*
function listIncs($module) { 
	global $kapenta, $session;
	$session->msg('deprecated: listIncs => $kapenta->listIncs()', 'bug');
	$incList = $kapenta->listIncs($module);
	return $icnList;
}
*/


//--------------------------------------------------------------------------------------------------
//|	list all page templates in a teme
//--------------------------------------------------------------------------------------------------
//arg: theme - name of a kapenta theme [string]
//returns: array of template files (base, not full path) [array]

function listTemplates($theme) {
	global $kapenta, $session;
	$session->msg('deprecated: listTemplates(...) => $kapenta->listTemplates(...)', 'bug');
	$tList = $kapenta->listTemplates($theme);
	return $tList;
}

//--------------------------------------------------------------------------------------------------
//|	get information from the server about the request the browser is making
//--------------------------------------------------------------------------------------------------
//returns: array representing request made by browser, server metadata and kapenta metadata
//: TODO - better handling of URLS outside of document root

function getRequestParams() {
	//removed in version 4
}

//--------------------------------------------------------------------------------------------------
//|	get arguments from request URL (refences to records, variables, switches, etc)
//--------------------------------------------------------------------------------------------------
//:	arguments have the form /var_value/ where value may contain underscores, variable names cannot.
//arg: request - the request array as created by getRequestParams() [array]
//returns: request array with action arguments added [array]

function getRequestArguments($request) {
	//removed in version 4
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
	//removed in version 4
}


?>
