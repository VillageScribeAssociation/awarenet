<?

	require_once($installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//	display a set of images associated with something, along with some metadata
//--------------------------------------------------------------------------------------------------
// * $args['refModule'] = module to list on
// * $args['refUID'] = number of images per page

function images_imagesetdetail($args) {
	global $serverPath;
	
	//----------------------------------------------------------------------------------------------
	//	check args and authorisation
	//----------------------------------------------------------------------------------------------
	if (array_key_exists('refModule', $args) == false) { return false; }
	if (array_key_exists('refUID', $args) == false) { return false; }
	$authArgs = array('UID' => $args['refUID']);
	if (authHas($args['refModule'], 'images', $authArgs) == false) { return false; }

	//----------------------------------------------------------------------------------------------
	//	load the image records and make html
	//----------------------------------------------------------------------------------------------
	$sql = "select * from images where refModule='" . sqlMarkup($args['refModule']) 
	     . "' and refUID='" . sqlMarkup($args['refUID']) . "'";
	
	$result = dbQuery($sql);	

	$html = '';
	$block = loadBlock('modules/images/views/imagedetail.block.php');
	
	while($row = dbFetchAssoc($result)) {
		
		$i = new Image();
		$i->loadArray(sqlRMArray($row));
		
		$labels = $i->extArray();
		
		$labels['thumbUrl'] = $serverPath . 'images/thumb/' . $row['recordAlias'];
		$labels['editUrl'] = $serverPath .'images/edit/return_uploadmultiple/'. $row['recordAlias'];
		if (authHas($row['refModule'], 'images', '') == false) {
		  $labels['editUrl'] = $serverPath . 'images/viewset/return_uploadmultiple/' . $recordAlias;
		}
		
		$returnUrl = '/images/uploadmultiple/refModule_' . $i->data['refModule'] 
			   . '/refUID_' . $i->data['refUID'] . '/';
		
		$labels['deleteForm'] = "
		<form name='deleteImage' method='POST' action='/images/delete/' >
		<input type='hidden' name='action' value='deleteImage' />
		<input type='hidden' name='UID' value='" . $i->data['UID'] . "' />
		<input type='hidden' name='return' value='" . $returnUrl . "' />
		<input type='submit' value='delete' />
		</form>
		";
		
		$html .= replaceLabels($labels, $block);
		
	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>