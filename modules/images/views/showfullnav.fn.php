<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//|	small column of full-page display of an image + caption, etc
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of an image record [string]

function images_showfullnav($args) {
	global $db;

	global $theme;

	global $serverPath;

	//----------------------------------------------------------------------------------------------
	//	add the form
	//----------------------------------------------------------------------------------------------
	if (array_key_exists('raUID', $args) == false) { return false; }
	$i = new Images_Image($args['raUID']);
	if ($i->fileName == '') { return false; }
	
	//----------------------------------------------------------------------------------------------
	//	find related inages
	//----------------------------------------------------------------------------------------------
	
	$related = '';
	
	$sql = "select * from Images_Image where refModule='" . $i->refModule 
	     . "' order by refUID='" . $i->refUID . "' limit 20";
	
	$result = $db->query($sql);
	while ($row = $db->fetchAssoc($result)) {
	  if ($row['UID'] != $i->UID) {
		$row = $db->rmArray($row);
		$showUrl = $serverPath . 'images/show/' . $row['alias'];
		$thumbUrl = $serverPath . 'images/thumb90/' . $row['alias'];
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
	$html = $theme->replaceLabels($labels, $theme->loadBlock('modules/images/views/showfullnav.block.php'));	
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>