<?

//--------------------------------------------------------------------------------------------------------------
//	confirm deletion of a static page
//--------------------------------------------------------------------------------------------------------------

	if (authHas('static', 'edit', '') == false) { do403(); }
	if ($request['ref'] == '') {do302('static/list/'); }
	
	$recordUID = raGetOwner($request['ref'], 'static');
	if ($recordUID == false) { do404(); }

	$labels = array('UID' => $recordUID, 'recordAlias' => $request['ref']);
	$_SESSION['sMessage'] .= replaceLabels($labels, loadBlock('modules/static/views/confirmdelete.block.php'));
	
	do302('static/' . $request['ref']);
	
?>