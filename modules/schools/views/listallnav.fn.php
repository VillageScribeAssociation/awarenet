<?

	require_once($installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all formatted for nav (300 px wide)
//--------------------------------------------------------------------------------------------------

function schools_listallnav($args) {
	$sql = "select * from schools order by name";
	$result = dbQuery($sql);
	$html = '';
	while ($row = dbFetchAssoc($result)) {
		$html .= "[[:schools::summarynav::schoolUID=" . $row['UID'] . ":]]";
	}
	return $html;
}


//--------------------------------------------------------------------------------------------------

?>

