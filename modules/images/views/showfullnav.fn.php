<?

	require_once($installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//|	small column of full-page display of an image + caption, etc
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of an image record [string]

function images_showfullnav($args) {
	global $serverPath;

	//----------------------------------------------------------------------------------------------
	//	add the form
	//----------------------------------------------------------------------------------------------
	if (array_key_exists('raUID', $args) == false) { return false; }
	$i = new Image($args['raUID']);
	if ($i->data['fileName'] == '') { return false; }
	
	//----------------------------------------------------------------------------------------------
	//	find related inages
	//----------------------------------------------------------------------------------------------
	
	$related = '';
	
	$sql = "select * from images where refModule='" . $i->data['refModule'] 
	     . "' order by refUID='" . $i->data['refUID'] . "' limit 20";
	
	$result = dbQuery($sql);
	while ($row = dbFetchAssoc($result)) {
	  if ($row['UID'] != $i->data['UID']) {
		$row = sqlRMArray($row);
		$showUrl = $serverPath . 'images/show/' . $row['recordAlias'];
		$thumbUrl = $serverPath . 'images/thumb90/' . $row['recordAlias'];
		$related .= "<a href='" . $showUrl . "'><img src='" . $thumbUrl 
			 . "' border='0' alt='" . $row['title'] . "' /></a>\n";
	  }
	}
	
	if ($related == '') { $related = '(no images are related to this one)'; }
	
	//----------------------------------------------------------------------------------------------
	//	mix and settle
	//----------------------------------------------------------------------------------------------
	$labels = $i->extArray();
	$labels['related'] = $related;
	$html = replaceLabels($labels, loadBlock('modules/images/views/showfullnav.block.php'));	
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

