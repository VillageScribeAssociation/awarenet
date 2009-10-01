<?

//--------------------------------------------------------------------------------------------------
//	confirm deletion of a group
//--------------------------------------------------------------------------------------------------

	if (authHas('announcements', 'edit', '') == false) { do403(); }
	if (array_key_exists('uid', $request['args']) == false) { do404(); }
	if (dbRecordExists('announcements', sqlMarkup($request['args']['uid'])) == false) { do404(); }
	
	$thisRa = raGetDefault('announcements', $request['args']['uid']);
	
	$labels = array('UID' => $request['args']['uid'], 'raUID' => $groupRa);
	
	$html .= replaceLabels($labels, loadBlock('modules/announcements/views/confirmdelete.block.php'));
	
	$_SESSION['sMessage'] .= $html;
	do302('announcements/' . $thisRa);

?>
