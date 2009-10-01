<?

	require_once($installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//	list all galleries belonging to a specified user
//--------------------------------------------------------------------------------------------------
// * $args['userUID'] = user whose galleries to list

function gallery_summarylist($args) {
	if (array_key_exists('userUID', $args) == false) { return false; }
	$html = '';

	$sql = "select * from gallery "
		 . "where parent='root' and createdBy='" . sqlMarkup($args['userUID']) . "' "
		 . "order by title DESC";

	$result = dbQuery($sql);
	if (dbNumRows($result) > 0) {
		while ($row = dbFetchAssoc($result)) 
			{ $html .= "[[:gallery::summary::raUID=" . $row['UID'] . ":]]"; }	

	} else { $html = "(this user has not created any image galleries yet)<br/>\n"; }

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>