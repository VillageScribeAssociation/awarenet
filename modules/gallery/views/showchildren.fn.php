<?

	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show child galleries - currently unused in awareNet
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a gallery [string]

function gallery_showchildren($args) {
	global $db;
	return '';		// nested galleries not enabled on awareNet

	$html = "<h2>Read More</h2>\n";

	if (false == array_key_exists('UID', $args)) { return ''; }

	$sql = "select * from gallery_gallery where parent='" . $args['UID'] . "' order by title DESC";	
	$result = $db->query($sql);

	if ($db->numRows($result) > 0) {
		while ($row = $db->fetchAssoc($result)) 
			{ $html .= "[[:gallery::summary::pageUID=" . $row['UID'] . ":]]"; }	

	} else { $html = "<br/>\n"; }

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
