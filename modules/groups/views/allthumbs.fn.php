<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');
	require_once($kapenta->installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//|	all images of all groups (thumbnails, no arguments) TODO: school argument?
//--------------------------------------------------------------------------------------------------
//TODO: this should be moved to the images module

function groups_allthumbs($args) {
		global $db;
		global $user;
		global $aliases;

	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	load images from the database
	//----------------------------------------------------------------------------------------------
	$conditions = array("refModule='groups'");
	$range = $db->loadRange('images_image', '*', $conditions, 'weight');

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------	
	if (0 == count($range)) { return ''; }

	foreach ($range as $row) {
		$thisRa = $aliases->getDefault('groups_group', $row['refUID']);
		$alt = str_replace('-', ' ', $thisRa);
		$html .= "<a href='/groups/show/" . $thisRa . "'>" 
				. "<img src='/images/thumb90/" . $row['alias'] 
				. "' border='0' alt='" . $alt . "'></a> ";
	} 

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
