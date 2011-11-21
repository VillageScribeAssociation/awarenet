<?

//-------------------------------------------------------------------------------------------------
//|	run a maintenance script and return the report (as html)
//-------------------------------------------------------------------------------------------------
//arg: modName - name of a kapenta module [string]
//role: admin - only administrators may use this
//TODO: move this to the core, probably on the $kapenta object

function admin_runmaintenance($args) {
	global $user, $kapenta;
	$report = '';		//% return value [string]
	$maint = array();
	$count = 0;
	$mods = $kapenta->listModules();

	//---------------------------------------------------------------------------------------------
	//	check user role and argument
	//---------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }
	if (array_key_exists('modName', $args) == false) { return ''; }
	if (in_array($args['modName'], $mods) == false) { return "(no such module)"; }

	//---------------------------------------------------------------------------------------------
	//	include the maintenance script and check that maintenance function exists
	//---------------------------------------------------------------------------------------------
	$fileName = 'modules/' . $args['modName'] . '/inc/maintenance.inc.php';
	if (false == $kapenta->fileExists($fileName)) {
		return '(no such maintenance script: ' . $fileName . ')';
	}

	require_once($kapenta->installPath . $fileName);
	
	$fnName = $args['modName'] . '_maintenance';
	if (function_exists($fnName) == false) { return '(invalid maintenance script: ' . $fnName . ')'; }

	$report = $fnName();

	return $report;
}

?>

