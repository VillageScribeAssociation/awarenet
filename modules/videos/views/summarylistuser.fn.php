<?

	require_once($kapenta->installPath . 'modules/videos/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all galleries belonging to a specified user	//TODO: fix this up
//--------------------------------------------------------------------------------------------------
//arg: userUID - user whose galleries to list [string]

function videos_summarylistuser($args) {
	global $kapenta;
	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('userUID', $args)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	load user's galleries from database
	//----------------------------------------------------------------------------------------------
	$conditions = array("createdBy='" . $kapenta->db->addMarkup($args['userUID']) . "'");
	$range = $kapenta->db->loadRange('videos_gallery', '*', $conditions, 'title DESC');

	//$sql = "select * from Videos_Gallery "
	//	 . "where parent='root' and createdBy='" . $kapenta->db->addMarkup($args['userUID']) . "' "
	//	 . "order by title DESC";

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	if (0 == count($range)) { return "(this user has not created any video galleries yet)<br/>\n"; }
	foreach ($range as $row) { $html .= "[[:videos::summary::raUID=" . $row['UID'] . ":]]"; }	
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
