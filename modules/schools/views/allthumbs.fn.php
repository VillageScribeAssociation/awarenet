<?

	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//|	all images of all schools (thumbnails, no arguments)
//--------------------------------------------------------------------------------------------------
//TODO: this should be on the images module

function schools_allthumbs($args) {
	global $db, $aliases, $user;
	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	//TODO: arguments and permissions

	//----------------------------------------------------------------------------------------------
	//	load images from database
	//----------------------------------------------------------------------------------------------
	$conditions = array("refModule='schools'");
	$range = $db->loadRange('images_image', '*', $conditions, 'weight');
	//$sql = "select * from Images_Image where refModule='schools' order by weight";

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------	
	if (0 == count($range)) { return ''; }

	foreach ($range as $row) {
		$thisRa = $aliases->getDefault('schools_school', $row['refUID']);
		$alt = str_replace('-', ' ', $thisRa);
		$html .= "<a href='%%serverPath%%schools/show/" . $thisRa . "'>" 
				. "<img src='/images/thumb90/" . $row['alias'] 
				. "' border='0' alt='" . $alt . "'></a> ";
	} 

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
