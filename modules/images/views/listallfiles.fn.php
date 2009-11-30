<?

	require_once($installPath . 'modules/images/models/image.mod.php');
	require_once($installPath . 'modules/images/inc/images__widthx.inc.php');

//--------------------------------------------------------------------------------------------------
//	display a single image scaled to fit the slideshow
//--------------------------------------------------------------------------------------------------
//ofgrup: admin

function images_listallfiles($args) { 
	global $user;
	if ($user->data['ofGroup'] != 'admin') { return ''; }
	
	$list = '';

	$sql = "select fileName from images";
	$result = dbQuery($sql);
	while ($row = dbFetchAssoc($result)) { $list .= sqlRemoveMarkup($row['fileName']). "\n"; }
	return $list;
}

//--------------------------------------------------------------------------------------------------

?>
