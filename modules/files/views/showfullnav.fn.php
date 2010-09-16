<?

	require_once($kapenta->installPath . 'modules/files/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//|	full-page display of an file + caption, etc (TODO: see if this can be removed)
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of an object [string]

function files_showfullnav($args) {
	global $db, $theme;
	$html = '';		

	//----------------------------------------------------------------------------------------------
	//	add the form
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('raUID', $args)) { return ''; }
	$model = new Files_File($args['raUID']);
	if (false == $model->loaded) { return ''; }
	//TODO: permissions check here	

	//----------------------------------------------------------------------------------------------
	//	find related files
	//----------------------------------------------------------------------------------------------
	$related = '';
	
	$sql = "select * from Files_File where refModule='" . $model->refModule 
	     . "' order by refUID='" . $model->refUID . "' limit 20";
	
	$result = $db->query($sql);
	while ($row = $db->fetchAssoc($result)) {
	  if ($row['UID'] != $model->UID) {
		$showUrl = '%%serverPathfiles/show/' . $row['alias'];
		$thumbUrl = '%%serverPath%%files/thumb90/' . $row['alias'];
		$related .= "<a href='" . $showUrl . "'><img src='" . $thumbUrl 
			 . "' border='0' alt='" . $row['title'] . "' /></a>\n";
	  }
	}
	
	if ($related == '') { $related = '(no files are related to this one)'; }
	
	//----------------------------------------------------------------------------------------------
	//	mix and settle
	//----------------------------------------------------------------------------------------------
	$labels = $model->extArray();
	$labels['related'] = $related;
	$block = $theme->loadBlock('modules/files/showfullnav.block.php');
	$html = $theme->replaceLabels($labels, $block);	
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
