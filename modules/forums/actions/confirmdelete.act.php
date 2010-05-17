<?

//--------------------------------------------------------------------------------------------------
//	confirm deletion of a forum
//--------------------------------------------------------------------------------------------------

	if (authHas('forums', 'edit', '') == false) { do403(); }
	if (array_key_exists('uid', $request['args']) == false) { do404(); }
	if (dbRecordExists('forums', sqlMarkup($request['args']['uid'])) == false) { do404(); }
	
	$thisRa = raGetDefault('forums', $request['args']['uid']);
	
	$labels = array('UID' => $request['args']['uid'], 'raUID' => $groupRa);
	
	$html .= replaceLabels($labels, loadBlock('modules/forums/views/confirmdelete.block.php'));
	
	$_SESSION['sMessage'] .= $html;
	do302('forums/' . $thisRa);

?>
