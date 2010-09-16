<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display a set of images associated with something
//--------------------------------------------------------------------------------------------------
//arg: refModule - module to list on [string]
//arg: refUID - UID of item which owns the images [string]

function images_imageset($args) {
	global $db, $user;
	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check args and authorisation
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $args)) { return '(no refModule)'; }
	if (false == array_key_exists('refUID', $args)) { return '(no refUID)'; }
	if (false == $user->authHas($args['refModule'], $args['refModel'], 'images-show', $args['refUID']))
		{ return ''; }

	//----------------------------------------------------------------------------------------------
	//	load the image records and make html
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "refModule='" . $db->addMarkup($args['refModule']) . "'";
	$conditions[] = "refUID='" . $db->addMarkup($args['refUID']) . "'";

	$range = $db->loadRange('Images_Image', '*', $conditions, 'weight');

	//$sql = "select * from Images_Image where refModule='" . $db->addMarkup($args['refModule']) 
	//    . "' and refUID='" . $db->addMarkup($args['refUID']) . "'";
	     
	foreach ($range as $row) {
		$imgUrl = '%%serverPath%%images/thumb/' . $row['alias'];
		$editURL = '%%serverPath%%images/edit/return_uploadmultiple/' . $row['alias'];
		if (false == $user->authHas($row['refModule'], $row['refModel'], 'images-edit', $row['refUID'])) 
			{ $editURL = $serverPath . 'images/viewset/return_uploadmultiple/' . $recordAlias; }
		
		$html .= "<a href='" . $editURL . "'>" 
			. "<img src='" . $imgUrl . "' border='0' /></a>\n";
		
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
