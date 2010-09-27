<?

	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all galleries belonging to a specified user	//TODO: fix this up
//--------------------------------------------------------------------------------------------------
//arg: userUID - user whose galleries to list [string]

function gallery_summarylistuser($args) {
	global $db;
	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('userUID', $args)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	load user's galleries from database
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "createdBy='" . $db->addMarkup($args['userUID']) . "'";
	$range = $db->loadRange('Gallery_Gallery', '*', $conditions, 'title DESC');

	//$sql = "select * from Gallery_Gallery "
	//	 . "where parent='root' and createdBy='" . $db->addMarkup($args['userUID']) . "' "
	//	 . "order by title DESC";

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	if (0 == count($range)) { return "(this user has not created any image galleries yet)<br/>\n"; }
	foreach ($range as $row) { $html .= "[[:gallery::summary::raUID=" . $row['UID'] . ":]]"; }	
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
