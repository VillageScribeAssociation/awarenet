<?

//-------------------------------------------------------------------------------------------------
//*	functions for processing events
//-------------------------------------------------------------------------------------------------
//+	Events are mostly raised by modules and are an important way for modules to signal one another.
//+ Arguments for an event (args array) is fairly loose at this point

//-------------------------------------------------------------------------------------------------
//|	sends and event to a specific module
//-------------------------------------------------------------------------------------------------
//arg: module - name of module [string]
//arg: event - event name [string]
//arg: args - details of event [array]
//: returns whatever the event handler does, unused at present

function eventSendSingle($module, $event, $args) {
	global $installPath;
	//TODO: this has been moved to $kapenta, check all events use new object
	//---------------------------------------------------------------------------------------------
	//	check if there is an event handler for the module 
	//---------------------------------------------------------------------------------------------
	$cbFile = $installPath . 'modules/' . $module . '/events/' . $event . '.on.php'; 
	if (file_exists($cbFile) == false) { return false; }	
	require_once($cbFile);
	
	$cbFn = $module . "__cb_" . $event;
	if (function_exists($cbFn) == false) { return false; }				  // handles this event?
	return $cbFn($args);												  // then do it
}

//-------------------------------------------------------------------------------------------------
//|	sends an event to all modules
//-------------------------------------------------------------------------------------------------
//arg: event - event name [string]
//arg: args - details of event [array]
//: may in the future return array of whatever module event handers return, if need arises

function eventSendAll($event, $args) {
	//TODO: this has been moved to $kapenta, check all events use new object
	$mods = listModules();
	foreach($mods as $module) { eventSendSingle($module, $event, $args); }
}

?>
