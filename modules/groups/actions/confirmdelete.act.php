<?

//--------------------------------------------------------------------------------------------------
//	confirm deletion of a group
//--------------------------------------------------------------------------------------------------

	if (authHas('groups', 'edit', '') == false) { do403(); }
	if (array_key_exists('uid', $request['args']) == false) { do404(); }
	if (dbRecordExists('groups', sqlMarkup($request['args']['uid'])) == false) { do404(); }
	
	$thisRa = raGetDefault('groups', $request['args']['uid']);
	
	$labels = array('UID' => $request['args']['uid'], 'raUID' => $groupRa);
	
	$html .= replaceLabels($labels, loadBlock('modules/groups/views/confirmdelete.block.php'));
	
	$_SESSION['sMessage'] .= $html;
	do302('groups/' . $thisRa);

?>
