<?

//--------------------------------------------------------------------------------------------------
//*	create a table showing image metadata
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID fo an Images_image object [string]
//opt: imageUID - UID of an Images_Image object [string]
//opt: imgUID - UID of an Images_Image object [string]

function images_metadata($args) {
	global $kapenta;
	global $theme;
	global $user;

	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('UID', $args)) { $args['raUID'] = $args['UID']; }
	if (true == array_key_exists('imageUID', $args)) { $args['raUID'] = $args['imageUID']; }
	if (true == array_key_exists('imgUID', $args)) { $args['raUID'] = $args['imgUID']; }
	if (false == array_key_exists('raUID', $args)) { return '(no image specified)'; }

	$model = new Images_Image($args['raUID']);
	if (false == $model->loaded) { return '(image not found)'; }

	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$ext = $model->extArray();
	$table = array();
	$table[] = array('', '');		//	title row 

	$ownerBlock = ''
	 . '[[:' . $ext['refModule'] . '::linkobject::'
	 . 'type=' . $ext['refModel'] . '::'
	 . 'UID=' . $ext['refUID'] . ':]]';

	$ownerLink = $theme->expandBlocks($ownerBlock, '');
	if ('' == $ownerLink) { $table[] = array('Attached to:', $ext['refModule']); }
	else { $table[] = array('Attached to:', $ownerLink); }	

	$table[] = array('Added by:', '[[:users::namelink::userUID=' . $ext['createdBy'] . ':]]');
	$table[] = array('Added on:', $ext['createdOn']);	

	if ('' != trim($ext['attribName'])) {	
		if ('' != trim($ext['attribUrl'])) {
			$url = $ext['attribUrl'];
			$table[] = array('Source:', "<a href='" . $url . "'>" . $ext['attribName'] . '</a>'); 
		} else {
			$table[] = array('Source:', $ext['attribName']); 
		}
	}


	if ($kapenta->fileExists($ext['fileName'])) {
		$size = filesize($kapenta->installPath . $ext['fileName']);
		$sizeHtml = $size . 'bytes';
		if ($size > 1024) { $sizeHtml = (floor(($size * 10) / 1024) / 10) . ' kb'; }
		if ($size > 1048576) { $sizeHtml = (floor(($size * 10) / 1048576) / 10) . ' mb'; }
		$table[] = array('Size:', $sizeHtml); 
	}

	if ('admin' == $user->role) {
		$table[] = array('UID:', $ext['UID']);
	}

	$html = $theme->arrayToHtmlTable($table, true, true);

	return $html;
}

?>
