<?

	require_once($installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show child galleries - currently unused in awareNet
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a gallery [string]

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

