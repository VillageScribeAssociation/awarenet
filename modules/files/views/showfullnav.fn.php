<?

	require_once($installPath . 'modules/files/models/files.mod.php');

//--------------------------------------------------------------------------------------------------
//	full-page display of an file + caption, etc
//--------------------------------------------------------------------------------------------------
// * $args['raUID'] = recordAlias or UID of record

function files_showfullnav($args) {
	global $serverPath;

	//----------------------------------------------------------------------------------------------
	//	add the form
	//----------------------------------------------------------------------------------------------
	if (array_key_exists('raUID', $args) == false) { return false; }
	$i = new file($args['raUID']);
	if ($i->data['fileName'] == '') { return false; }
	
	//----------------------------------------------------------------------------------------------
	//	find related inages
	//----------------------------------------------------------------------------------------------
	
	$related = '';
	
	$sql = "select * from files where refModule='" . $i->data['refModule'] 
	     . "' order by refUID='" . $i->data['refUID'] . "' limit 20";
	
	$result = dbQuery($sql);
	while ($row = dbFetchAssoc($result)) {
	  if ($row['UID'] != $i->data['UID']) {
		$showUrl = $serverPath . 'files/show/' . $row['recordAlias'];
		$thumbUrl = $serverPath . 'files/thumb90/' . $row['recordAlias'];
		$related .= "<a href='" . $showUrl . "'><img src='" . $thumbUrl 
			 . "' border='0' alt='" . $row['title'] . "' /></a>\n";
	  }
	}
	
	if ($related == '') { $related = '(no files are related to this one)'; }
	
	//----------------------------------------------------------------------------------------------
	//	mix and settle
	//----------------------------------------------------------------------------------------------
	$labels = $i->extArray();
	$labels['related'] = $related;
	$html = replaceLabels($labels, loadBlock('modules/files/showfullnav.block.php'));	
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>