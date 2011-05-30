<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');
	require_once($kapenta->installPath . 'modules/images/inc/images__widthx.inc.php');

//--------------------------------------------------------------------------------------------------
//|	list all original image files known to image module
//--------------------------------------------------------------------------------------------------

function images_listallfiles($args) {
	global $db;
 
	global $user;
	if ('admin' != $user->role) { return ''; }
	
	$list = '';

	$sql = "select fileName from images_image";
	$result = $db->query($sql);
	while ($row = $db->fetchAssoc($result)) { $list .= sqlRemoveMarkup($row['fileName']). "\n"; }
	return $list;
}

//--------------------------------------------------------------------------------------------------

?>