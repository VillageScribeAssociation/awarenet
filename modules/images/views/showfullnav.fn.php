<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//|	small column of full-page display of an image + caption, etc
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of an image record [string]

function images_showfullnav($args) {
		global $kapenta;
		global $kapenta;
		global $theme;
		global $user;


	//----------------------------------------------------------------------------------------------
	//	add the form
	//----------------------------------------------------------------------------------------------
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new Images_Image($args['raUID']);
	if ('' == $model->fileName) { return false; }
	
	//----------------------------------------------------------------------------------------------
	//	find related images
	//----------------------------------------------------------------------------------------------
	
	$related = '';
	
	$sql = "select * from images_image where refModule='" . $model->refModule 
	     . "' order by refUID='" . $model->refUID . "' limit 20";
	
	$result = $kapenta->db->query($sql);
	while ($row = $kapenta->db->fetchAssoc($result)) {
	  if ($row['UID'] != $model->UID) {
		$row = $kapenta->db->rmArray($row);
		$showUrl = '%%serverPath%%images/show/' . $row['alias'];
		$thumbUrl = '%%serverPath%%images/thumb90/' . $row['alias'];
		$related .= "<a href='" . $showUrl . "'><img src='" . $thumbUrl 
			 . "' border='0' alt='" . $row['title'] . "' /></a>\n";
	  }
	}
	
	if ('' == $related) { $related = '(no images are related to this one)'; }
	
	//----------------------------------------------------------------------------------------------
	//	mix and settle
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/images/views/showfullnav.block.php');
	$labels = $model->extArray();
	$labels['related'] = $related;
	$html = $theme->replaceLabels($labels, $block);	
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
