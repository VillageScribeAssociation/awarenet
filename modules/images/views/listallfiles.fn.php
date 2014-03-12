<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');
	require_once($kapenta->installPath . 'modules/images/inc/images__widthx.inc.php');

//--------------------------------------------------------------------------------------------------
//|	list all original image files known to image module
//--------------------------------------------------------------------------------------------------

function images_listallfiles($args) {
	global $kapenta;
 
	global $kapenta;
	if ('admin' != $kapenta->user->role) { return ''; }
	
	$list = '';

	$sql = "select fileName from images_image";
	$result = $kapenta->db->query($sql);
	while ($row = $kapenta->db->fetchAssoc($result)) { $list .= sqlRemoveMarkup($row['fileName']). "\n"; }
	return $list;
}

//--------------------------------------------------------------------------------------------------

?>