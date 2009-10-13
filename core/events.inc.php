<?

//-------------------------------------------------------------------------------------------------
//	functions for processing events
//-------------------------------------------------------------------------------------------------

//-------------------------------------------------------------------------------------------------
//	sends and event to a specific module
//-------------------------------------------------------------------------------------------------

function eventSendSingle($module, $event, $args) {
	global $installPath;

	//---------------------------------------------------------------------------------------------
	//	check if there si an event handler for the module 
	//---------------------------------------------------------------------------------------------
	$cbFile = $installPath . 'modules/' . $module . '/events/' . $event . '.on.php'; 
	if (file_exists($cbFile) == false) { return false; }	
	include($cbFile);
	
	$cbFn = $module . "__cb_" . $event;
	if (function_exists($cbFn) == false) { return false; }				  // handles this event?
	return $cbFn($args);												  // do it
}

//-------------------------------------------------------------------------------------------------
//	sends an event to all modules
//-------------------------------------------------------------------------------------------------

function eventSendAll($event, $args) {
	$mods = listModules();
	foreach($mods as $module) { callbackSendSingle($module, $event, $args); }
}

?>
