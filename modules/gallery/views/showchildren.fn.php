<?

	require_once($installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//	show child pages
//--------------------------------------------------------------------------------------------------
// * $args['UID'] = UID or a page

function gallery_showchildren($args) {
	if (array_key_exists('UID', $args) == false) { return false; }
	global $serverPath;
	$html = "<h2>Read More</h2>\n";

	$sql = "select * from gallery where parent='" . $args['UID'] . "' order by title DESC";	
	$result = dbQuery($sql);
	if (dbNumRows($result) > 0) {
		while ($row = dbFetchAssoc($result)) 
			{ $html .= "[[:gallery::summary::pageUID=" . $row['UID'] . ":]]"; }	

	} else { $html = "<br/>\n"; }

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>