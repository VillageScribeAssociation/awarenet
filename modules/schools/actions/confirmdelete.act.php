<?

//--------------------------------------------------------------------------------------------------
//	confirm deletion of a school
//--------------------------------------------------------------------------------------------------

	if (authHas('schools', 'edit', '') == false) { do403(); }
	if (array_key_exists('uid', $request['args']) == false) { do404(); }
	if (dbRecordExists('schools', sqlMarkup($request['args']['uid'])) == false) { do404(); }
	
	$thisRa = raGetDefault('schools', $request['args']['uid']);
	
	$labels = array('UID' => $request['args']['uid'], 'raUID' => $schoolRa);
	
	$html .= replaceLabels($labels, loadBlock('modules/schools/views/confirmdelete.block.php'));
	
	$_SESSION['sMessage'] .= $html;
	do302('schools/' . $thisRa);

?>
