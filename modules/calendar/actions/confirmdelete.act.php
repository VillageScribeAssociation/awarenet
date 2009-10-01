<?

//--------------------------------------------------------------------------------------------------
//	confirm deletion of a coin
//--------------------------------------------------------------------------------------------------

	if (authHas('calendar', 'edit', '') == false) { do403(); }
	if (array_key_exists('uid', $request['args']) == false) { do404(); }
	if (dbRecordExists('calendar', sqlMarkup($request['args']['uid'])) == false) { do404(); }
	
	$coinRa = raGetDefault('calendar', $request['args']['uid']);
	
	$labels = array('UID' => $request['args']['uid'], 'raUID' => $coinRa);
	
	$html .= replaceLabels($labels, loadBlock('modules/calendar/views/confirmdelete.block.php'));
	
	$_SESSION['sMessage'] .= $html;
	do302('calendar/' . $coinRa);

?>
