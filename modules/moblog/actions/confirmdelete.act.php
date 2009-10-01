<?

//--------------------------------------------------------------------------------------------------
//	confirm deletion of a group
//--------------------------------------------------------------------------------------------------

	if (authHas('moblog', 'edit', '') == false) { do403(); }
	if (array_key_exists('uid', $request['args']) == false) { do404(); }
	if (dbRecordExists('moblog', sqlMarkup($request['args']['uid'])) == false) { do404(); }
	
	$thisRa = raGetDefault('moblog', $request['args']['uid']);
	
	$labels = array('UID' => $request['args']['uid'], 'raUID' => $thisRa);
	
	$html .= replaceLabels($labels, loadBlock('modules/moblog/views/confirmdelete.block.php'));
	
	$_SESSION['sMessage'] .= $html;
	do302('moblog/' . $thisRa);

?>
