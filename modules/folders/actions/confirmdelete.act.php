<?

//--------------------------------------------------------------------------------------------------
//	confirm deletion of a group
//--------------------------------------------------------------------------------------------------

	if (authHas('folder', 'edit', '') == false) { do403(); }
	if (array_key_exists('uid', $request['args']) == false) { do404(); }
	if (dbRecordExists('folder', sqlMarkup($request['args']['uid'])) == false) { do404(); }
	
	$thisRa = raGetDefault('folder', $request['args']['uid']);
	
	$labels = array('UID' => $request['args']['uid'], 'raUID' => $groupRa);
	
	$html .= replaceLabels($labels, loadBlock('modules/folder/confirmdelete.block.php'));
	
	$_SESSION['sMessage'] .= $html;
	do302('folder/' . $thisRa);

?>
