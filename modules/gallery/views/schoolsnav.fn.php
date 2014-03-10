<?

//--------------------------------------------------------------------------------------------------
//|	display a list of galleries at all schools
//--------------------------------------------------------------------------------------------------

function gallery_schoolsnav($args) {
	global $user;
	global $kapenta;
	global $theme;

	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------

	$sql = ''
	 . 'SELECT schoolUID, count(UID) AS numGalleries '
	 . 'FROM gallery_gallery group by schoolUID '
	 . 'ORDER BY numGalleries DESC';

	$result = $kapenta->db->query($sql);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$table = array();
	$table[] = array('School', '#');
	while ($row = $kapenta->db->fetchAssoc($result)) {
		$item = $kapenta->db->rmArray($row);
		$block = "[[:schools::name::link=yes::schoolUID=" . $item['schoolUID'] . ":]]";
		$table[] = array($block, $item['numGalleries']);
	}

	$html= $theme->arrayToHtmlTable($table, true, true);
	return $html;
}

?>
