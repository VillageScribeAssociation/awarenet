<?

//--------------------------------------------------------------------------------------------------
//	confirm deletion of a group
//--------------------------------------------------------------------------------------------------

	if (authHas('gallery', 'edit', '') == false) { do403(); }
	if (array_key_exists('uid', $request['args']) == false) { do404(); }
	if (dbRecordExists('gallery', sqlMarkup($request['args']['uid'])) == false) { do404(); }
	
	$thisRa = raGetDefault('gallery', $request['args']['uid']);
	
	$labels = array('UID' => $request['args']['uid'], 'raUID' => $groupRa);
	
	$html .= replaceLabels($labels, loadBlock('modules/gallery/views/confirmdelete.block.php'));
	
	$_SESSION['sMessage'] .= $html;
	do302('gallery/' . $thisRa);

?>
