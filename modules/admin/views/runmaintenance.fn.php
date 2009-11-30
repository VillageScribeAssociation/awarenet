<?

//-------------------------------------------------------------------------------------------------
//	run a maintenance script and return the report (as html)
//-------------------------------------------------------------------------------------------------
//ofgroup: admin

function admin_runmaintenance($args) {
	global $user;
	global $installPath;
	global $serverPath;

	$maint = array();
	$count = 0;
	$mods = listModules();

	if ($user->data['ofGroup'] != 'admin') { return ''; }
	if (array_key_exists('modName', $args) == false) { return ''; }
	if (in_array($args['modName'], $mods) == false) { return "(no such module)"; }

	//---------------------------------------------------------------------------------------------
	//	include the maintenance script and check that maintenance function exists
	//---------------------------------------------------------------------------------------------
	$fileName = $installPath . 'modules/' . $args['modName'] . '/inc/maintenance.inc.php';
	if (file_exists($fileName) == false) { return '(no such maintenance script)'; }
	require_once($fileName);
	
	$fnName = 'maintenance_' . $args['modName'];
	if (function_exists($fnName) == false) { return '(invalid maintenance script)'; }

	$report = $fnName();

	return $report;
}

?>
